<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Repositories\FeeRepository;
use SchoolManagementApi\Services\AuthService;
use WP_REST_Request;

class FeeController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new FeeRepository();
    }

    // --- Structures CRUD ---

    public function getStructures(WP_REST_Request $request) {
        $params = $request->get_params();
        $filters = ['type' => 'STRUCTURE'];
        if (!empty($params['class_id'])) {
            $filters['class_id'] = (int)$params['class_id'];
        }
        $result = $this->repository->findAll($params, ['id', 'title', 'amount', 'due_date'], ['title'], $filters);
        return $this->success('Fee structures fetched successfully', $result);
    }

    public function createStructure(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['title']) || empty($params['amount']) || empty($params['class_id'])) {
            return $this->error('Validation failed: title, amount, and class_id are required.');
        }

        $data = [
            'type' => 'STRUCTURE',
            'student_id' => null,
            'class_id' => (int)$params['class_id'],
            'title' => sanitize_text_field($params['title']),
            'amount' => (float)$params['amount'],
            'due_date' => isset($params['due_date']) ? sanitize_text_field($params['due_date']) : null,
            'status' => 'ACTIVE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $id = $this->repository->create($data, ['%s', '%d', '%d', '%s', '%f', '%s', '%s', '%s', '%s']);
        if (!$id) {
            return $this->error('Failed to create fee structure.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_FEE_STRUCTURE', "Created fee structure ID: $id");
        return $this->success('Fee structure created successfully', array_merge(['id' => $id], $data), 201);
    }

    public function updateStructure(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repository->findById($id);

        if (!$item || $item['type'] !== 'STRUCTURE') {
            return $this->error('Fee structure not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        if (isset($params['title'])) {
            $data['title'] = sanitize_text_field($params['title']);
            $formats[] = '%s';
        }
        if (isset($params['amount'])) {
            $data['amount'] = (float)$params['amount'];
            $formats[] = '%f';
        }
        if (isset($params['due_date'])) {
            $data['due_date'] = sanitize_text_field($params['due_date']);
            $formats[] = '%s';
        }
        if (isset($params['class_id'])) {
            $data['class_id'] = (int)$params['class_id'];
            $formats[] = '%d';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $this->repository->update($id, $data, $formats);
        return $this->success('Fee structure updated successfully', $this->repository->findById($id));
    }

    public function deleteStructure(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $item = $this->repository->findById($id);

        if (!$item || $item['type'] !== 'STRUCTURE') {
            return $this->error('Fee structure not found.', [], 404);
        }

        $this->repository->delete($id);
        return $this->success('Fee structure deleted successfully');
    }

    // --- Fee Collections ---

    public function getCollections(WP_REST_Request $request) {
        $params = $request->get_params();
        $filters = ['type' => 'COLLECTION'];
        if (!empty($params['student_id'])) {
            $filters['student_id'] = (int)$params['student_id'];
        }
        $result = $this->repository->findAll($params, ['id', 'title', 'amount', 'paid_at', 'payment_method'], ['title', 'transaction_id'], $filters);
        return $this->success('Fee collections fetched successfully', $result);
    }

    /**
     * POST /fees/collections (Razorpay / cash mock payments)
     */
    public function collectFee(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['student_id']) || empty($params['title']) || !isset($params['amount'])) {
            return $this->error('Validation failed: student_id, title, and amount are required.');
        }

        $data = [
            'type' => 'COLLECTION',
            'student_id' => (int)$params['student_id'],
            'class_id' => null,
            'title' => sanitize_text_field($params['title']),
            'amount' => (float)$params['amount'],
            'due_date' => null,
            'status' => 'PAID',
            'payment_method' => isset($params['payment_method']) ? sanitize_text_field($params['payment_method']) : 'Cash',
            'transaction_id' => isset($params['transaction_id']) ? sanitize_text_field($params['transaction_id']) : ('TXN_' . bin2hex(random_bytes(6))),
            'paid_at' => current_time('mysql'),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $formats = ['%s', '%d', '%d', '%s', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];
        $id = $this->repository->create($data, $formats);

        if (!$id) {
            return $this->error('Failed to register fee collection.');
        }

        // Generate dynamic mock receipt printable HTML
        $receipt_html = "
        <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px dashed #333; max-width: 400px; margin: auto;'>
            <h3 style='text-align: center; margin: 0; color: #4F46E5;'>FEE PAYMENT RECEIPT</h3>
            <p style='text-align: center; font-size: 12px; color: #777;'>Global International School</p>
            <hr>
            <p><strong>Receipt No:</strong> REC-2026-{$id}</p>
            <p><strong>Student ID:</strong> {$data['student_id']}</p>
            <p><strong>Details:</strong> {$data['title']}</p>
            <p><strong>Amount Paid:</strong> $" . number_format($data['amount'], 2) . "</p>
            <p><strong>Payment Method:</strong> {$data['payment_method']}</p>
            <p><strong>Txn ID:</strong> {$data['transaction_id']}</p>
            <p><strong>Paid Date:</strong> {$data['paid_at']}</p>
            <hr>
            <p style='text-align: center; font-size: 11px;'>Thank you for your payment!</p>
        </div>";

        AuthService::logActivity(get_current_user_id(), 'FEE_COLLECTION', "Collected fee amount: {$data['amount']} from student ID: {$data['student_id']}");
        return $this->success('Fee collection registered successfully', [
            'collection_details' => array_merge(['id' => $id], $data),
            'printable_receipt_html' => base64_encode($receipt_html)
        ], 201);
    }

    // --- Fee Reports ---

    public function getFeeReport(WP_REST_Request $request) {
        global $wpdb;
        $table = $wpdb->prefix . 'school_fees';

        $total_structures = (float)$wpdb->get_var("SELECT SUM(amount) FROM $table WHERE type = 'STRUCTURE' AND deleted_at IS NULL");
        $total_collections = (float)$wpdb->get_var("SELECT SUM(amount) FROM $table WHERE type = 'COLLECTION' AND deleted_at IS NULL");
        
        $pending = $total_structures - $total_collections;
        if ($pending < 0) {
            $pending = 0.00;
        }

        $collections_by_method = $wpdb->get_results("
            SELECT payment_method, SUM(amount) as total 
            FROM $table 
            WHERE type = 'COLLECTION' AND deleted_at IS NULL
            GROUP BY payment_method
        ", ARRAY_A) ?: [];

        return $this->success('Fee report statements fetched successfully', [
            'total_billed' => $total_structures,
            'total_collected' => $total_collections,
            'total_pending' => $pending,
            'breakdown_by_method' => $collections_by_method
        ]);
    }
}
