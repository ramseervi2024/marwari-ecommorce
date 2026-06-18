<?php
namespace GymErpApi\Controllers;
use GymErpApi\Repositories\EquipmentRepository;
use WP_REST_Request;

class EquipmentController extends BaseController {
    private $repo;
    public function __construct() { $this->repo = new EquipmentRepository(); }

    public function getEquipment(WP_REST_Request $request) {
        return $this->success('Equipment list.', $this->repo->findAll($request->get_params(), ['name','category','brand','location']));
    }

    public function getEquipmentItem(WP_REST_Request $request) {
        $item = $this->repo->findById((int)$request['id']);
        return $item ? $this->success('Equipment details.', $item) : $this->error('Not found.', [], 404);
    }

    public function getSummary(WP_REST_Request $request) {
        return $this->success('Equipment summary.', $this->repo->getSummary());
    }

    public function createEquipment(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['name'])) return $this->error('Equipment name is required.');

        $data = [
            'name'                  => sanitize_text_field($p['name']),
            'category'              => sanitize_text_field($p['category'] ?? 'General'),
            'brand'                 => sanitize_text_field($p['brand'] ?? ''),
            'model_number'          => sanitize_text_field($p['model_number'] ?? ''),
            'serial_number'         => sanitize_text_field($p['serial_number'] ?? ''),
            'purchase_date'         => $p['purchase_date'] ?? null,
            'purchase_price'        => (float)($p['purchase_price'] ?? 0),
            'warranty_expiry'       => $p['warranty_expiry'] ?? null,
            'location'              => sanitize_text_field($p['location'] ?? ''),
            'condition_status'      => sanitize_text_field($p['condition_status'] ?? 'Good'),
            'last_maintenance_date' => $p['last_maintenance_date'] ?? null,
            'next_maintenance_date' => $p['next_maintenance_date'] ?? null,
            'maintenance_notes'     => sanitize_textarea_field($p['maintenance_notes'] ?? ''),
            'status'                => 'Active'
        ];
        $formats = ['%s','%s','%s','%s','%s','%s','%f','%s','%s','%s','%s','%s','%s','%s'];

        $id = $this->repo->create($data, $formats);
        return $id ? $this->success('Equipment added.', ['id' => $id], 201) : $this->error('Failed to add.');
    }

    public function updateEquipment(WP_REST_Request $request) {
        $p = $request->get_json_params();
        $id = (int)$request['id'];
        if (!$this->repo->findById($id)) return $this->error('Not found.', [], 404);

        $data = [];
        $formats = [];
        $str_fields = ['name','category','brand','model_number','serial_number','location','condition_status','maintenance_notes','status','purchase_date','warranty_expiry','last_maintenance_date','next_maintenance_date'];
        foreach ($str_fields as $f) {
            if (isset($p[$f])) { $data[$f] = $p[$f]; $formats[] = '%s'; }
        }
        if (isset($p['purchase_price'])) { $data['purchase_price'] = (float)$p['purchase_price']; $formats[] = '%f'; }

        return $this->repo->update($id, $data, $formats) ? $this->success('Updated.') : $this->error('Update failed.');
    }

    public function deleteEquipment(WP_REST_Request $request) {
        return $this->repo->delete((int)$request['id']) ? $this->success('Deleted.') : $this->error('Delete failed.');
    }

    public function logMaintenance(WP_REST_Request $request) {
        $p = $request->get_json_params();
        $id = (int)$request['id'];
        $item = $this->repo->findById($id);
        if (!$item) return $this->error('Equipment not found.', [], 404);

        $data = [
            'last_maintenance_date' => current_time('Y-m-d'),
            'next_maintenance_date' => $p['next_maintenance_date'] ?? null,
            'condition_status'      => sanitize_text_field($p['condition_status'] ?? $item['condition_status']),
            'maintenance_notes'     => sanitize_textarea_field($p['maintenance_notes'] ?? $item['maintenance_notes']),
        ];
        return $this->repo->update($id, $data, ['%s','%s','%s','%s'])
            ? $this->success('Maintenance logged.')
            : $this->error('Failed to log maintenance.');
    }
}
