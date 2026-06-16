<?php
namespace ManufacturingManagementApi\Controllers;

use ManufacturingManagementApi\Repositories\JobWorkRepository;
use ManufacturingManagementApi\Repositories\FinishedGoodsRepository;
use ManufacturingManagementApi\Repositories\InventoryRepository;
use ManufacturingManagementApi\Repositories\SupplierRepository;
use WP_REST_Request;

class JobWorkController extends BaseController {
    private $repo;
    private $fgRepo;
    private $invRepo;
    private $supRepo;

    public function __construct() {
        $this->repo = new JobWorkRepository();
        $this->fgRepo = new FinishedGoodsRepository();
        $this->invRepo = new InventoryRepository();
        $this->supRepo = new SupplierRepository();
    }

    public function index(WP_REST_Request $request) {
        $items = $this->repo->all();
        foreach ($items as &$item) {
            $product = $this->fgRepo->find(intval($item['product_id']));
            $item['product_name'] = $product ? $product['product_name'] : 'Unknown';
            $item['product_code'] = $product ? $product['product_code'] : 'Unknown';

            $vendor = $this->supRepo->find(intval($item['vendor_id']));
            $item['vendor_name'] = $vendor ? $vendor['supplier_name'] : 'Unknown';
        }
        return $this->success('Job work records retrieved successfully.', $items);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['job_work_number']) || empty($params['vendor_id']) || empty($params['product_id']) || empty($params['quantity'])) {
            return $this->error('Validation failed: job_work_number, vendor_id, product_id, and quantity are required.');
        }

        $params['vendor_id'] = intval($params['vendor_id']);
        $params['product_id'] = intval($params['product_id']);
        $params['quantity'] = floatval($params['quantity']);
        $params['job_cost'] = floatval($params['job_cost'] ?? 0);
        $params['dispatch_date'] = !empty($params['dispatch_date']) ? sanitize_text_field($params['dispatch_date']) : current_time('mysql');
        $params['expected_return_date'] = !empty($params['expected_return_date']) ? sanitize_text_field($params['expected_return_date']) : null;
        $params['actual_return_date'] = null;
        $params['status'] = sanitize_text_field($params['status'] ?? 'PENDING');
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create job work. Ensure job_work_number is unique.');
        }

        if ($params['status'] === 'Completed') {
            $prod = $this->fgRepo->find($params['product_id']);
            if ($prod) {
                $new_stock = floatval($prod['quantity']) + $params['quantity'];
                $this->fgRepo->update($prod['id'], ['quantity' => $new_stock]);
                $this->invRepo->logMovement('FINISHED', $prod['id'], 'IN', $params['quantity'], 'Job Completed: ' . $params['job_work_number']);
            }
        }

        return $this->success('Job work registered successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Job work record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['status'])) $updates['status'] = sanitize_text_field($params['status']);
        if (isset($params['job_cost'])) $updates['job_cost'] = floatval($params['job_cost']);
        if (isset($params['actual_return_date'])) $updates['actual_return_date'] = sanitize_text_field($params['actual_return_date']);
        $updates['updated_at'] = current_time('mysql');

        // Transitioning to 'Completed' triggers finished product stock increment
        if (isset($params['status']) && $params['status'] === 'Completed' && $item['status'] !== 'Completed') {
            $prod = $this->fgRepo->find(intval($item['product_id']));
            if ($prod) {
                $new_stock = floatval($prod['quantity']) + floatval($item['quantity']);
                $this->fgRepo->update($prod['id'], ['quantity' => $new_stock]);
                $this->invRepo->logMovement('FINISHED', $prod['id'], 'IN', floatval($item['quantity']), 'Job Completed: ' . $item['job_work_number']);
                $updates['actual_return_date'] = current_time('mysql');
            }
        }

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update job work.');
        }

        return $this->success('Job work updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        if (!$this->repo->find($id)) {
            return $this->error('Job work record not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete job work.');
        }
        return $this->success('Job work deleted successfully.');
    }
}
