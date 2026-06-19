<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\IoTDeviceRepository;
use WorkspaceErpApi\Repositories\SensorDataRepository;
use WorkspaceErpApi\Repositories\AccessLogRepository;
use WP_REST_Request;

class SmartBuildingController extends BaseController {
    private $deviceRepo;
    private $sensorRepo;
    private $accessRepo;

    public function __construct() {
        $this->deviceRepo = new IoTDeviceRepository();
        $this->sensorRepo = new SensorDataRepository();
        $this->accessRepo = new AccessLogRepository();
    }

    public function indexDevices(WP_REST_Request $request) {
        return $this->success('IoT devices fetched successfully', $this->deviceRepo->findAll($request->get_params(), ['id', 'device_name'], ['device_name']));
    }

    public function createDevice(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['device_name']) || empty($params['device_type'])) {
            return $this->error('device_name and device_type are required.');
        }

        $data = [
            'device_name' => sanitize_text_field($params['device_name']),
            'device_type' => sanitize_text_field($params['device_type']),
            'building_id' => isset($params['building_id']) && $params['building_id'] !== '' ? intval($params['building_id']) : null,
            'floor_id' => isset($params['floor_id']) && $params['floor_id'] !== '' ? intval($params['floor_id']) : null,
            'serial_number' => isset($params['serial_number']) ? sanitize_text_field($params['serial_number']) : '',
            'manufacturer' => isset($params['manufacturer']) ? sanitize_text_field($params['manufacturer']) : '',
            'installed_date' => isset($params['installed_date']) && $params['installed_date'] !== '' ? sanitize_text_field($params['installed_date']) : current_time('Y-m-d'),
            'status' => 'ACTIVE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $this->deviceRepo->create($data, ['%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s']);
        if (!$id) return $this->error('Failed to register IoT device.');
        return $this->success('IoT device registered successfully', array_merge(['id' => $id], $data), 201);
    }

    public function updateDevice(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $device = $this->deviceRepo->findById($id);
        if (!$device) return $this->error('IoT device not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['device_name'])) { $update['device_name'] = sanitize_text_field($params['device_name']); $formats[] = '%s'; }
        if (isset($params['device_type'])) { $update['device_type'] = sanitize_text_field($params['device_type']); $formats[] = '%s'; }
        if (isset($params['building_id'])) { $update['building_id'] = $params['building_id'] !== '' ? intval($params['building_id']) : null; $formats[] = '%d'; }
        if (isset($params['floor_id'])) { $update['floor_id'] = $params['floor_id'] !== '' ? intval($params['floor_id']) : null; $formats[] = '%d'; }
        if (isset($params['serial_number'])) { $update['serial_number'] = sanitize_text_field($params['serial_number']); $formats[] = '%s'; }
        if (isset($params['manufacturer'])) { $update['manufacturer'] = sanitize_text_field($params['manufacturer']); $formats[] = '%s'; }
        if (isset($params['installed_date'])) { $update['installed_date'] = $params['installed_date'] !== '' ? sanitize_text_field($params['installed_date']) : null; $formats[] = '%s'; }
        if (isset($params['status'])) { $update['status'] = sanitize_text_field($params['status']); $formats[] = '%s'; }

        if (empty($update)) return $this->error('No parameters to update.');

        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $success = $this->deviceRepo->update($id, $update, $formats);
        if (!$success) return $this->error('Failed to update IoT device.');
        return $this->success('IoT device updated successfully', $this->deviceRepo->findById($id));
    }

    public function deleteDevice(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $device = $this->deviceRepo->findById($id);
        if (!$device) return $this->error('IoT device not found.', [], 404);

        $this->deviceRepo->delete($id);
        return $this->success('IoT device deleted successfully');
    }

    public function indexSensors(WP_REST_Request $request) {
        return $this->success('Sensor data readings fetched successfully', $this->sensorRepo->findAll($request->get_params(), ['id', 'recorded_at'], []));
    }

    public function indexAccessLogs(WP_REST_Request $request) {
        return $this->success('Access gate logs fetched successfully', $this->accessRepo->findAll($request->get_params(), ['id', 'recorded_at'], ['person_name']));
    }
}
