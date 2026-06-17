<?php
namespace AccountingManagementApi\Controllers;

use AccountingManagementApi\Repositories\VendorRepository;
use AccountingManagementApi\Services\AuthService;
use WP_REST_Request;

class VendorController extends BaseController {
    private $vendorRepository;

    public function __construct() {
        $this->vendorRepository = new VendorRepository();
    }

    /**
     * GET /vendors
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'vendor_code', 'vendor_name', 'created_at'];
        $search_fields = ['vendor_code', 'vendor_name', 'mobile', 'email', 'gst_number', 'state'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->vendorRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Vendors retrieved successfully.', $results);
    }

    /**
     * GET /vendors/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $vendor = $this->vendorRepository->findById($id);

        if (!$vendor) {
            return $this->error('Vendor not found.', [], 404);
        }

        return $this->success('Vendor retrieved successfully.', $vendor);
    }

    /**
     * POST /vendors
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['vendor_name'])) {
            return $this->error('Validation failed: vendor_name is required.');
        }

        // Generate vendor code
        $vendor_code = 'VEND-ACC-' . sprintf('%04d', rand(1000, 9999));
        while ($this->vendorRepository->existsVendorCode($vendor_code)) {
            $vendor_code = 'VEND-ACC-' . sprintf('%04d', rand(1000, 9999));
        }

        $data = [
            'vendor_code' => $vendor_code,
            'vendor_name' => sanitize_text_field($params['vendor_name']),
            'mobile' => sanitize_text_field($params['mobile'] ?? ''),
            'email' => sanitize_email($params['email'] ?? ''),
            'address' => sanitize_textarea_field($params['address'] ?? ''),
            'gst_number' => sanitize_text_field($params['gst_number'] ?? ''),
            'state' => sanitize_text_field($params['state'] ?? ''),
            'outstanding_amount' => floatval($params['outstanding_amount'] ?? 0.00),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s'];
        $inserted_id = $this->vendorRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create vendor.');
        }

        AuthService::logActivity(get_current_user_id(), 'VENDOR_CREATE', "Created vendor profile $vendor_code ($inserted_id)");

        return $this->success('Vendor profile created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /vendors/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $vendor = $this->vendorRepository->findById($id);

        if (!$vendor) {
            return $this->error('Vendor not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = [
            'vendor_name' => '%s',
            'mobile' => '%s',
            'email' => '%s',
            'address' => '%s',
            'gst_number' => '%s',
            'state' => '%s',
            'outstanding_amount' => '%f',
            'status' => '%s'
        ];

        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                if ($field === 'email') {
                    $data[$field] = sanitize_email($params[$field]);
                } elseif ($field === 'address') {
                    $data[$field] = sanitize_textarea_field($params[$field]);
                } elseif ($format === '%f') {
                    $data[$field] = floatval($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->vendorRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update vendor details.');
        }

        AuthService::logActivity(get_current_user_id(), 'VENDOR_UPDATE', "Updated vendor ID: $id");

        return $this->success('Vendor updated successfully.', $this->vendorRepository->findById($id));
    }

    /**
     * DELETE /vendors/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $vendor = $this->vendorRepository->findById($id);

        if (!$vendor) {
            return $this->error('Vendor not found.', [], 404);
        }

        $deleted = $this->vendorRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete vendor.');
        }

        AuthService::logActivity(get_current_user_id(), 'VENDOR_DELETE', "Soft deleted vendor ID: $id ($vendor[vendor_code])");

        return $this->success('Vendor deleted successfully.');
    }
}
