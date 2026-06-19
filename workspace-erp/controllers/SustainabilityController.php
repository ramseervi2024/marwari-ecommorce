<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\EnergyUsageRepository;
use WP_REST_Request;

class SustainabilityController extends BaseController {
    private $energyRepo;

    public function __construct() {
        $this->energyRepo = new EnergyUsageRepository();
    }

    public function indexEnergy(WP_REST_Request $request) {
        return $this->success('Energy metrics fetched successfully', $this->energyRepo->findAll($request->get_params(), ['id', 'reading_date'], []));
    }

    public function createEnergy(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['building_id']) || empty($params['consumption_kwh'])) {
            return $this->error('building_id and consumption_kwh are required.');
        }

        $data = [
            'building_id' => intval($params['building_id']),
            'reading_date' => isset($params['reading_date']) ? sanitize_text_field($params['reading_date']) : current_time('Y-m-d'),
            'consumption_kwh' => floatval($params['consumption_kwh']),
            'cost' => isset($params['cost']) ? floatval($params['cost']) : 0.00,
            'source' => isset($params['source']) ? sanitize_text_field($params['source']) : 'GRID',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $this->energyRepo->create($data, ['%d', '%s', '%f', '%f', '%s', '%s', '%s', '%s']);
        return $this->success('Energy reading logged successfully', array_merge(['id' => $id], $data), 201);
    }

    public function updateEnergy(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $energy = $this->energyRepo->findById($id);
        if (!$energy) return $this->error('Energy reading not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['building_id'])) { $update['building_id'] = intval($params['building_id']); $formats[] = '%d'; }
        if (isset($params['reading_date'])) { $update['reading_date'] = sanitize_text_field($params['reading_date']); $formats[] = '%s'; }
        if (isset($params['consumption_kwh'])) { $update['consumption_kwh'] = floatval($params['consumption_kwh']); $formats[] = '%f'; }
        if (isset($params['cost'])) { $update['cost'] = floatval($params['cost']); $formats[] = '%f'; }
        if (isset($params['source'])) { $update['source'] = sanitize_text_field($params['source']); $formats[] = '%s'; }

        if (empty($update)) return $this->error('No parameters to update.');

        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->energyRepo->update($id, $update, $formats);
        if (!$success) return $this->error('Failed to update energy reading.');

        return $this->success('Energy reading updated successfully', $this->energyRepo->findById($id));
    }

    public function deleteEnergy(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $energy = $this->energyRepo->findById($id);
        if (!$energy) return $this->error('Energy reading not found.', [], 404);

        $this->energyRepo->delete($id);
        return $this->success('Energy reading deleted successfully');
    }
}
