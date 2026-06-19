<?php
namespace WorkspaceErpApi\Controllers;

use WorkspaceErpApi\Repositories\TicketRepository;
use WorkspaceErpApi\Repositories\WorkOrderRepository;
use WorkspaceErpApi\Repositories\MaintenanceScheduleRepository;
use WorkspaceErpApi\Services\AuthService;
use WP_REST_Request;

class FacilityController extends BaseController {
    private $ticketRepo;
    private $workRepo;
    private $maintRepo;

    public function __construct() {
        $this->ticketRepo = new TicketRepository();
        $this->workRepo = new WorkOrderRepository();
        $this->maintRepo = new MaintenanceScheduleRepository();
    }

    public function indexTickets(WP_REST_Request $request) {
        return $this->success('Tickets fetched successfully', $this->ticketRepo->findAll($request->get_params(), ['id', 'ticket_no', 'title', 'status'], ['ticket_no', 'title', 'description']));
    }

    public function createTicket(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['title'])) return $this->error('title is required.');

        $no = 'TKT-' . rand(1000, 9999);
        $data = [
            'ticket_no' => $no,
            'title' => sanitize_text_field($params['title']),
            'description' => isset($params['description']) ? sanitize_textarea_field($params['description']) : '',
            'category' => isset($params['category']) ? sanitize_text_field($params['category']) : 'General',
            'priority' => isset($params['priority']) ? sanitize_text_field($params['priority']) : 'MEDIUM',
            'building_id' => isset($params['building_id']) ? intval($params['building_id']) : null,
            'floor_id' => isset($params['floor_id']) ? intval($params['floor_id']) : null,
            'raised_by' => get_current_user_id(),
            'status' => 'OPEN',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];
        $id = $this->ticketRepo->create($data, ['%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s', '%s', '%s']);
        return $this->success('Ticket raised successfully', array_merge(['id' => $id], $data), 201);
    }

    public function updateTicket(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $ticket = $this->ticketRepo->findById($id);
        if (!$ticket) return $this->error('Ticket not found.', [], 404);

        $params = $request->get_json_params();
        $update = [];
        $formats = [];
        if (isset($params['status'])) { 
            $update['status'] = sanitize_text_field($params['status']); 
            $formats[] = '%s'; 
            if ($update['status'] === 'RESOLVED') {
                $update['resolved_at'] = current_time('mysql');
                $formats[] = '%s';
            }
        }
        if (isset($params['assigned_to'])) { $update['assigned_to'] = intval($params['assigned_to']); $formats[] = '%d'; }
        if (isset($params['title'])) { $update['title'] = sanitize_text_field($params['title']); $formats[] = '%s'; }
        if (isset($params['description'])) { $update['description'] = sanitize_textarea_field($params['description']); $formats[] = '%s'; }
        if (isset($params['category'])) { $update['category'] = sanitize_text_field($params['category']); $formats[] = '%s'; }
        if (isset($params['priority'])) { $update['priority'] = sanitize_text_field($params['priority']); $formats[] = '%s'; }

        if (empty($update)) return $this->error('No fields to update.');
        $update['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $this->ticketRepo->update($id, $update, $formats);
        return $this->success('Ticket updated successfully', $this->ticketRepo->findById($id));
    }

    public function deleteTicket(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $ticket = $this->ticketRepo->findById($id);
        if (!$ticket) return $this->error('Ticket not found.', [], 404);

        $this->ticketRepo->delete($id);
        AuthService::logActivity(get_current_user_id(), 'DELETE_TICKET', "Soft deleted ticket ID: $id");
        return $this->success('Ticket deleted successfully');
    }

    public function indexWorkOrders(WP_REST_Request $request) {
        return $this->success('Work orders fetched successfully', $this->workRepo->findAll($request->get_params(), ['id', 'status'], []));
    }

    public function indexMaintenance(WP_REST_Request $request) {
        return $this->success('Maintenance schedules fetched successfully', $this->maintRepo->findAll($request->get_params(), ['id', 'title'], ['title']));
    }
}
