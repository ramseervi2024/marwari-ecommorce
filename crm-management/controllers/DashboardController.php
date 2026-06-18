<?php
namespace CrmManagementApi\Controllers;

use CrmManagementApi\Repositories\LeadRepository;
use CrmManagementApi\Repositories\FollowupRepository;
use CrmManagementApi\Repositories\QuotationRepository;
use CrmManagementApi\Repositories\DealRepository;
use CrmManagementApi\Repositories\CustomerRepository;
use WP_REST_Request;

class DashboardController extends BaseController {
    private $leadRepository;
    private $followupRepository;
    private $quotationRepository;
    private $dealRepository;
    private $customerRepository;

    public function __construct() {
        $this->leadRepository      = new LeadRepository();
        $this->followupRepository  = new FollowupRepository();
        $this->quotationRepository = new QuotationRepository();
        $this->dealRepository      = new DealRepository();
        $this->customerRepository  = new CustomerRepository();
    }

    /**
     * GET /dashboard/stats
     */
    public function getStats(WP_REST_Request $request) {
        global $wpdb;
        $current_user = wp_get_current_user();
        $today        = current_time('Y-m-d');
        $this_month   = date('Y-m');

        $table_leads      = $wpdb->prefix . 'crm_leads';
        $table_followups  = $wpdb->prefix . 'crm_followups';
        $table_quotations = $wpdb->prefix . 'crm_quotations';
        $table_deals      = $wpdb->prefix . 'crm_deals';
        $table_customers  = $wpdb->prefix . 'crm_customers';
        $table_invoices   = $wpdb->prefix . 'crm_invoices';
        $table_payments   = $wpdb->prefix . 'crm_payments';

        // Check if user is customer
        if (in_array('crm_customer', (array)$current_user->roles)) {
            // Customer Portal Stats
            $cust = $this->customerRepository->findByUserId($current_user->ID);
            if (!$cust) {
                return $this->error('No customer record associated with your account.', [], 404);
            }
            $cust_id = $cust['id'];

            $total_quotes = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_quotations q JOIN $table_leads l ON q.lead_id = l.id WHERE l.email = %s", $current_user->user_email));
            $total_invoices = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_invoices WHERE customer_id = %d", $cust_id));
            $unpaid_invoices = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_invoices WHERE customer_id = %d AND status = 'Unpaid'", $cust_id));
            
            return $this->success('Customer portal stats retrieved.', [
                'summary' => [
                    'total_quotations' => $total_quotes,
                    'total_invoices'   => $total_invoices,
                    'unpaid_invoices'  => $unpaid_invoices,
                    'customer_code'    => $cust['customer_code']
                ]
            ]);
        }

        // Staff stats: check restriction (Sales Executive and Telecaller only see their own assigned leads/tasks/followups)
        $is_admin_or_manager = current_user_can('manage_crm_settings') || current_user_can('view_crm_reports');
        $user_id_filter = $is_admin_or_manager ? null : $current_user->ID;

        // Total Leads
        $lead_query = "SELECT COUNT(*) FROM $table_leads WHERE deleted_at IS NULL";
        $lead_args = [];
        if ($user_id_filter) {
            $lead_query .= " AND assigned_to = %d";
            $lead_args[] = $user_id_filter;
        }
        $total_leads = (int)$wpdb->get_var($lead_args ? $wpdb->prepare($lead_query, $lead_args) : $lead_query);

        // New Leads
        $new_lead_query = "SELECT COUNT(*) FROM $table_leads WHERE deleted_at IS NULL AND lead_status = 'New'";
        if ($user_id_filter) {
            $new_lead_query .= " AND assigned_to = %d";
        }
        $new_leads = (int)$wpdb->get_var($lead_args ? $wpdb->prepare($new_lead_query, $lead_args) : $new_lead_query);

        // Followups Today
        $followup_query = "SELECT COUNT(*) FROM $table_followups f 
                           JOIN $table_leads l ON f.lead_id = l.id 
                           WHERE f.followup_date = %s";
        $followup_args = [$today];
        if ($user_id_filter) {
            $followup_query .= " AND l.assigned_to = %d";
            $followup_args[] = $user_id_filter;
        }
        $followups_today = (int)$wpdb->get_var($wpdb->prepare($followup_query, $followup_args));

        // Quotations Sent
        $quote_query = "SELECT COUNT(*) FROM $table_quotations q 
                        JOIN $table_leads l ON q.lead_id = l.id 
                        WHERE q.status = 'Sent'";
        $quote_args = [];
        if ($user_id_filter) {
            $quote_query .= " AND l.assigned_to = %d";
            $quote_args[] = $user_id_filter;
        }
        $quotes_sent = (int)$wpdb->get_var($quote_args ? $wpdb->prepare($quote_query, $quote_args) : $quote_query);

        // Deals Won & Lost
        $won_deal_query = "SELECT COUNT(*) FROM $table_deals d 
                           JOIN $table_leads l ON d.lead_id = l.id 
                           WHERE d.deal_stage = 'Won'";
        $lost_deal_query = "SELECT COUNT(*) FROM $table_deals d 
                            JOIN $table_leads l ON d.lead_id = l.id 
                            WHERE d.deal_stage = 'Lost'";
        $deal_args = [];
        if ($user_id_filter) {
            $won_deal_query .= " AND d.assigned_to = %d";
            $lost_deal_query .= " AND d.assigned_to = %d";
            $deal_args[] = $user_id_filter;
        }
        $deals_won = (int)$wpdb->get_var($deal_args ? $wpdb->prepare($won_deal_query, $deal_args) : $won_deal_query);
        $deals_lost = (int)$wpdb->get_var($deal_args ? $wpdb->prepare($lost_deal_query, $deal_args) : $lost_deal_query);

        // Monthly Revenue (sum of successful payments this month)
        $payment_query = "SELECT SUM(amount) FROM $table_payments 
                          WHERE DATE_FORMAT(payment_date, '%%Y-%%m') = %s AND status = 'Success'";
        $monthly_revenue = (float)$wpdb->get_var($wpdb->prepare($payment_query, $this_month));

        // Conversion Rate: Won Deals / Total Leads * 100
        $conversion_rate = 0;
        if ($total_leads > 0) {
            $conversion_rate = round(($deals_won / $total_leads) * 100, 2);
        }

        // Funnel stats by Deal Stage
        $stages = ['Prospecting', 'Qualification', 'Proposal', 'Negotiation', 'Won', 'Lost'];
        $funnel = [];
        foreach ($stages as $stage) {
            $funnel_query = "SELECT COUNT(*) FROM $table_deals d 
                             JOIN $table_leads l ON d.lead_id = l.id 
                             WHERE d.deal_stage = %s";
            $funnel_args = [$stage];
            if ($user_id_filter) {
                $funnel_query .= " AND d.assigned_to = %d";
                $funnel_args[] = $user_id_filter;
            }
            $funnel[$stage] = (int)$wpdb->get_var($wpdb->prepare($funnel_query, $funnel_args));
        }

        // Recent Followups Today
        $recent_followups_query = "SELECT f.*, l.first_name, l.last_name, l.company_name FROM $table_followups f 
                                   JOIN $table_leads l ON f.lead_id = l.id 
                                   WHERE f.followup_date = %s";
        $rf_args = [$today];
        if ($user_id_filter) {
            $recent_followups_query .= " AND l.assigned_to = %d";
            $rf_args[] = $user_id_filter;
        }
        $recent_followups_query .= " ORDER BY f.followup_time ASC LIMIT 5";
        $rf_records = $wpdb->get_results($wpdb->prepare($recent_followups_query, $rf_args), ARRAY_A);

        return $this->success('Dashboard stats retrieved.', [
            'summary' => [
                'total_leads'     => $total_leads,
                'new_leads'       => $new_leads,
                'followups_today' => $followups_today,
                'quotes_sent'     => $quotes_sent,
                'deals_won'       => $deals_won,
                'deals_lost'      => $deals_lost,
                'monthly_revenue' => round($monthly_revenue, 2),
                'conversion_rate' => $conversion_rate
            ],
            'funnel'           => $funnel,
            'recent_followups' => $rf_records ?: []
        ]);
    }

    /**
     * GET /dashboard/activity-logs
     */
    public function getActivityLogs(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_params();
        $limit  = max(1, min(100, intval($params['limit'] ?? 50)));
        $page   = max(1, intval($params['page'] ?? 1));
        $offset = ($page - 1) * $limit;
        $table  = $wpdb->prefix . 'crm_activity_logs';

        $total  = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table");
        $logs   = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $limit, $offset
        ), ARRAY_A);

        foreach ($logs as &$log) {
            if ($log['user_id']) {
                $user = get_userdata($log['user_id']);
                $log['username'] = $user ? $user->display_name : 'System';
            } else {
                $log['username'] = 'System';
            }
        }

        return $this->success('Activity logs retrieved.', [
            'total'  => $total,
            'page'   => $page,
            'limit'  => $limit,
            'pages'  => ceil($total / $limit),
            'data'   => $logs,
        ]);
    }
}
