<?php
namespace JewelleryManagementApi\Controllers;

use JewelleryManagementApi\Repositories\InventoryRepository;
use WP_REST_Request;

class BarcodeController extends BaseController {
    private $inventoryRepo;

    public function __construct() {
        $this->inventoryRepo = new InventoryRepository();
    }

    /**
     * Look up finished item by barcode scanned
     */
    public function scan(WP_REST_Request $request) {
        $barcode = sanitize_text_field($request->get_param('barcode') ?: '');
        if (empty($barcode)) {
            return $this->error('Barcode is required.');
        }

        $item = $this->inventoryRepo->getByBarcode($barcode);
        if (!$item) {
            return $this->error('No ornament found matching barcode: ' . $barcode, [], 404);
        }

        return $this->success('Ornament scanned successfully.', $item);
    }

    /**
     * Generate barcode label printable configurations
     */
    public function printLabel(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->inventoryRepo->find($id);
        if (!$item) {
            return $this->error('Item not found.', [], 404);
        }

        // Return label parameters for printing machine interface
        return $this->success('Print configuration generated.', [
            'barcode' => $item['barcode'],
            'product_name' => $item['product_name'],
            'category' => $item['category'],
            'purity' => $item['purity'],
            'gross_weight' => $item['gross_weight'] . 'g',
            'net_weight' => $item['net_weight'] . 'g',
            'hallmark' => $item['hallmark_number'] ?: 'N/A',
            'label_width_mm' => 50,
            'label_height_mm' => 25,
            'dpi' => 300
        ]);
    }
}
