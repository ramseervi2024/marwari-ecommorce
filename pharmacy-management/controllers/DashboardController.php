<?php
namespace PharmacyErpApi\Controllers;

use PharmacyErpApi\Repositories\MedicineRepository;
use PharmacyErpApi\Repositories\BatchRepository;
use PharmacyErpApi\Repositories\BillRepository;
use WP_REST_Request;

class DashboardController extends BaseController {
    public function getStats(WP_REST_Request $request) {
        global $wpdb;
        $p = $wpdb->prefix;
        
        $total_medicines = $wpdb->get_var("SELECT COUNT(*) FROM {$p}pharmacy_medicines WHERE deleted_at IS NULL");
        $total_suppliers = $wpdb->get_var("SELECT COUNT(*) FROM {$p}pharmacy_suppliers WHERE deleted_at IS NULL");
        $bills_today = $wpdb->get_var("SELECT COUNT(*) FROM {$p}pharmacy_bills WHERE DATE(bill_date) = CURDATE() AND deleted_at IS NULL");
        $revenue_today = $wpdb->get_var("SELECT COALESCE(SUM(grand_total),0) FROM {$p}pharmacy_bills WHERE DATE(bill_date) = CURDATE() AND deleted_at IS NULL AND status='Paid'");
        
        $medRepo = new MedicineRepository();
        $batchRepo = new BatchRepository();
        $billRepo = new BillRepository();

        $low_stock = $medRepo->getLowStock();
        $expiry_alerts = $batchRepo->getExpiryAlerts(30);
        $recent_bills = $wpdb->get_results("SELECT * FROM {$p}pharmacy_bills WHERE deleted_at IS NULL ORDER BY id DESC LIMIT 5", ARRAY_A);
        $revenue_data = $billRepo->getDailyRevenue();

        return $this->success('Dashboard stats.', [
            'summary' => [
                'total_medicines' => (int)$total_medicines,
                'total_suppliers' => (int)$total_suppliers,
                'bills_today' => (int)$bills_today,
                'revenue_today' => (float)$revenue_today,
                'low_stock_count' => count($low_stock),
                'expiry_alerts_count' => count($expiry_alerts)
            ],
            'low_stock' => array_slice($low_stock, 0, 10),
            'expiry_alerts' => array_slice($expiry_alerts, 0, 10),
            'recent_bills' => $recent_bills,
            'revenue_chart' => $revenue_data
        ]);
    }
}
