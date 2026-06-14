<?php
namespace FleetTrackPro\Controllers;

use WP_REST_Request;

class ReportController extends BaseController {
    
    /**
     * Helper to parse start & end date filters
     */
    private function getDateRange(WP_REST_Request $request) {
        $start = $request->get_param('start_date');
        $end = $request->get_param('end_date');
        
        // Default to last 6 months if empty
        if (empty($start)) {
            $start = date('Y-m-d', strtotime('-6 months'));
        }
        if (empty($end)) {
            $end = date('Y-m-d');
        }
        
        return [$start, $end];
    }

    /**
     * GET /reports/profit-loss
     */
    public function getProfitLoss(WP_REST_Request $request) {
        global $wpdb;
        list($start, $end) = $this->getDateRange($request);
        
        $table_trips = $wpdb->prefix . 'fleet_trips';
        $table_expenses = $wpdb->prefix . 'fleet_expenses';

        $revenues = $wpdb->get_results($wpdb->prepare("
            SELECT DATE_FORMAT(trip_date, '%Y-%m') as month, SUM(revenue) as revenue
            FROM $table_trips
            WHERE trip_date BETWEEN %s AND %s AND deleted_at IS NULL
            GROUP BY month
            ORDER BY month ASC
        ", $start, $end), ARRAY_A) ?: [];

        $expenses = $wpdb->get_results($wpdb->prepare("
            SELECT DATE_FORMAT(expense_date, '%Y-%m') as month, SUM(amount) as expenses
            FROM $table_expenses
            WHERE expense_date BETWEEN %s AND %s AND deleted_at IS NULL
            GROUP BY month
            ORDER BY month ASC
        ", $start, $end), ARRAY_A) ?: [];

        $pl_map = [];
        foreach ($revenues as $rev) {
            $pl_map[$rev['month']] = [
                'month' => $rev['month'],
                'revenue' => (float)$rev['revenue'],
                'expenses' => 0.00,
                'profit' => (float)$rev['revenue']
            ];
        }

        foreach ($expenses as $exp) {
            $m = $exp['month'];
            $amt = (float)$exp['expenses'];
            if (isset($pl_map[$m])) {
                $pl_map[$m]['expenses'] = $amt;
                $pl_map[$m]['profit'] = $pl_map[$m]['revenue'] - $amt;
            } else {
                $pl_map[$m] = [
                    'month' => $m,
                    'revenue' => 0.00,
                    'expenses' => $amt,
                    'profit' => -$amt
                ];
            }
        }

        ksort($pl_map);
        return $this->success("Profit & Loss Report from $start to $end", array_values($pl_map));
    }

    /**
     * GET /reports/revenue
     */
    public function getRevenue(WP_REST_Request $request) {
        global $wpdb;
        list($start, $end) = $this->getDateRange($request);
        
        $table_trips = $wpdb->prefix . 'fleet_trips';
        $table_vehicles = $wpdb->prefix . 'fleet_vehicles';
        $table_drivers = $wpdb->prefix . 'fleet_drivers';

        $query = "SELECT t.id, t.trip_date, t.revenue, v.vehicle_number, d.name as driver_name
                  FROM $table_trips t
                  LEFT JOIN $table_vehicles v ON t.vehicle_id = v.id
                  LEFT JOIN $table_drivers d ON t.driver_id = d.id
                  WHERE t.trip_date BETWEEN %s AND %s AND t.deleted_at IS NULL
                  ORDER BY t.trip_date DESC";
                  
        $rows = $wpdb->get_results($wpdb->prepare($query, $start, $end), ARRAY_A) ?: [];
        $total = (float)$wpdb->get_var($wpdb->prepare("SELECT SUM(revenue) FROM $table_trips WHERE trip_date BETWEEN %s AND %s AND deleted_at IS NULL", $start, $end));

        return $this->success("Revenue Statement Report", [
            'total_revenue' => $total,
            'records' => $rows
        ]);
    }

    /**
     * GET /reports/expenses
     */
    public function getExpenses(WP_REST_Request $request) {
        global $wpdb;
        list($start, $end) = $this->getDateRange($request);
        
        $table_expenses = $wpdb->prefix . 'fleet_expenses';
        $table_vehicles = $wpdb->prefix . 'fleet_vehicles';

        $query = "SELECT e.*, v.vehicle_number
                  FROM $table_expenses e
                  LEFT JOIN $table_vehicles v ON e.vehicle_id = v.id
                  WHERE e.expense_date BETWEEN %s AND %s AND e.deleted_at IS NULL
                  ORDER BY e.expense_date DESC";
                  
        $rows = $wpdb->get_results($wpdb->prepare($query, $start, $end), ARRAY_A) ?: [];
        $total = (float)$wpdb->get_var($wpdb->prepare("SELECT SUM(amount) FROM $table_expenses WHERE expense_date BETWEEN %s AND %s AND deleted_at IS NULL", $start, $end));

        // Group by expense type
        $breakdown = $wpdb->get_results($wpdb->prepare("
            SELECT expense_type, SUM(amount) as total_amount
            FROM $table_expenses
            WHERE expense_date BETWEEN %s AND %s AND deleted_at IS NULL
            GROUP BY expense_type
            ORDER BY total_amount DESC
        ", $start, $end), ARRAY_A) ?: [];

        return $this->success("Expenses Statement Report", [
            'total_expenses' => $total,
            'breakdown' => $breakdown,
            'records' => $rows
        ]);
    }

    /**
     * GET /reports/fuel
     */
    public function getFuel(WP_REST_Request $request) {
        global $wpdb;
        list($start, $end) = $this->getDateRange($request);
        
        $table_fuel = $wpdb->prefix . 'fleet_fuel';
        $table_vehicles = $wpdb->prefix . 'fleet_vehicles';

        $query = "SELECT f.*, v.vehicle_number
                  FROM $table_fuel f
                  LEFT JOIN $table_vehicles v ON f.vehicle_id = v.id
                  WHERE f.fuel_date BETWEEN %s AND %s AND f.deleted_at IS NULL
                  ORDER BY f.fuel_date DESC";
                  
        $rows = $wpdb->get_results($wpdb->prepare($query, $start, $end), ARRAY_A) ?: [];
        
        $stats = $wpdb->get_row($wpdb->prepare("
            SELECT SUM(fuel_quantity) as total_liters, SUM(fuel_cost) as total_cost
            FROM $table_fuel
            WHERE fuel_date BETWEEN %s AND %s AND deleted_at IS NULL
        ", $start, $end), ARRAY_A);

        return $this->success("Fuel Consumption Report", [
            'total_liters' => (float)($stats['total_liters'] ?? 0),
            'total_cost' => (float)($stats['total_cost'] ?? 0),
            'records' => $rows
        ]);
    }

    /**
     * GET /reports/vehicle
     */
    public function getVehicleReport(WP_REST_Request $request) {
        global $wpdb;
        list($start, $end) = $this->getDateRange($request);
        
        $table_vehicles = $wpdb->prefix . 'fleet_vehicles';
        $table_trips = $wpdb->prefix . 'fleet_trips';
        $table_expenses = $wpdb->prefix . 'fleet_expenses';

        $revs = $wpdb->get_results($wpdb->prepare("
            SELECT t.vehicle_id, v.vehicle_number, v.vehicle_type,
            COUNT(t.id) as trips_count, 
            SUM(t.distance_travelled) as total_distance, 
            SUM(t.revenue) as total_revenue
            FROM $table_trips t
            LEFT JOIN $table_vehicles v ON t.vehicle_id = v.id
            WHERE t.trip_date BETWEEN %s AND %s AND t.deleted_at IS NULL AND v.deleted_at IS NULL
            GROUP BY t.vehicle_id
        ", $start, $end), ARRAY_A) ?: [];

        $exps = $wpdb->get_results($wpdb->prepare("
            SELECT vehicle_id, SUM(amount) as total_expenses
            FROM $table_expenses
            WHERE expense_date BETWEEN %s AND %s AND deleted_at IS NULL AND vehicle_id IS NOT NULL
            GROUP BY vehicle_id
        ", $start, $end), ARRAY_A) ?: [];

        $exp_map = [];
        foreach ($exps as $e) {
            $exp_map[$e['vehicle_id']] = (float)$e['total_expenses'];
        }

        $records = [];
        foreach ($revs as $r) {
            $vid = $r['vehicle_id'];
            $revenue = (float)$r['total_revenue'];
            $expenses = isset($exp_map[$vid]) ? $exp_map[$vid] : 0.00;
            $dist = (float)$r['total_distance'];

            $records[] = [
                'vehicle_id' => $vid,
                'vehicle_number' => $r['vehicle_number'],
                'vehicle_type' => $r['vehicle_type'],
                'trips_count' => (int)$r['trips_count'],
                'total_distance_km' => $dist,
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $revenue - $expenses,
                'cost_per_km' => $dist > 0 ? round($expenses / $dist, 2) : 0.00
            ];
        }

        return $this->success("Vehicle Performance and Cost Analysis Report", $records);
    }

    /**
     * GET /reports/driver
     */
    public function getDriverReport(WP_REST_Request $request) {
        global $wpdb;
        list($start, $end) = $this->getDateRange($request);
        
        $table_drivers = $wpdb->prefix . 'fleet_drivers';
        $table_trips = $wpdb->prefix . 'fleet_trips';

        $records = $wpdb->get_results($wpdb->prepare("
            SELECT t.driver_id, d.name as driver_name, d.phone, d.salary as monthly_salary,
            COUNT(t.id) as trips_count, 
            SUM(t.distance_travelled) as total_distance_km, 
            SUM(t.revenue) as total_revenue,
            AVG(t.revenue) as avg_revenue_per_trip
            FROM $table_trips t
            LEFT JOIN $table_drivers d ON t.driver_id = d.id
            WHERE t.trip_date BETWEEN %s AND %s AND t.deleted_at IS NULL AND d.deleted_at IS NULL
            GROUP BY t.driver_id
            ORDER BY total_revenue DESC
        ", $start, $end), ARRAY_A) ?: [];

        return $this->success("Driver Utilization and Performance Report", $records);
    }

    /**
     * GET /reports/trips
     */
    public function getTrips(WP_REST_Request $request) {
        global $wpdb;
        list($start, $end) = $this->getDateRange($request);
        
        $table_trips = $wpdb->prefix . 'fleet_trips';
        $table_vehicles = $wpdb->prefix . 'fleet_vehicles';
        $table_drivers = $wpdb->prefix . 'fleet_drivers';
        $table_routes = $wpdb->prefix . 'fleet_routes';

        $query = "SELECT t.*, v.vehicle_number, d.name as driver_name, r.route_name
                  FROM $table_trips t
                  LEFT JOIN $table_vehicles v ON t.vehicle_id = v.id
                  LEFT JOIN $table_drivers d ON t.driver_id = d.id
                  LEFT JOIN $table_routes r ON t.route_id = r.id
                  WHERE t.trip_date BETWEEN %s AND %s AND t.deleted_at IS NULL
                  ORDER BY t.trip_date DESC";
                  
        $rows = $wpdb->get_results($wpdb->prepare($query, $start, $end), ARRAY_A) ?: [];

        return $this->success("Detailed Trips History Report", $rows);
    }
}
