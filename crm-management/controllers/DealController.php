<?php
namespace CrmManagementApi\Controllers;

use CrmManagementApi\Repositories\DealRepository;
use CrmManagementApi\Repositories\LeadRepository;
use CrmManagementApi\Repositories\CustomerRepository;
use CrmManagementApi\Services\AuthService;
use WP_REST_Request;

class DealController extends BaseController {
    private $dealRepository;
    private $leadRepository;
    private $customerRepository;

    public function __construct() {
        $this->dealRepository     = new DealRepository();
        $this->leadRepository     = new LeadRepository();
        $this->customerRepository = new CustomerRepository();
    }

    /**
     * GET /deals
     */
    public function getDeals(WP_REST_Request $request) {
        $params = $request->get_params();
        $current_user = wp_get_current_user();

        $allowed_sorts = ['id', 'deal_number', 'deal_value', 'expected_close_date', 'deal_stage', 'probability'];
        $extra_filters = [];

        if (isset($params['deal_stage'])) {
            $extra_filters['deal_stage'] = sanitize_text_field($params['deal_stage']);
        }
        if (isset($params['lead_id'])) {
            $extra_filters['lead_id'] = intval($params['lead_id']);
        }

        // Executive / Telecaller restriction: see only assigned deals
        if (!current_user_can('view_crm_reports') && !current_user_can('manage_crm_settings')) {
            $extra_filters['assigned_to'] = $current_user->ID;
        } elseif (isset($params['assigned_to'])) {
            $extra_filters['assigned_to'] = intval($params['assigned_to']);
        }

        $results = $this->dealRepository->findAll($params, $allowed_sorts, [], $extra_filters);

        // Map titles
        foreach ($results['data'] as &$row) {
            $lead = $this->leadRepository->findById($row['lead_id']);
            $row['lead_name']    = $lead ? $lead['first_name'] . ' ' . $lead['last_name'] : 'Unknown Lead';
            $row['company_name'] = $lead ? $lead['company_name'] : '';
            
            if ($row['customer_id']) {
                $cust = $this->customerRepository->findById($row['customer_id']);
                $row['customer_name'] = $cust ? $cust['company_name'] : '';
            } else {
                $row['customer_name'] = '';
            }

            if ($row['assigned_to']) {
                $assigned = get_userdata($row['assigned_to']);
                $row['assigned_name'] = $assigned ? $assigned->display_name : 'Unknown';
            } else {
                $row['assigned_name'] = 'Unassigned';
            }
        }

        return $this->success('Deals list retrieved.', $results);
    }

    /**
     * POST /deals
     */
    public function createDeal(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['lead_id']) || empty($params['deal_value'])) {
            return $this->error('lead_id and deal_value are required.');
        }

        $lead_id = intval($params['lead_id']);
        $lead = $this->leadRepository->findById($lead_id);
        if (!$lead) {
            return $this->error('Lead not found.', [], 404);
        }

        // Check if customer exists or create one from lead
        $customer_id = null;
        if (!empty($params['customer_id'])) {
            $customer_id = intval($params['customer_id']);
        } else {
            // Auto convert/link to customer if stage is Won
            global $wpdb;
            $table_customers = $wpdb->prefix . 'crm_customers';
            $cust = $wpdb->get_row($wpdb->prepare("SELECT id FROM $table_customers WHERE email = %s", $lead['email']), ARRAY_A);
            if ($cust) {
                $customer_id = $cust['id'];
            } else {
                $customer_code = 'CUST-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
                $wpdb->insert($table_customers, [
                    'customer_code'  => $customer_code,
                    'company_name'   => $lead['company_name'] ?: ($lead['first_name'] . ' ' . $lead['last_name']),
                    'contact_person' => $lead['first_name'] . ' ' . $lead['last_name'],
                    'mobile'         => $lead['mobile'],
                    'email'          => $lead['email'],
                    'status'         => 'Active'
                ]);
                $customer_id = $wpdb->insert_id;
            }
        }

        // Auto generate deal number
        global $wpdb;
        $table_name = $wpdb->prefix . 'crm_deals';
        $max_id = (int)$wpdb->get_var("SELECT MAX(id) FROM $table_name") + 1;
        $deal_number = 'DL-' . date('Y') . '-' . sprintf('%04d', $max_id);

        $deal_stage = sanitize_text_field($params['deal_stage'] ?? 'Prospecting');
        $probability = intval($params['probability'] ?? 10);
        $assigned_to = isset($params['assigned_to']) ? intval($params['assigned_to']) : get_current_user_id();

        $data = [
            'deal_number'         => $deal_number,
            'lead_id'             => $lead_id,
            'customer_id'         => $customer_id,
            'deal_value'          => floatval($params['deal_value']),
            'expected_close_date' => !empty($params['expected_close_date']) ? sanitize_text_field($params['expected_close_date']) : null,
            'deal_stage'          => $deal_stage,
            'probability'         => $probability,
            'assigned_to'         => $assigned_to
        ];

        $formats = ['%s', '%d', '%d', '%f', '%s', '%s', '%d', '%d'];

        $id = $this->dealRepository->create($data, $formats);
        if (!$id) {
            return $this->error('Failed to create deal.');
        }

        // Automatically update the lead status to reflect deal stage
        if ($deal_stage === 'Won') {
            $this->leadRepository->update($lead_id, ['lead_status' => 'Won'], ['%s']);
        } elseif ($deal_stage === 'Lost') {
            $this->leadRepository->update($lead_id, ['lead_status' => 'Lost'], ['%s']);
        } else {
            $this->leadRepository->update($lead_id, ['lead_status' => 'Negotiation'], ['%s']);
        }

        AuthService::logActivity(get_current_user_id(), 'DEAL_CREATE', "Created deal: $deal_number for lead ID: $lead_id");

        return $this->success('Deal created successfully.', $this->dealRepository->findById($id), 201);
    }

    /**
     * GET /deals/{id}
     */
    public function getDeal(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $deal = $this->dealRepository->findById($id);
        if (!$deal) {
            return $this->error('Deal not found.', [], 404);
        }
        $lead = $this->leadRepository->findById($deal['lead_id']);
        $deal['lead_name']    = $lead ? $lead['first_name'] . ' ' . $lead['last_name'] : '';
        $deal['company_name'] = $lead ? $lead['company_name'] : '';
        if ($deal['customer_id']) {
            $cust = $this->customerRepository->findById($deal['customer_id']);
            $deal['customer_name'] = $cust ? $cust['company_name'] : '';
        }
        return $this->success('Deal retrieved.', $deal);
    }

    /**
     * PUT /deals/{id}
     */
    public function updateDeal(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $deal = $this->dealRepository->findById($id);

        if (!$deal) {
            return $this->error('Deal not found.', [], 404);
        }

        // Privilege check
        $current_user = wp_get_current_user();
        if (!current_user_can('view_crm_reports') && !current_user_can('manage_crm_settings')) {
            if (intval($deal['assigned_to']) !== $current_user->ID) {
                return $this->error('Access Denied.', [], 403);
            }
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = [
            'customer_id'         => '%d',
            'deal_value'          => '%f',
            'expected_close_date' => '%s',
            'deal_stage'          => '%s',
            'probability'         => '%d',
            'assigned_to'         => '%d'
        ];

        foreach ($fields as $key => $fmt) {
            if (isset($params[$key])) {
                if ($fmt === '%d') {
                    $data[$key] = intval($params[$key]);
                } elseif ($fmt === '%f') {
                    $data[$key] = floatval($params[$key]);
                } else {
                    $data[$key] = sanitize_text_field($params[$key]);
                }
                $formats[] = $fmt;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->dealRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update deal.');
        }

        // Synchronize lead status if stage updated
        if (isset($data['deal_stage'])) {
            $stage = $data['deal_stage'];
            $lead_status = 'Negotiation';
            if ($stage === 'Won') {
                $lead_status = 'Won';
            } elseif ($stage === 'Lost') {
                $lead_status = 'Lost';
            }
            $this->leadRepository->update($deal['lead_id'], ['lead_status' => $lead_status], ['%s']);
        }

        AuthService::logActivity(get_current_user_id(), 'DEAL_UPDATE', "Updated deal ID: $id ($deal[deal_number])");

        return $this->success('Deal updated successfully.', $this->dealRepository->findById($id));
    }

    /**
     * DELETE /deals/{id}
     */
    public function deleteDeal(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $deal = $this->dealRepository->findById($id);

        if (!$deal) {
            return $this->error('Deal not found.', [], 404);
        }

        if (!current_user_can('manage_crm_settings') && !current_user_can('view_crm_reports')) {
            return $this->error('Access Denied.', [], 403);
        }

        $deleted = $this->dealRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete deal.');
        }

        AuthService::logActivity(get_current_user_id(), 'DEAL_DELETE', "Deleted deal ID: $id ($deal[deal_number])");

        return $this->success('Deal deleted successfully.');
    }

    /**
     * GET /pipeline
     * Returns grouped Kanban columns of deals
     */
    public function getPipeline(WP_REST_Request $request) {
        global $wpdb;
        $current_user = wp_get_current_user();

        $table_deals = $wpdb->prefix . 'crm_deals';
        $table_leads = $wpdb->prefix . 'crm_leads';

        $is_admin_or_manager = current_user_can('view_crm_reports') || current_user_can('manage_crm_settings');
        $user_id_filter = $is_admin_or_manager ? null : $current_user->ID;

        // Fetch all active deals
        $query = "SELECT d.*, l.first_name, l.last_name, l.company_name FROM $table_deals d 
                  JOIN $table_leads l ON d.lead_id = l.id 
                  WHERE l.deleted_at IS NULL";
        
        $args = [];
        if ($user_id_filter) {
            $query .= " AND d.assigned_to = %d";
            $args[] = $user_id_filter;
        }

        $deals = $wpdb->get_results($args ? $wpdb->prepare($query, $args) : $query, ARRAY_A);

        $stages = [
            'Prospecting' => [],
            'Qualification' => [],
            'Proposal' => [],
            'Negotiation' => [],
            'Won' => [],
            'Lost' => []
        ];

        foreach ($deals as $d) {
            $stage = $d['deal_stage'];
            if (!isset($stages[$stage])) {
                $stages[$stage] = [];
            }
            $d['lead_name'] = $d['first_name'] . ' ' . $d['last_name'];
            $stages[$stage][] = $d;
        }

        // Calculate columns summaries
        $columns = [];
        foreach ($stages as $stage_name => $items) {
            $value = 0;
            foreach ($items as $item) {
                $value += floatval($item['deal_value']);
            }
            $columns[] = [
                'stage' => $stage_name,
                'count' => count($items),
                'total_value' => round($value, 2),
                'items' => $items
            ];
        }

        return $this->success('Kanban pipeline data retrieved.', $columns);
    }
}
