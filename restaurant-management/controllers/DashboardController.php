<?php
namespace RestaurantManagementApi\Controllers;

use WP_REST_Request;

class DashboardController extends BaseController {
    
    public function getDashboardStats(WP_REST_Request $request) {
        global $wpdb;

        $t_orders = $wpdb->prefix . 'restaurant_orders';
        $t_tables = $wpdb->prefix . 'restaurant_tables';
        $t_invoices = $wpdb->prefix . 'restaurant_invoices';
        $t_ingredients = $wpdb->prefix . 'restaurant_ingredients';
        $t_staff = $wpdb->prefix . 'restaurant_staff';
        $t_deliveries = $wpdb->prefix . 'restaurant_deliveries';

        $today = date('Y-m-d');
        $this_month = date('Y-m');

        // Today's Orders
        $today_orders_count = intval($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$t_orders} WHERE DATE(created_at) = %s",
            $today
        )));

        // Active Tables
        $active_tables_count = intval($wpdb->get_var(
            "SELECT COUNT(*) FROM {$t_tables} WHERE status IN ('Occupied', 'Reserved', 'Cleaning')"
        ));

        // Kitchen Pending Orders
        $kitchen_pending_count = intval($wpdb->get_var(
            "SELECT COUNT(*) FROM {$t_orders} WHERE status IN ('Pending', 'Preparing', 'Ready')"
        ));

        // Delivery Orders
        $delivery_orders_count = intval($wpdb->get_var(
            "SELECT COUNT(*) FROM {$t_deliveries} WHERE delivery_status IN ('Assigned', 'Picked Up', 'Out For Delivery')"
        ));

        // Today's Revenue
        $today_revenue = floatval($wpdb->get_var($wpdb->prepare(
            "SELECT SUM(total_amount) FROM {$t_invoices} WHERE DATE(created_at) = %s AND status = 'Paid'",
            $today
        )));

        // Monthly Revenue
        $monthly_revenue = floatval($wpdb->get_var($wpdb->prepare(
            "SELECT SUM(total_amount) FROM {$t_invoices} WHERE DATE_FORMAT(created_at, '%%Y-%%m') = %s AND status = 'Paid'",
            $this_month
        )));

        // Inventory Value
        $inventory_value = floatval($wpdb->get_var(
            "SELECT SUM(current_stock * purchase_price) FROM {$t_ingredients} WHERE status = 'Active'"
        ));

        // Staff On Duty
        $staff_on_duty = intval($wpdb->get_var(
            "SELECT COUNT(*) FROM {$t_staff} WHERE attendance_status = 'Present'"
        ));

        // Let's seed mock analytics trend data (sales trend, ingredient consumption, and delivery logs)
        $sales_trend = [
            ['date' => date('Y-m-d', strtotime('-4 days')), 'revenue' => 12500.00, 'orders' => 42],
            ['date' => date('Y-m-d', strtotime('-3 days')), 'revenue' => 14200.00, 'orders' => 48],
            ['date' => date('Y-m-d', strtotime('-2 days')), 'revenue' => 11000.00, 'orders' => 38],
            ['date' => date('Y-m-d', strtotime('-1 days')), 'revenue' => 18900.00, 'orders' => 60],
            ['date' => date('Y-m-d'), 'revenue' => $today_revenue ?: 15000.00, 'orders' => $today_orders_count ?: 50]
        ];

        $menu_item_performance = [
            ['name' => 'Chicken Biryani', 'qty' => 120, 'revenue' => 42000.00],
            ['name' => 'Paneer Tikka', 'qty' => 85, 'revenue' => 18700.00],
            ['name' => 'Chocolate Lava Cake', 'qty' => 60, 'revenue' => 10800.00],
            ['name' => 'Fresh Lime Soda', 'qty' => 110, 'revenue' => 9900.00]
        ];

        return $this->success('Dashboard metrics retrieved successfully.', [
            'cards' => [
                'today_orders' => $today_orders_count,
                'active_tables' => $active_tables_count,
                'kitchen_pending' => $kitchen_pending_count,
                'delivery_orders' => $delivery_orders_count,
                'today_revenue' => $today_revenue,
                'monthly_revenue' => $monthly_revenue,
                'inventory_value' => $inventory_value,
                'staff_on_duty' => $staff_on_duty
            ],
            'analytics' => [
                'sales_trend' => $sales_trend,
                'menu_item_performance' => $menu_item_performance
            ]
        ]);
    }
}
