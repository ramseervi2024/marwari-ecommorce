<?php
namespace ManufacturingManagementApi\Controllers;

use ManufacturingManagementApi\Repositories\ProductionRepository;
use ManufacturingManagementApi\Repositories\WorkOrderRepository;
use ManufacturingManagementApi\Repositories\FinishedGoodsRepository;
use ManufacturingManagementApi\Repositories\InventoryRepository;
use ManufacturingManagementApi\Repositories\MachineRepository;
use WP_REST_Request;

class ProductionController extends BaseController {
    private $repo;
    private $woRepo;
    private $fgRepo;
    private $invRepo;
    private $macRepo;

    public function __construct() {
        $this->repo = new ProductionRepository();
        $this->woRepo = new WorkOrderRepository();
        $this->fgRepo = new FinishedGoodsRepository();
        $this->invRepo = new InventoryRepository();
        $this->macRepo = new MachineRepository();
    }

    public function index(WP_REST_Request $request) {
        $items = $this->repo->all();
        foreach ($items as &$item) {
            $product = $this->fgRepo->find(intval($item['product_id']));
            $item['product_name'] = $product ? $product['product_name'] : 'Unknown';
            $item['product_code'] = $product ? $product['product_code'] : 'Unknown';

            $machine = $this->macRepo->find(intval($item['machine_id']));
            $item['machine_name'] = $machine ? $machine['machine_name'] : 'Unknown';
        }
        return $this->success('Production entries retrieved successfully.', $items);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['work_order_id']) || empty($params['product_id']) || empty($params['quantity_produced'])) {
            return $this->error('Validation failed: work_order_id, product_id, and quantity_produced are required.');
        }

        $wo = $this->woRepo->find(intval($params['work_order_id']));
        if (!$wo) {
            return $this->error('Work order not found.');
        }

        $prod = $this->fgRepo->find(intval($params['product_id']));
        if (!$prod) {
            return $this->error('Finished product not found.');
        }

        $params['quantity_produced'] = floatval($params['quantity_produced']);
        $params['production_date'] = !empty($params['production_date']) ? sanitize_text_field($params['production_date']) : current_time('mysql');
        $params['production_cost'] = floatval($params['production_cost'] ?? 0);
        $params['machine_id'] = !empty($params['machine_id']) ? intval($params['machine_id']) : null;
        $params['operator'] = sanitize_text_field($params['operator'] ?? '');
        $params['status'] = sanitize_text_field($params['status'] ?? 'COMPLETED');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create production log.');
        }

        // Check if related work order should be completed
        $this->woRepo->update($wo['id'], [
            'status' => 'Completed',
            'end_date' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ]);

        return $this->success('Production log recorded successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Production entry not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete production entry.');
        }
        return $this->success('Production entry deleted successfully.');
    }
}
