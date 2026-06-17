<?php
namespace ServiceManagementApi\Controllers;

use ServiceManagementApi\Repositories\QuotationRepository;
use ServiceManagementApi\Repositories\LeadRepository;
use ServiceManagementApi\Services\AuthService;
use WP_REST_Request;

class QuotationController extends BaseController {
    private $quotationRepository;
    private $leadRepository;

    public function __construct() {
        $this->quotationRepository = new QuotationRepository();
        $this->leadRepository = new LeadRepository();
    }

    /**
     * GET /quotations
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'quotation_number', 'quotation_date', 'total_amount', 'status'];
        $search_fields = ['quotation_number', 'customer_name', 'status', 'email', 'phone'];

        $extra_filters = [];
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->quotationRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);

        foreach ($results['data'] as &$row) {
            $row['items'] = $this->quotationRepository->getQuotationItems($row['id']);
            if (!empty($row['lead_id'])) {
                $lead = $this->leadRepository->findById($row['lead_id']);
                $row['lead_name'] = $lead ? $lead['lead_name'] : 'Unknown';
            } else {
                $row['lead_name'] = 'Direct Quote';
            }
        }

        return $this->success('Quotations list retrieved.', $results);
    }

    /**
     * GET /quotations/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $quotation = $this->quotationRepository->findById($id);

        if (!$quotation) {
            return $this->error('Quotation not found.', [], 404);
        }

        $quotation['items'] = $this->quotationRepository->getQuotationItems($id);
        if (!empty($quotation['lead_id'])) {
            $lead = $this->leadRepository->findById($quotation['lead_id']);
            $quotation['lead_name'] = $lead ? $lead['lead_name'] : 'Unknown';
        } else {
            $quotation['lead_name'] = 'Direct Quote';
        }

        return $this->success('Quotation retrieved successfully.', $quotation);
    }

    /**
     * POST /quotations
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['customer_name']) || empty($params['items']) || !is_array($params['items'])) {
            return $this->error('customer_name and items array are required.');
        }

        // Generate quotation number
        $quotation_number = 'QT-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        while ($this->quotationRepository->existsQuotationNumber($quotation_number)) {
            $quotation_number = 'QT-' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        }

        $quotation_date = sanitize_text_field($params['quotation_date'] ?? date('Y-m-d'));
        $lead_id = isset($params['lead_id']) ? intval($params['lead_id']) : null;

        // Validate items and calculate total amount
        $items = [];
        $total_amount = 0.00;
        foreach ($params['items'] as $item) {
            if (empty($item['service_name']) || !isset($item['price'])) {
                continue;
            }
            $qty = intval($item['quantity'] ?? 1);
            $price = floatval($item['price']);
            $total_amount += $qty * $price;

            $items[] = [
                'service_name' => $item['service_name'],
                'quantity' => $qty,
                'price' => $price
            ];
        }

        if (empty($items)) {
            return $this->error('No valid service items added to the quotation.');
        }

        $data = [
            'quotation_number' => $quotation_number,
            'lead_id' => $lead_id,
            'customer_name' => sanitize_text_field($params['customer_name']),
            'email' => sanitize_email($params['email'] ?? ''),
            'phone' => sanitize_text_field($params['phone'] ?? ''),
            'quotation_date' => $quotation_date,
            'total_amount' => $total_amount,
            'status' => sanitize_text_field($params['status'] ?? 'Draft')
        ];

        $formats = ['%s', '%d', '%s', '%s', '%s', '%s', '%f', '%s'];
        $inserted_id = $this->quotationRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create quotation record.');
        }

        // Add quotation items
        $this->quotationRepository->addQuotationItems($inserted_id, $items);

        // Update Lead Status to converted if quotation is generated
        if ($lead_id) {
            $this->leadRepository->update($lead_id, ['status' => 'Converted'], ['%s']);
        }

        AuthService::logActivity(
            get_current_user_id(),
            'QUOTATION_CREATE',
            "Created quotation $quotation_number (Total: $total_amount) for {$data['customer_name']}"
        );

        return $this->success('Quotation created successfully.', ['id' => $inserted_id, 'quotation_number' => $quotation_number], 201);
    }

    /**
     * PUT /quotations/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $quotation = $this->quotationRepository->findById($id);

        if (!$quotation) {
            return $this->error('Quotation not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        if (isset($params['status'])) {
            $status = sanitize_text_field($params['status']);
            if (!in_array($status, ['Draft', 'Sent', 'Accepted', 'Declined'])) {
                return $this->error('Invalid quotation status.');
            }
            $data['status'] = $status;
            $formats[] = '%s';
        }

        if (isset($params['quotation_date'])) {
            $data['quotation_date'] = sanitize_text_field($params['quotation_date']);
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->quotationRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update quotation details.');
        }

        AuthService::logActivity(get_current_user_id(), 'QUOTATION_UPDATE', "Updated quotation status/date of ID: $id");

        return $this->success('Quotation updated successfully.', $this->quotationRepository->findById($id));
    }

    /**
     * DELETE /quotations/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $quotation = $this->quotationRepository->findById($id);

        if (!$quotation) {
            return $this->error('Quotation not found.', [], 404);
        }

        $deleted = $this->quotationRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete quotation.');
        }

        AuthService::logActivity(get_current_user_id(), 'QUOTATION_DELETE', "Soft deleted quotation ID: $id ({$quotation['quotation_number']})");

        return $this->success('Quotation deleted successfully.');
    }
}
