<?php
namespace CrmManagementApi\Controllers;

use CrmManagementApi\Repositories\QuotationRepository;
use CrmManagementApi\Repositories\LeadRepository;
use CrmManagementApi\Services\AuthService;
use WP_REST_Request;

class QuotationController extends BaseController {
    private $quotationRepository;
    private $leadRepository;

    public function __construct() {
        $this->quotationRepository = new QuotationRepository();
        $this->leadRepository      = new LeadRepository();
    }

    /**
     * GET /quotations
     */
    public function getQuotations(WP_REST_Request $request) {
        $params = $request->get_params();
        $current_user = wp_get_current_user();

        $allowed_sorts = ['id', 'quotation_number', 'quotation_date', 'valid_until', 'grand_total', 'status'];
        $extra_filters = [];

        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['lead_id'])) {
            $extra_filters['lead_id'] = intval($params['lead_id']);
        }

        // Executive restriction: see only quotations of their assigned leads
        // Customer restriction: see only quotations of leads matching their customer email
        if (in_array('crm_customer', (array)$current_user->roles)) {
            global $wpdb;
            $table_leads = $wpdb->prefix . 'crm_leads';
            $customer_lead_ids = $wpdb->get_col($wpdb->prepare("SELECT id FROM $table_leads WHERE email = %s AND deleted_at IS NULL", $current_user->user_email));
            
            if (empty($customer_lead_ids)) {
                return $this->success('Quotations list (empty).', [
                    'total' => 0, 'page' => 1, 'limit' => 10, 'pages' => 0, 'data' => []
                ]);
            }
            $in_clause = implode(',', array_map('intval', $customer_lead_ids));
            $table_name = $wpdb->prefix . 'crm_quotations';
            
            $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
            $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 10;
            $offset = ($page - 1) * $limit;
            $sort = isset($params['sort']) && in_array($params['sort'], $allowed_sorts) ? $params['sort'] : 'id';
            $order = isset($params['order']) && strtoupper($params['order']) === 'DESC' ? 'DESC' : 'ASC';

            $where = "lead_id IN ($in_clause) AND status != 'Draft'"; // Don't show drafts to customers
            $total = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE $where");
            $rows = $wpdb->get_results("SELECT * FROM $table_name WHERE $where ORDER BY $sort $order LIMIT $limit OFFSET $offset", ARRAY_A);

            $results = [
                'total' => $total, 'page' => $page, 'limit' => $limit, 'pages' => ceil($total / $limit), 'data' => $rows ?: []
            ];
        } elseif (!current_user_can('view_crm_reports') && !current_user_can('manage_crm_settings')) {
            global $wpdb;
            $table_leads = $wpdb->prefix . 'crm_leads';
            $assigned_lead_ids = $wpdb->get_col($wpdb->prepare("SELECT id FROM $table_leads WHERE assigned_to = %d AND deleted_at IS NULL", $current_user->ID));
            
            if (empty($assigned_lead_ids)) {
                return $this->success('Quotations list (empty).', [
                    'total' => 0, 'page' => 1, 'limit' => 10, 'pages' => 0, 'data' => []
                ]);
            }
            $in_clause = implode(',', array_map('intval', $assigned_lead_ids));
            $table_name = $wpdb->prefix . 'crm_quotations';
            
            $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
            $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 10;
            $offset = ($page - 1) * $limit;
            $sort = isset($params['sort']) && in_array($params['sort'], $allowed_sorts) ? $params['sort'] : 'id';
            $order = isset($params['order']) && strtoupper($params['order']) === 'DESC' ? 'DESC' : 'ASC';

            $where = "lead_id IN ($in_clause)";
            $total = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE $where");
            $rows = $wpdb->get_results("SELECT * FROM $table_name WHERE $where ORDER BY $sort $order LIMIT $limit OFFSET $offset", ARRAY_A);

            $results = [
                'total' => $total, 'page' => $page, 'limit' => $limit, 'pages' => ceil($total / $limit), 'data' => $rows ?: []
            ];
        } else {
            $results = $this->quotationRepository->findAll($params, $allowed_sorts, [], $extra_filters);
        }

        // Map lead names
        foreach ($results['data'] as &$row) {
            $lead = $this->leadRepository->findById($row['lead_id']);
            $row['lead_name']    = $lead ? $lead['first_name'] . ' ' . $lead['last_name'] : '';
            $row['company_name'] = $lead ? $lead['company_name'] : '';
        }

        return $this->success('Quotations list retrieved.', $results);
    }

    /**
     * GET /quotations/{id}
     */
    public function getQuotation(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $quotation = $this->quotationRepository->findById($id);

        if (!$quotation) {
            return $this->error('Quotation not found.', [], 404);
        }

        // Privilege check
        $lead = $this->leadRepository->findById($quotation['lead_id']);
        $current_user = wp_get_current_user();
        if (in_array('crm_customer', (array)$current_user->roles)) {
            if (!$lead || $lead['email'] !== $current_user->user_email) {
                return $this->error('Access Denied.', [], 403);
            }
        } elseif (!current_user_can('view_crm_reports') && !current_user_can('manage_crm_settings')) {
            if ($lead && intval($lead['assigned_to']) !== $current_user->ID) {
                return $this->error('Access Denied.', [], 403);
            }
        }

        $quotation['lead_name']    = $lead ? $lead['first_name'] . ' ' . $lead['last_name'] : '';
        $quotation['company_name'] = $lead ? $lead['company_name'] : '';
        $quotation['mobile']       = $lead ? $lead['mobile'] : '';
        $quotation['email']        = $lead ? $lead['email'] : '';
        
        // Decode JSON items
        $quotation['items'] = json_decode($quotation['items'] ?? '[]', true);

        return $this->success('Quotation retrieved.', $quotation);
    }

    /**
     * POST /quotations
     */
    public function createQuotation(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['lead_id']) || empty($params['quotation_date']) || empty($params['valid_until'])) {
            return $this->error('lead_id, quotation_date, and valid_until are required.');
        }

        $lead_id = intval($params['lead_id']);
        $lead = $this->leadRepository->findById($lead_id);
        if (!$lead) {
            return $this->error('Lead not found.', [], 404);
        }

        // Auto generate quote number
        global $wpdb;
        $table_name = $wpdb->prefix . 'crm_quotations';
        $max_id = (int)$wpdb->get_var("SELECT MAX(id) FROM $table_name") + 1;
        $quotation_number = 'QT-' . date('Y') . '-' . sprintf('%04d', $max_id);

        $subtotal   = floatval($params['subtotal'] ?? 0);
        $discount   = floatval($params['discount'] ?? 0);
        $tax_amount = floatval($params['tax_amount'] ?? 0);
        $grand_total = ($subtotal - $discount) + $tax_amount;

        $items = isset($params['items']) ? json_encode($params['items']) : '[]';

        $data = [
            'quotation_number' => $quotation_number,
            'lead_id'          => $lead_id,
            'quotation_date'   => sanitize_text_field($params['quotation_date']),
            'valid_until'      => sanitize_text_field($params['valid_until']),
            'subtotal'         => $subtotal,
            'discount'         => $discount,
            'tax_amount'       => $tax_amount,
            'grand_total'      => $grand_total,
            'status'           => sanitize_text_field($params['status'] ?? 'Draft'),
            'items'            => $items
        ];

        $formats = ['%s', '%d', '%s', '%s', '%f', '%f', '%f', '%f', '%s', '%s'];

        $id = $this->quotationRepository->create($data, $formats);
        if (!$id) {
            return $this->error('Failed to create quotation.');
        }

        // Update lead status to "Quotation Sent" if needed
        if ($data['status'] === 'Sent' && $lead['lead_status'] !== 'Negotiation' && $lead['lead_status'] !== 'Won') {
            $this->leadRepository->update($lead_id, ['lead_status' => 'Quotation Sent'], ['%s']);
        }

        AuthService::logActivity(get_current_user_id(), 'QUOTATION_CREATE', "Created quotation: $quotation_number");

        return $this->success('Quotation created successfully.', $this->quotationRepository->findById($id), 201);
    }

    /**
     * PUT /quotations/{id}
     */
    public function updateQuotation(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $quotation = $this->quotationRepository->findById($id);

        if (!$quotation) {
            return $this->error('Quotation not found.', [], 404);
        }

        $lead = $this->leadRepository->findById($quotation['lead_id']);
        $current_user = wp_get_current_user();

        // Customer privilege: can only update status to Accept or Reject
        if (in_array('crm_customer', (array)$current_user->roles)) {
            if (!$lead || $lead['email'] !== $current_user->user_email) {
                return $this->error('Access Denied.', [], 403);
            }
            $params = $request->get_json_params();
            $status = sanitize_text_field($params['status'] ?? '');
            if (!in_array($status, ['Accepted', 'Rejected'])) {
                return $this->error('Customers can only Accept or Reject quotations.');
            }

            $this->quotationRepository->update($id, ['status' => $status], ['%s']);
            
            // If accepted, update lead status to Negotiation or Won
            if ($status === 'Accepted') {
                $this->leadRepository->update($lead['id'], ['lead_status' => 'Negotiation'], ['%s']);
            }

            AuthService::logActivity($current_user->ID, 'QUOTATION_STATUS', "Customer marked quotation $quotation[quotation_number] as $status");
            return $this->success("Quotation status updated to $status successfully.", $this->quotationRepository->findById($id));
        }

        // Executive privilege check
        if (!current_user_can('view_crm_reports') && !current_user_can('manage_crm_settings')) {
            if ($lead && intval($lead['assigned_to']) !== $current_user->ID) {
                return $this->error('Access Denied.', [], 403);
            }
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = [
            'quotation_date' => '%s',
            'valid_until'    => '%s',
            'subtotal'       => '%f',
            'discount'       => '%f',
            'tax_amount'     => '%f',
            'status'         => '%s'
        ];

        foreach ($fields as $key => $fmt) {
            if (isset($params[$key])) {
                $data[$key] = ($fmt === '%f') ? floatval($params[$key]) : sanitize_text_field($params[$key]);
                $formats[] = $fmt;
            }
        }

        if (isset($params['items'])) {
            $data['items'] = json_encode($params['items']);
            $formats[] = '%s';
        }

        // Calculate grand total if subtotal/discount/tax updated
        if (isset($data['subtotal']) || isset($data['discount']) || isset($data['tax_amount'])) {
            $sub = isset($data['subtotal']) ? $data['subtotal'] : floatval($quotation['subtotal']);
            $disc = isset($data['discount']) ? $data['discount'] : floatval($quotation['discount']);
            $tax = isset($data['tax_amount']) ? $data['tax_amount'] : floatval($quotation['tax_amount']);
            $data['grand_total'] = ($sub - $disc) + $tax;
            $formats[] = '%f';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->quotationRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update quotation.');
        }

        // Update lead status to "Quotation Sent" if needed
        if (isset($data['status']) && $data['status'] === 'Sent' && $lead && $lead['lead_status'] !== 'Negotiation' && $lead['lead_status'] !== 'Won') {
            $this->leadRepository->update($lead['id'], ['lead_status' => 'Quotation Sent'], ['%s']);
        }

        AuthService::logActivity(get_current_user_id(), 'QUOTATION_UPDATE', "Updated quotation: $quotation[quotation_number]");

        return $this->success('Quotation updated successfully.', $this->quotationRepository->findById($id));
    }

    /**
     * DELETE /quotations/{id}
     */
    public function deleteQuotation(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $quotation = $this->quotationRepository->findById($id);

        if (!$quotation) {
            return $this->error('Quotation not found.', [], 404);
        }

        if (!current_user_can('manage_crm_settings') && !current_user_can('view_crm_reports')) {
            return $this->error('Access Denied.', [], 403);
        }

        $deleted = $this->quotationRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete quotation.');
        }

        AuthService::logActivity(get_current_user_id(), 'QUOTATION_DELETE', "Deleted quotation ID: $id ($quotation[quotation_number])");

        return $this->success('Quotation deleted successfully.');
    }
}
