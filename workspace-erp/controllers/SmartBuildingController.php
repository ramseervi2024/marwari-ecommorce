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

    public function indexSensors(WP_REST_Request $request) {
        return $this->success('Sensor data readings fetched successfully', $this->sensorRepo->findAll($request->get_params(), ['id', 'recorded_at'], []));
    }

    public function indexAccessLogs(WP_REST_Request $request) {
        return $this->success('Access gate logs fetched successfully', $this->accessRepo->findAll($request->get_params(), ['id', 'recorded_at'], ['person_name']));
    }
}
