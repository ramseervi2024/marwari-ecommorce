<?php
namespace AccountingManagementApi\Controllers;

use AccountingManagementApi\Repositories\CustomerRepository;
use AccountingManagementApi\Services\AuthService;
use WP_REST_Request;

class CustomerController extends BaseController {
    private $customerRepository;

    public function __construct() {
        $this->customerRepository = new CustomerRepository();
    }

    /**
     * GET /customers
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'customer_code', 'customer_name', 'created_at'];
        $search_fields = ['customer_code', 'customer_name', 'mobile', 'email', 'gst_number', 'state'];
        
        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->customerRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Customers retrieved successfully.', $results);
    }

    /**
     * GET /customers/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return $this->error('Customer not found.', [], 404);
        }

        return $this->success('Customer retrieved successfully.', $customer);
    }

    /**
     * POST /customers
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['customer_name'])) {
            return $this->error('Validation failed: customer_name is required.');
        }

        // Generate customer code
        $customer_code = 'CUST-ACC-' . sprintf('%04d', rand(1000, 9999));
        while ($this->customerRepository->existsCustomerCode($customer_code)) {
            $customer_code = 'CUST-ACC-' . sprintf('%04d', rand(1000, 9999));
        }

        $data = [
            'customer_code' => $customer_code,
            'customer_name' => sanitize_text_field($params['customer_name']),
            'mobile' => sanitize_text_field($params['mobile'] ?? ''),
            'email' => sanitize_email($params['email'] ?? ''),
            'address' => sanitize_textarea_field($params['address'] ?? ''),
            'gst_number' => sanitize_text_field($params['gst_number'] ?? ''),
            'state' => sanitize_text_field($params['state'] ?? ''),
            'credit_limit' => floatval($params['credit_limit'] ?? 0.00),
            'outstanding_amount' => floatval($params['outstanding_amount'] ?? 0.00),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%f', '%s'];
        $inserted_id = $this->customerRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create customer.');
        }

        AuthService::logActivity(get_current_user_id(), 'CUSTOMER_CREATE', "Created customer profile $customer_code ($inserted_id)");

        return $this->success('Customer profile created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /customers/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return $this->error('Customer not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = [
            'customer_name' => '%s',
            'mobile' => '%s',
            'email' => '%s',
            'address' => '%s',
            'gst_number' => '%s',
            'state' => '%s',
            'credit_limit' => '%f',
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

        $updated = $this->customerRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update customer details.');
        }

        AuthService::logActivity(get_current_user_id(), 'CUSTOMER_UPDATE', "Updated customer ID: $id");

        return $this->success('Customer updated successfully.', $this->customerRepository->findById($id));
    }

    /**
     * DELETE /customers/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return $this->error('Customer not found.', [], 404);
        }

        $deleted = $this->customerRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete customer.');
        }

        AuthService::logActivity(get_current_user_id(), 'CUSTOMER_DELETE', "Soft deleted customer ID: $id ($customer[customer_code])");

        return $this->success('Customer deleted successfully.');
    }
}
