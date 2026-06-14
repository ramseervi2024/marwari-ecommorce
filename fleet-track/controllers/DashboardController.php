<?php
namespace FleetTrackPro\Controllers;

use WP_REST_Request;

class DashboardController extends BaseController {
    
    /**
     * GET /dashboard
     */
    public function getSummary(WP_REST_Request $request) {
        global $wpdb;
        
        $table_vehicles = $wpdb->prefix . 'fleet_vehicles';
        $table_drivers = $wpdb->prefix . 'fleet_drivers';
        $table_trips = $wpdb->prefix . 'fleet_trips';
        $table_expenses = $wpdb->prefix . 'fleet_expenses';
        $table_fuel = $wpdb->prefix . 'fleet_fuel';
        $table_routes = $wpdb->prefix . 'fleet_routes';

        // 1. Dashboard Cards Data
        $total_vehicles = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_vehicles WHERE deleted_at IS NULL");
        $active_vehicles = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_vehicles WHERE status = 'ACTIVE' AND deleted_at IS NULL");
        
        $total_drivers = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_drivers WHERE deleted_at IS NULL");
        $active_drivers = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_drivers WHERE status = 'ACTIVE' AND deleted_at IS NULL");
        
        $total_trips = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_trips WHERE deleted_at IS NULL");
        $total_revenue = (float)$wpdb->get_var("SELECT SUM(revenue) FROM $table_trips WHERE deleted_at IS NULL");
        $total_expenses = (float)$wpdb->get_var("SELECT SUM(amount) FROM $table_expenses WHERE deleted_at IS NULL");
        $total_profit = $total_revenue - $total_expenses;

        // 2. Monthly Revenue Trend
        $revenue_trend = $wpdb->get_results("
            SELECT DATE_FORMAT(trip_date, '%Y-%m') as month, SUM(revenue) as revenue
            FROM $table_trips
            WHERE deleted_at IS NULL
            GROUP BY month
            ORDER BY month ASC
            LIMIT 12
        ", ARRAY_A) ?: [];

        // 3. Monthly Expense Trend
        $expense_trend = $wpdb->get_results("
            SELECT DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(amount) as expenses
            FROM $table_expenses
            WHERE deleted_at IS NULL
            GROUP BY month
            ORDER BY month ASC
            LIMIT 12
        ", ARRAY_A) ?: [];

        // 4. Fuel Consumption Trend
        $fuel_trend = $wpdb->get_results("
            SELECT DATE_FORMAT(fuel_date, '%Y-%m') as month, SUM(fuel_quantity) as quantity, SUM(fuel_cost) as cost
            FROM $table_fuel
            WHERE deleted_at IS NULL
            GROUP BY month
            ORDER BY month ASC
            LIMIT 12
        ", ARRAY_A) ?: [];

        // 5. Top Profitable Vehicles
        $vehicle_revs = $wpdb->get_results("
            SELECT t.vehicle_id, v.vehicle_number, SUM(t.revenue) as revenue, SUM(t.distance_travelled) as distance
            FROM $table_trips t
            LEFT JOIN $table_vehicles v ON t.vehicle_id = v.id
            WHERE t.deleted_at IS NULL AND v.deleted_at IS NULL
            GROUP BY t.vehicle_id
        ", ARRAY_A) ?: [];

        $vehicle_exps = $wpdb->get_results("
            SELECT vehicle_id, SUM(amount) as expenses
            FROM $table_expenses
            WHERE deleted_at IS NULL AND vehicle_id IS NOT NULL
            GROUP BY vehicle_id
        ", ARRAY_A) ?: [];

        $vehicle_exp_map = [];
        foreach ($vehicle_exps as $vexp) {
            $vehicle_exp_map[$vexp['vehicle_id']] = (float)$vexp['expenses'];
        }

        $vehicle_profitability = [];
        foreach ($vehicle_revs as $vrev) {
            $vid = $vrev['vehicle_id'];
            $rev = (float)$vrev['revenue'];
            $exp = isset($vehicle_exp_map[$vid]) ? $vehicle_exp_map[$vid] : 0.00;
            $dist = (float)$vrev['distance'];
            
            $vehicle_profitability[] = [
                'id' => $vid,
                'vehicle_number' => $vrev['vehicle_number'],
                'revenue' => $rev,
                'expenses' => $exp,
                'profit' => $rev - $exp,
                'cost_per_km' => $dist > 0 ? round($exp / $dist, 2) : 0.00
            ];
        }

        // Sort for top profitable
        usort($vehicle_profitability, function($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });
        $top_profitable_vehicles = array_slice($vehicle_profitability, 0, 5);

        // Sort for top expenses
        $vehicle_top_expenses = $vehicle_profitability;
        usort($vehicle_top_expenses, function($a, $b) {
            return $b['expenses'] <=> $a['expenses'];
        });
        $top_expense_vehicles = array_slice($vehicle_top_expenses, 0, 5);

        // 6. Driver Performance Matrix
        $driver_performance = $wpdb->get_results("
            SELECT t.driver_id, d.name as driver_name, 
            COUNT(t.id) as total_trips, 
            SUM(t.distance_travelled) as total_distance, 
            SUM(t.revenue) as total_revenue,
            AVG(t.revenue) as avg_revenue_per_trip
            FROM $table_trips t
            LEFT JOIN $table_drivers d ON t.driver_id = d.id
            WHERE t.deleted_at IS NULL AND d.deleted_at IS NULL
            GROUP BY t.driver_id
        ", ARRAY_A) ?: [];

        return $this->success('Dashboard summary metrics fetched successfully', [
            'cards' => [
                'total_vehicles' => $total_vehicles,
                'active_vehicles' => $active_vehicles,
                'total_drivers' => $total_drivers,
                'active_drivers' => $active_drivers,
                'total_trips' => $total_trips,
                'total_revenue' => $total_revenue,
                'total_expenses' => $total_expenses,
                'total_profit' => $total_profit
            ],
            'trends' => [
                'revenue' => $revenue_trend,
                'expenses' => $expense_trend,
                'fuel' => $fuel_trend
            ],
            'vehicles' => [
                'top_profitable' => $top_profitable_vehicles,
                'top_expenses' => $top_expense_vehicles
            ],
            'drivers' => $driver_performance
        ]);
    }
}
