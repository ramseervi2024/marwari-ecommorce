<?php
namespace InventoryManagementApi\Controllers;

use WP_REST_Request;

class ReportsController extends BaseController {

    /**
     * GET /reports/stock-valuation
     */
    public function getStockValuationReport(WP_REST_Request $request) {
        global $wpdb;
        $table_products = $wpdb->prefix . 'inv_products';
        $table_stock = $wpdb->prefix . 'inv_stock';

        $query = "
            SELECT 
                p.id as product_id, 
                p.sku, 
                p.product_name, 
                p.category,
                p.unit, 
                p.purchase_price,
                p.selling_price,
                COALESCE(SUM(s.available_stock), 0) as total_available_stock,
                (COALESCE(SUM(s.available_stock), 0) * p.purchase_price) as total_valuation
            FROM $table_products p
            LEFT JOIN $table_stock s ON p.id = s.product_id
            WHERE p.deleted_at IS NULL AND p.status = 'ACTIVE'
            GROUP BY p.id
            ORDER BY total_valuation DESC
        ";

        $results = $wpdb->get_results($query, ARRAY_A) ?: [];

        return $this->success('Stock valuation report retrieved.', $results);
    }

    /**
     * GET /reports/low-stock
     */
    public function getLowStockReport(WP_REST_Request $request) {
        global $wpdb;
        $table_products = $wpdb->prefix . 'inv_products';
        $table_stock = $wpdb->prefix . 'inv_stock';

        $query = "
            SELECT 
                p.id as product_id, 
                p.sku, 
                p.product_name, 
                p.category,
                p.unit, 
                p.minimum_stock,
                COALESCE(SUM(s.available_stock), 0) as total_available_stock
            FROM $table_products p
            LEFT JOIN $table_stock s ON p.id = s.product_id
            WHERE p.deleted_at IS NULL AND p.status = 'ACTIVE'
            GROUP BY p.id
            HAVING total_available_stock < p.minimum_stock
            ORDER BY total_available_stock ASC
        ";

        $results = $wpdb->get_results($query, ARRAY_A) ?: [];

        return $this->success('Low stock alert report retrieved.', $results);
    }

    /**
     * GET /reports/stock-movements
     */
    public function getStockMovementReport(WP_REST_Request $request) {
        global $wpdb;
        
        $table_inward = $wpdb->prefix . 'inv_stock_inward';
        $table_inward_items = $wpdb->prefix . 'inv_inward_items';
        $table_outward = $wpdb->prefix . 'inv_stock_outward';
        $table_outward_items = $wpdb->prefix . 'inv_outward_items';
        $table_products = $wpdb->prefix . 'inv_products';
        $table_warehouses = $wpdb->prefix . 'inv_warehouses';

        // Fetch inward movements
        $inward_query = "
            SELECT 
                i.inward_date as movement_date,
                'INWARD' as movement_type,
                i.reference_type,
                i.reference_id,
                p.sku,
                p.product_name,
                w.warehouse_name,
                ii.quantity,
                i.remarks
            FROM $table_inward i
            JOIN $table_inward_items ii ON i.id = ii.inward_id
            JOIN $table_products p ON ii.product_id = p.id
            JOIN $table_warehouses w ON ii.warehouse_id = w.id
        ";

        // Fetch outward movements
        $outward_query = "
            SELECT 
                o.outward_date as movement_date,
                'OUTWARD' as movement_type,
                o.reference_type,
                o.reference_id,
                p.sku,
                p.product_name,
                w.warehouse_name,
                oi.quantity,
                o.remarks
            FROM $table_outward o
            JOIN $table_outward_items oi ON o.id = oi.outward_id
            JOIN $table_products p ON oi.product_id = p.id
            JOIN $table_warehouses w ON oi.warehouse_id = w.id
        ";

        $union_query = "
            ($inward_query)
            UNION ALL
            ($outward_query)
            ORDER BY movement_date DESC
            LIMIT 100
        ";

        $results = $wpdb->get_results($union_query, ARRAY_A) ?: [];

        return $this->success('Stock movements report retrieved.', $results);
    }

    /**
     * GET /reports/audit-variances
     */
    public function getAuditVarianceReport(WP_REST_Request $request) {
        global $wpdb;
        $table_audits = $wpdb->prefix . 'inv_audits';
        $table_audit_items = $wpdb->prefix . 'inv_audit_items';
        $table_products = $wpdb->prefix . 'inv_products';
        $table_warehouses = $wpdb->prefix . 'inv_warehouses';

        $query = "
            SELECT 
                a.audit_number,
                a.audit_date,
                w.warehouse_name,
                p.sku,
                p.product_name,
                ai.system_quantity,
                ai.physical_quantity,
                ai.variance
            FROM $table_audits a
            JOIN $table_audit_items ai ON a.id = ai.audit_id
            JOIN $table_products p ON ai.product_id = p.id
            JOIN $table_warehouses w ON a.warehouse_id = w.id
            WHERE a.status = 'Completed' AND a.deleted_at IS NULL
            ORDER BY a.audit_date DESC, ai.id ASC
        ";

        $results = $wpdb->get_results($query, ARRAY_A) ?: [];

        return $this->success('Audit variance report retrieved.', $results);
    }
}
