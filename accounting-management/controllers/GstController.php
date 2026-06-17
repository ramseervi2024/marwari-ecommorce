<?php
namespace AccountingManagementApi\Controllers;

use AccountingManagementApi\Repositories\GstRepository;
use WP_REST_Request;

class GstController extends BaseController {
    private $gstRepository;

    public function __construct() {
        $this->gstRepository = new GstRepository();
    }

    /**
     * GET /gst
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'gst_rate', 'gst_amount', 'created_at'];
        $search_fields = ['invoice_type', 'gst_type', 'tax_period'];
        
        $extra_filters = [];
        if (isset($params['tax_period'])) {
            $extra_filters['tax_period'] = sanitize_text_field($params['tax_period']);
        }
        if (isset($params['invoice_type'])) {
            $extra_filters['invoice_type'] = sanitize_text_field($params['invoice_type']);
        }

        $results = $this->gstRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('GST returns summary retrieved.', $results);
    }
}
