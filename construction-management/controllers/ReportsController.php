<?php
namespace ConstructionManagementApi\Controllers;

use WP_REST_Request;

class ReportsController extends BaseController {

    /**
     * GET /reports/project-cost
     */
    public function getProjectCost(WP_REST_Request $request) {
        global $wpdb;
        $project_id = intval($request->get_param('project_id'));

        if (!$project_id) {
            return $this->error('Validation failed: project_id is required.');
        }

        $prefix = $wpdb->prefix . 'construction_';
        $project = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$prefix}projects WHERE id = %d AND deleted_at IS NULL", $project_id), ARRAY_A);
        
        if (!$project) {
            return $this->error('Project not found.', [], 404);
        }

        // 1. Material Cost: Sum of approved purchases
        $material_cost = floatval($wpdb->get_var($wpdb->prepare(
            "SELECT SUM(total_amount) FROM {$prefix}purchases WHERE project_id = %d AND status = 'Approved' AND deleted_at IS NULL",
            $project_id
        )));

        // 2. Labour Cost: Sum of payroll earnings
        $labour_cost = floatval($wpdb->get_var($wpdb->prepare(
            "SELECT SUM(total_earnings) FROM {$prefix}payroll WHERE project_id = %d AND deleted_at IS NULL",
            $project_id
        )));

        // 3. Equipment Cost: Sum of Equipment Rent type in site expenses
        $equipment_cost = floatval($wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM {$prefix}site_expenses WHERE project_id = %d AND expense_type = 'Equipment Rent' AND deleted_at IS NULL",
            $project_id
        )));

        // 4. Contractor Cost: Sum of Contractor payments in site expenses
        $contractor_cost = floatval($wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM {$prefix}site_expenses WHERE project_id = %d AND expense_type IN ('Contractor Payment', 'Civil Work', 'Electrical Work', 'Plumbing') AND deleted_at IS NULL",
            $project_id
        )));

        // 5. Site Expenses: Sum of other expenses (excluding Equipment Rent & Contractor payments)
        $other_expenses = floatval($wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM {$prefix}site_expenses 
             WHERE project_id = %d 
               AND expense_type NOT IN ('Equipment Rent', 'Contractor Payment', 'Civil Work', 'Electrical Work', 'Plumbing') 
               AND deleted_at IS NULL",
            $project_id
        )));

        $total_cost = $material_cost + $labour_cost + $equipment_cost + $contractor_cost + $other_expenses;

        // Proactively update project's actual cost in the DB
        $wpdb->update($prefix . 'projects', ['actual_cost' => $total_cost], ['id' => $project_id], ['%f'], ['%d']);

        $breakdown = [
            'project_id' => $project_id,
            'project_name' => $project['project_name'],
            'project_code' => $project['project_code'],
            'estimated_cost' => floatval($project['estimated_cost']),
            'actual_cost' => $total_cost,
            'breakdown' => [
                'materials' => $material_cost,
                'labour' => $labour_cost,
                'equipment_rent' => $equipment_cost,
                'contractors' => $contractor_cost,
                'site_miscellaneous' => $other_expenses
            ]
        ];

        return $this->success('Project costing breakdown loaded successfully.', $breakdown);
    }

    /**
     * GET /reports/profitability
     */
    public function getProfitability(WP_REST_Request $request) {
        global $wpdb;
        $prefix = $wpdb->prefix . 'construction_';

        $projects = $wpdb->get_results("SELECT id, project_code, project_name, estimated_cost, status FROM {$prefix}projects WHERE deleted_at IS NULL", ARRAY_A);
        
        $profitability = [];
        foreach ($projects as $proj) {
            $proj_id = intval($proj['id']);

            // Fetch invoice amount billed and paid
            $billed_amount = floatval($wpdb->get_var($wpdb->prepare(
                "SELECT SUM(invoice_amount) FROM {$prefix}billing WHERE project_id = %d AND deleted_at IS NULL",
                $proj_id
            )));

            $received_amount = floatval($wpdb->get_var($wpdb->prepare(
                "SELECT SUM(invoice_amount) FROM {$prefix}billing WHERE project_id = %d AND payment_status = 'PAID' AND deleted_at IS NULL",
                $proj_id
            )));

            // Fetch actual cost breakdown
            $material_cost = floatval($wpdb->get_var($wpdb->prepare("SELECT SUM(total_amount) FROM {$prefix}purchases WHERE project_id = %d AND status = 'Approved' AND deleted_at IS NULL", $proj_id)));
            $labour_cost = floatval($wpdb->get_var($wpdb->prepare("SELECT SUM(total_earnings) FROM {$prefix}payroll WHERE project_id = %d AND deleted_at IS NULL", $proj_id)));
            $expense_cost = floatval($wpdb->get_var($wpdb->prepare("SELECT SUM(amount) FROM {$prefix}site_expenses WHERE project_id = %d AND deleted_at IS NULL", $proj_id)));
            $actual_cost = $material_cost + $labour_cost + $expense_cost;

            $profit_estimated = floatval($proj['estimated_cost']) - $actual_cost;
            $profit_realized = $received_amount - $actual_cost;

            $profitability[] = [
                'project_id' => $proj_id,
                'project_name' => $proj['project_name'],
                'project_code' => $proj['project_code'],
                'status' => $proj['status'],
                'estimated_value' => floatval($proj['estimated_cost']),
                'total_billed' => $billed_amount,
                'total_received' => $received_amount,
                'total_cost' => $actual_cost,
                'estimated_profit' => $profit_estimated,
                'realized_profit' => $profit_realized,
                'profit_margin_percentage' => $billed_amount > 0 ? round(($profit_realized / $billed_amount) * 100, 2) : 0.00
            ];
        }

        return $this->success('Profitability analysis reports loaded.', $profitability);
    }

    /**
     * GET /reports/budget-vs-actual
     */
    public function getBudgetVsActual(WP_REST_Request $request) {
        global $wpdb;
        $prefix = $wpdb->prefix . 'construction_';

        $projects = $wpdb->get_results("SELECT id, project_code, project_name, estimated_cost, status FROM {$prefix}projects WHERE deleted_at IS NULL", ARRAY_A);
        
        $budget_report = [];
        foreach ($projects as $proj) {
            $proj_id = intval($proj['id']);

            // Sum up material purchases, labour payroll, and site expenses
            $material_cost = floatval($wpdb->get_var($wpdb->prepare("SELECT SUM(total_amount) FROM {$prefix}purchases WHERE project_id = %d AND status = 'Approved' AND deleted_at IS NULL", $proj_id)));
            $labour_cost = floatval($wpdb->get_var($wpdb->prepare("SELECT SUM(total_earnings) FROM {$prefix}payroll WHERE project_id = %d AND deleted_at IS NULL", $proj_id)));
            $expense_cost = floatval($wpdb->get_var($wpdb->prepare("SELECT SUM(amount) FROM {$prefix}site_expenses WHERE project_id = %d AND deleted_at IS NULL", $proj_id)));
            
            $actual_cost = $material_cost + $labour_cost + $expense_cost;
            $budget = floatval($proj['estimated_cost']);
            $variance = $budget - $actual_cost;
            $percentage_consumed = $budget > 0 ? round(($actual_cost / $budget) * 100, 2) : 0.00;

            $budget_report[] = [
                'project_id' => $proj_id,
                'project_name' => $proj['project_name'],
                'project_code' => $proj['project_code'],
                'status' => $proj['status'],
                'budget' => $budget,
                'actual' => $actual_cost,
                'variance' => $variance,
                'percentage_consumed' => $percentage_consumed,
                'over_budget' => $variance < 0
            ];
        }

        return $this->success('Budget vs Actual costs report loaded.', $budget_report);
    }
}
