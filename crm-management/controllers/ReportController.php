<?php
namespace CrmManagementApi\Controllers;

use CrmManagementApi\Services\AuthService;
use WP_REST_Request;

if (!defined('ABSPATH')) {
    exit;
}

class ReportController extends BaseController {

    /**
     * GET /reports/leads
     * Lead count by status and source, with date filters
     */
    public function getLeadsReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'crm_leads';
        $params = $request->get_params();
        $date_from = sanitize_text_field($params['date_from'] ?? date('Y-m-01'));
        $date_to   = sanitize_text_field($params['date_to']   ?? date('Y-m-d'));

        // Leads by status
        $by_status = $wpdb->get_results($wpdb->prepare(
            "SELECT lead_status, COUNT(*) as count FROM $table WHERE deleted_at IS NULL AND DATE(created_at) BETWEEN %s AND %s GROUP BY lead_status",
            $date_from, $date_to
        ), ARRAY_A);

        // Leads by source
        $by_source = $wpdb->get_results($wpdb->prepare(
            "SELECT lead_source, COUNT(*) as count FROM $table WHERE deleted_at IS NULL AND DATE(created_at) BETWEEN %s AND %s GROUP BY lead_source",
            $date_from, $date_to
        ), ARRAY_A);

        // Daily trend
        $daily_trend = $wpdb->get_results($wpdb->prepare(
            "SELECT DATE(created_at) as date, COUNT(*) as count FROM $table WHERE deleted_at IS NULL AND DATE(created_at) BETWEEN %s AND %s GROUP BY DATE(created_at) ORDER BY date ASC",
            $date_from, $date_to
        ), ARRAY_A);

        return $this->success('Leads report retrieved.', [
            'date_from'   => $date_from,
            'date_to'     => $date_to,
            'by_status'   => $by_status,
            'by_source'   => $by_source,
            'daily_trend' => $daily_trend,
        ]);
    }

    /**
     * GET /reports/followups
     */
    public function getFollowupsReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'crm_followups';
        $params = $request->get_params();
        $date_from = sanitize_text_field($params['date_from'] ?? date('Y-m-01'));
        $date_to   = sanitize_text_field($params['date_to']   ?? date('Y-m-d'));

        $by_type = $wpdb->get_results($wpdb->prepare(
            "SELECT communication_type, COUNT(*) as count FROM $table WHERE DATE(followup_date) BETWEEN %s AND %s GROUP BY communication_type",
            $date_from, $date_to
        ), ARRAY_A);

        $by_status = $wpdb->get_results($wpdb->prepare(
            "SELECT status, COUNT(*) as count FROM $table WHERE DATE(followup_date) BETWEEN %s AND %s GROUP BY status",
            $date_from, $date_to
        ), ARRAY_A);

        $total = (int)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE DATE(followup_date) BETWEEN %s AND %s",
            $date_from, $date_to
        ));

        return $this->success('Follow-ups report retrieved.', [
            'date_from' => $date_from,
            'date_to'   => $date_to,
            'total'     => $total,
            'by_type'   => $by_type,
            'by_status' => $by_status,
        ]);
    }

    /**
     * GET /reports/quotations
     */
    public function getQuotationsReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'crm_quotations';
        $params = $request->get_params();
        $date_from = sanitize_text_field($params['date_from'] ?? date('Y-m-01'));
        $date_to   = sanitize_text_field($params['date_to']   ?? date('Y-m-d'));

        $by_status = $wpdb->get_results($wpdb->prepare(
            "SELECT status, COUNT(*) as count, SUM(grand_total) as total_value FROM $table WHERE DATE(quotation_date) BETWEEN %s AND %s GROUP BY status",
            $date_from, $date_to
        ), ARRAY_A);

        $total_value = (float)$wpdb->get_var($wpdb->prepare(
            "SELECT SUM(grand_total) FROM $table WHERE status = 'Accepted' AND DATE(quotation_date) BETWEEN %s AND %s",
            $date_from, $date_to
        ));

        return $this->success('Quotations report retrieved.', [
            'date_from'   => $date_from,
            'date_to'     => $date_to,
            'by_status'   => $by_status,
            'accepted_value' => round($total_value, 2),
        ]);
    }

    /**
     * GET /reports/deals
     */
    public function getDealsReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'crm_deals';
        $params = $request->get_params();
        $date_from = sanitize_text_field($params['date_from'] ?? date('Y-m-01'));
        $date_to   = sanitize_text_field($params['date_to']   ?? date('Y-m-d'));

        $by_stage = $wpdb->get_results($wpdb->prepare(
            "SELECT deal_stage, COUNT(*) as count, SUM(deal_value) as total_value FROM $table WHERE DATE(created_at) BETWEEN %s AND %s GROUP BY deal_stage",
            $date_from, $date_to
        ), ARRAY_A);

        $won_value  = (float)$wpdb->get_var($wpdb->prepare("SELECT SUM(deal_value) FROM $table WHERE deal_stage = 'Won' AND DATE(created_at) BETWEEN %s AND %s", $date_from, $date_to));
        $lost_value = (float)$wpdb->get_var($wpdb->prepare("SELECT SUM(deal_value) FROM $table WHERE deal_stage = 'Lost' AND DATE(created_at) BETWEEN %s AND %s", $date_from, $date_to));

        return $this->success('Deals report retrieved.', [
            'date_from'  => $date_from,
            'date_to'    => $date_to,
            'by_stage'   => $by_stage,
            'won_value'  => round($won_value, 2),
            'lost_value' => round($lost_value, 2),
        ]);
    }

    /**
     * GET /reports/pipeline
     */
    public function getPipelineReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'crm_deals';

        $stages = ['Prospecting', 'Qualification', 'Proposal', 'Negotiation', 'Won', 'Lost'];
        $pipeline = [];
        $total_weighted_value = 0;

        foreach ($stages as $stage) {
            $row = $wpdb->get_row($wpdb->prepare(
                "SELECT COUNT(*) as count, SUM(deal_value) as total_value, AVG(probability) as avg_probability FROM $table WHERE deal_stage = %s",
                $stage
            ), ARRAY_A);

            $weighted = (float)($row['total_value'] ?? 0) * ((float)($row['avg_probability'] ?? 0) / 100);
            $total_weighted_value += $weighted;

            $pipeline[] = [
                'stage'           => $stage,
                'count'           => (int)($row['count'] ?? 0),
                'total_value'     => round((float)($row['total_value'] ?? 0), 2),
                'avg_probability' => round((float)($row['avg_probability'] ?? 0), 2),
                'weighted_value'  => round($weighted, 2),
            ];
        }

        return $this->success('Pipeline report retrieved.', [
            'stages'               => $pipeline,
            'total_weighted_value' => round($total_weighted_value, 2),
        ]);
    }

    /**
     * GET /reports/revenue
     */
    public function getRevenueReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'crm_payments';
        $params = $request->get_params();
        $year = intval($params['year'] ?? date('Y'));

        $monthly = $wpdb->get_results($wpdb->prepare(
            "SELECT DATE_FORMAT(payment_date, '%%Y-%%m') as month, SUM(amount) as revenue, COUNT(*) as transactions FROM $table WHERE YEAR(payment_date) = %d AND status = 'Success' GROUP BY month ORDER BY month ASC",
            $year
        ), ARRAY_A);

        $total_revenue = (float)$wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM $table WHERE YEAR(payment_date) = %d AND status = 'Success'",
            $year
        ));

        return $this->success('Revenue report retrieved.', [
            'year'          => $year,
            'total_revenue' => round($total_revenue, 2),
            'monthly'       => $monthly,
        ]);
    }

    /**
     * GET /reports/team-performance
     */
    public function getTeamPerformanceReport(WP_REST_Request $request) {
        global $wpdb;
        $table_leads = $wpdb->prefix . 'crm_leads';
        $table_deals = $wpdb->prefix . 'crm_deals';
        $params = $request->get_params();
        $date_from = sanitize_text_field($params['date_from'] ?? date('Y-m-01'));
        $date_to   = sanitize_text_field($params['date_to']   ?? date('Y-m-d'));

        // Get CRM staff users
        $users = get_users([
            'role__in' => ['crm_super_admin', 'crm_sales_manager', 'crm_sales_executive', 'crm_telecaller'],
        ]);

        $performance = [];
        foreach ($users as $user) {
            $leads_assigned = (int)$wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_leads WHERE assigned_to = %d AND deleted_at IS NULL AND DATE(created_at) BETWEEN %s AND %s",
                $user->ID, $date_from, $date_to
            ));

            $deals_won = (int)$wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_deals WHERE assigned_to = %d AND deal_stage = 'Won' AND DATE(created_at) BETWEEN %s AND %s",
                $user->ID, $date_from, $date_to
            ));

            $deals_value = (float)$wpdb->get_var($wpdb->prepare(
                "SELECT SUM(deal_value) FROM $table_deals WHERE assigned_to = %d AND deal_stage = 'Won' AND DATE(created_at) BETWEEN %s AND %s",
                $user->ID, $date_from, $date_to
            ));

            $performance[] = [
                'user_id'        => $user->ID,
                'name'           => $user->display_name,
                'role'           => !empty($user->roles) ? $user->roles[0] : '',
                'leads_assigned' => $leads_assigned,
                'deals_won'      => $deals_won,
                'revenue_value'  => round($deals_value, 2),
            ];
        }

        return $this->success('Team performance report retrieved.', [
            'date_from'   => $date_from,
            'date_to'     => $date_to,
            'performance' => $performance,
        ]);
    }

    /**
     * GET /reports/lead-sources
     */
    public function getLeadSourcesReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'crm_leads';

        $sources = $wpdb->get_results(
            "SELECT lead_source, COUNT(*) as count FROM $table WHERE deleted_at IS NULL GROUP BY lead_source ORDER BY count DESC",
            ARRAY_A
        );

        $total = array_sum(array_column($sources, 'count'));
        foreach ($sources as &$s) {
            $s['percentage'] = $total > 0 ? round(($s['count'] / $total) * 100, 2) : 0;
        }

        return $this->success('Lead sources report retrieved.', [
            'total'   => $total,
            'sources' => $sources,
        ]);
    }

    /**
     * GET /reports/conversion-rate
     */
    public function getConversionRateReport(WP_REST_Request $request) {
        global $wpdb;
        $table_leads = $wpdb->prefix . 'crm_leads';
        $table_deals = $wpdb->prefix . 'crm_deals';
        $params = $request->get_params();
        $date_from = sanitize_text_field($params['date_from'] ?? date('Y-m-01'));
        $date_to   = sanitize_text_field($params['date_to']   ?? date('Y-m-d'));

        $total_leads = (int)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_leads WHERE deleted_at IS NULL AND DATE(created_at) BETWEEN %s AND %s",
            $date_from, $date_to
        ));

        $converted = (int)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_leads WHERE deleted_at IS NULL AND lead_status = 'Won' AND DATE(created_at) BETWEEN %s AND %s",
            $date_from, $date_to
        ));

        $deals_won = (int)$wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_deals WHERE deal_stage = 'Won' AND DATE(created_at) BETWEEN %s AND %s",
            $date_from, $date_to
        ));

        $conversion_rate = $total_leads > 0 ? round(($converted / $total_leads) * 100, 2) : 0;
        $deal_conversion  = $total_leads > 0 ? round(($deals_won / $total_leads) * 100, 2) : 0;

        return $this->success('Conversion rate report retrieved.', [
            'date_from'        => $date_from,
            'date_to'          => $date_to,
            'total_leads'      => $total_leads,
            'leads_won'        => $converted,
            'deals_won'        => $deals_won,
            'lead_conversion'  => $conversion_rate,
            'deal_conversion'  => $deal_conversion,
        ]);
    }

    /**
     * GET /reports/forecast
     */
    public function getForecastReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'crm_deals';

        // Open deals weighted forecast
        $open_stages = ['Prospecting', 'Qualification', 'Proposal', 'Negotiation'];
        $in_clause = implode(',', array_map(function($s) { return "'$s'"; }, $open_stages));

        $deals = $wpdb->get_results(
            "SELECT deal_value, probability, expected_close_date, deal_stage FROM $table WHERE deal_stage IN ($in_clause) AND expected_close_date IS NOT NULL ORDER BY expected_close_date ASC",
            ARRAY_A
        );

        $total_forecast = 0;
        $by_month = [];

        foreach ($deals as $d) {
            $weighted = floatval($d['deal_value']) * (intval($d['probability']) / 100);
            $total_forecast += $weighted;
            $month = date('Y-m', strtotime($d['expected_close_date']));
            $by_month[$month] = ($by_month[$month] ?? 0) + $weighted;
        }

        ksort($by_month);
        $monthly_forecast = [];
        foreach ($by_month as $month => $value) {
            $monthly_forecast[] = ['month' => $month, 'forecast_value' => round($value, 2)];
        }

        return $this->success('Forecast report retrieved.', [
            'total_forecast'   => round($total_forecast, 2),
            'open_deals_count' => count($deals),
            'monthly_forecast' => $monthly_forecast,
        ]);
    }
}
