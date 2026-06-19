<?php
namespace WholesaleErp\Routes;
use WholesaleErp\Controllers\ReportsController;
use WholesaleErp\Middleware\AuthMiddleware;

if (!defined('ABSPATH')) exit;

class ReportsRoutes {
    public static function register() {
        $ns = 'wholesale/v1';
        $ctrl = new ReportsController();
        
        $reports = [
            'dealers'      => 'getDealersReport',
            'orders'       => 'getOrdersReport',
            'sales'        => 'getSalesReport',
            'collections'  => 'getCollectionsReport',
            'outstanding'  => 'getOutstandingReport',
            'inventory'    => 'getInventoryReport',
            'dispatches'   => 'getDispatchesReport',
            'gst'          => 'getGstReport',
            'targets'      => 'getTargetsReport',
            'profit-loss'  => 'getProfitLossReport',
        ];

        foreach ($reports as $route => $method) {
            register_rest_route($ns, '/reports/' . $route, [
                'methods' => 'GET',
                'callback' => [$ctrl, $method],
                'permission_callback' => [AuthMiddleware::class, 'authenticate']
            ]);
        }
    }
}
