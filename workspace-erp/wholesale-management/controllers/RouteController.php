<?php
namespace WholesaleErp\Controllers;
use WholesaleErp\Repositories\RouteRepository;
use WP_REST_Request;

if (!defined('ABSPATH')) exit;

class RouteController extends BaseController {
    private $repo;
    public function __construct() {
        $this->repo = new RouteRepository();
    }

    public function getRoutes(WP_REST_Request $request) {
        $searchable = ['route_name', 'area', 'beat_day'];
        $sortable = ['id', 'route_name', 'total_dealers', 'created_at'];
        return $this->success('Routes list.', $this->repo->findAll($request->get_params(), $searchable, $sortable));
    }

    public function getRoute(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repo->findById($id);
        return $item ? $this->success('Route details.', $item) : $this->error('Route not found.', [], 404);
    }

    public function createRoute(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['route_name'])) {
            return $this->error('Route name is required.');
        }
        $data = [
            'route_name'    => $p['route_name'],
            'sales_rep_id'  => !empty($p['sales_rep_id']) ? (int)$p['sales_rep_id'] : null,
            'area'          => $p['area'] ?? '',
            'beat_day'      => $p['beat_day'] ?? '',
            'total_dealers' => isset($p['total_dealers']) ? (int)$p['total_dealers'] : 0,
            'status'        => $p['status'] ?? 'Active',
            'notes'         => $p['notes'] ?? '',
        ];
        $formats = ['%s', '%d', '%s', '%s', '%d', '%s', '%s'];
        $id = $this->repo->create($data, $formats);
        return $id ? $this->success('Route created.', ['id' => $id]) : $this->error('Failed to create route.');
    }

    public function updateRoute(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = [
            'route_name'    => '%s',
            'sales_rep_id'  => '%d',
            'area'          => '%s',
            'beat_day'      => '%s',
            'total_dealers' => '%d',
            'status'        => '%s',
            'notes'         => '%s',
        ];
        $data = [];
        $formats = [];
        foreach ($fields as $f => $fmt) {
            if (isset($p[$f])) {
                $data[$f] = $p[$f];
                $formats[] = $fmt;
            }
        }
        if (empty($data)) {
            return $this->error('No fields to update.');
        }
        return $this->repo->update($id, $data, $formats) ? $this->success('Route updated.') : $this->error('Failed to update route.');
    }

    public function deleteRoute(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        return $this->repo->delete($id) ? $this->success('Route deleted.') : $this->error('Failed to delete route.');
    }
}
