<?php
namespace GarmentManagementApi\Controllers;

use GarmentManagementApi\Repositories\CuttingRepository;
use GarmentManagementApi\Repositories\OrderRepository;
use GarmentManagementApi\Repositories\FabricRepository;
use GarmentManagementApi\Repositories\BomRepository;
use GarmentManagementApi\Repositories\InventoryRepository;
use WP_REST_Request;

class CuttingController extends BaseController {
    private $repo;
    private $orderRepo;
    private $fabricRepo;
    private $bomRepo;
    private $invRepo;

    public function __construct() {
        $this->repo = new CuttingRepository();
        $this->orderRepo = new OrderRepository();
        $this->fabricRepo = new FabricRepository();
        $this->bomRepo = new BomRepository();
        $this->invRepo = new InventoryRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Cutting logs retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Cutting record not found.', [], 404);
        }
        return $this->success('Cutting record retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['cutting_number']) || empty($params['order_id']) || empty($params['fabric_id']) || empty($params['planned_pieces'])) {
            return $this->error('Validation failed: cutting_number, order_id, fabric_id, and planned_pieces are required.');
        }

        $params['order_id'] = intval($params['order_id']);
        $params['fabric_id'] = intval($params['fabric_id']);
        $params['layers'] = intval($params['layers'] ?? 1);
        $params['planned_pieces'] = floatval($params['planned_pieces']);
        $params['actual_pieces'] = floatval($params['actual_pieces'] ?? 0);
        $params['wastage_meters'] = floatval($params['wastage_meters'] ?? 0);
        $params['cutting_date'] = sanitize_text_field($params['cutting_date'] ?? current_time('mysql'));
        $params['operator_name'] = sanitize_text_field($params['operator_name'] ?? '');
        $params['status'] = sanitize_text_field($params['status'] ?? 'PENDING');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        // Check if completing instantly
        if ($params['status'] === 'COMPLETED' || $params['status'] === 'Completed') {
            $deduction = $this->verifyAndDeductFabric($params['order_id'], $params['fabric_id'], $params['actual_pieces'], $params['wastage_meters'], $params['cutting_number']);
            if (is_wp_error($deduction)) {
                return $this->error($deduction->get_error_message());
            }
        }

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create cutting record. Ensure cutting_number is unique.');
        }

        return $this->success('Cutting record created successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Cutting record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['order_id'])) $updates['order_id'] = intval($params['order_id']);
        if (isset($params['fabric_id'])) $updates['fabric_id'] = intval($params['fabric_id']);
        if (isset($params['layers'])) $updates['layers'] = intval($params['layers']);
        if (isset($params['planned_pieces'])) $updates['planned_pieces'] = floatval($params['planned_pieces']);
        if (isset($params['actual_pieces'])) $updates['actual_pieces'] = floatval($params['actual_pieces']);
        if (isset($params['wastage_meters'])) $updates['wastage_meters'] = floatval($params['wastage_meters']);
        if (isset($params['cutting_date'])) $updates['cutting_date'] = sanitize_text_field($params['cutting_date']);
        if (isset($params['operator_name'])) $updates['operator_name'] = sanitize_text_field($params['operator_name']);
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        $updates['updated_at'] = current_time('mysql');

        // Transitioning to completed state triggers stock checks and deductions
        $is_completed = (isset($params['status']) && (strcasecmp($params['status'], 'COMPLETED') === 0));
        $was_completed = (strcasecmp($item['status'], 'COMPLETED') === 0);

        if ($is_completed && !$was_completed) {
            $order_id = isset($updates['order_id']) ? $updates['order_id'] : intval($item['order_id']);
            $fabric_id = isset($updates['fabric_id']) ? $updates['fabric_id'] : intval($item['fabric_id']);
            $pieces = isset($updates['actual_pieces']) ? $updates['actual_pieces'] : floatval($item['actual_pieces']);
            $wastage = isset($updates['wastage_meters']) ? $updates['wastage_meters'] : floatval($item['wastage_meters']);
            
            $deduction = $this->verifyAndDeductFabric($order_id, $fabric_id, $pieces, $wastage, $item['cutting_number']);
            if (is_wp_error($deduction)) {
                return $this->error($deduction->get_error_message());
            }
        }

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update cutting record.');
        }

        return $this->success('Cutting record updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Cutting record not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete cutting record.');
        }
        return $this->success('Cutting record deleted successfully.');
    }

    private function verifyAndDeductFabric(int $order_id, int $fabric_id, float $pieces, float $wastage_meters, string $cutting_number) {
        $order = $this->orderRepo->find($order_id);
        if (!$order) {
            return new \WP_Error('order_not_found', 'Target order not found.');
        }

        $fabric = $this->fabricRepo->find($fabric_id);
        if (!$fabric) {
            return new \WP_Error('fabric_not_found', 'Target fabric not found.');
        }

        $req_per_piece = 1.0; // fallback default
        $bom_items = $this->bomRepo->getByProduct($order['style_code']);
        if (empty($bom_items)) {
            $bom_items = $this->bomRepo->getByProduct($order['product_name']);
        }

        if (!empty($bom_items)) {
            $bom = $bom_items[0];
            $req_per_piece = floatval($bom['fabric_requirement']);
        }

        $total_required = ($req_per_piece * $pieces) + $wastage_meters;
        if (floatval($fabric['available_meters']) < $total_required) {
            return new \WP_Error(
                'insufficient_stock',
                "Insufficient fabric stock for '{$fabric['fabric_name']}'. Required: {$total_required} meters, Available: {$fabric['available_meters']} meters."
            );
        }

        // Deduct
        $new_meters = floatval($fabric['available_meters']) - $total_required;
        $this->fabricRepo->update($fabric_id, ['available_meters' => $new_meters]);
        $this->invRepo->logMovement('FABRIC', $fabric_id, 'OUT', $total_required, 'Fabric Cut Run: ' . $cutting_number);

        // Also if wastage meters are above 0, register a wastage record
        if ($wastage_meters > 0) {
            global $wpdb;
            $table_wastage = $wpdb->prefix . 'garment_wastage';
            $cost_impact = $wastage_meters * floatval($fabric['cost_per_meter']);
            $wpdb->insert($table_wastage, [
                'department' => 'Cutting',
                'material_type' => 'Fabric',
                'quantity' => $wastage_meters,
                'reason' => 'Cutting wastage for Run ' . $cutting_number,
                'cost_impact' => $cost_impact,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ]);
        }

        return true;
    }
}
