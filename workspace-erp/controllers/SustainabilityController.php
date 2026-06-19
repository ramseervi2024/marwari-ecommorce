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
}
