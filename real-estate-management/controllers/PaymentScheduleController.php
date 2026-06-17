<?php
namespace RealEstateManagementApi\Controllers;

use RealEstateManagementApi\Repositories\PaymentScheduleRepository;
use RealEstateManagementApi\Repositories\BookingRepository;
use RealEstateManagementApi\Services\AuthService;
use WP_REST_Request;

class PaymentScheduleController extends BaseController {
    private $paymentScheduleRepository;
    private $bookingRepository;

    public function __construct() {
        $this->paymentScheduleRepository = new PaymentScheduleRepository();
        $this->bookingRepository = new BookingRepository();
    }

    /**
     * GET /payment-schedules
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'booking_id', 'due_date', 'amount', 'paid_amount', 'balance_amount', 'payment_status', 'created_at'];
        $search_fields = ['installment_name', 'payment_status'];
        
        $extra_filters = [];
        if (isset($params['booking_id'])) {
            $extra_filters['booking_id'] = intval($params['booking_id']);
        }
        if (isset($params['payment_status'])) {
            $extra_filters['payment_status'] = sanitize_text_field($params['payment_status']);
        }

        $results = $this->paymentScheduleRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        
        // Enrich data with booking details
        if (!empty($results['data'])) {
            foreach ($results['data'] as &$row) {
                $bkg = $this->bookingRepository->findById(intval($row['booking_id']));
                $row['booking_number'] = $bkg ? $bkg['booking_number'] : '';
            }
        }

        return $this->success('Payment schedules retrieved successfully.', $results);
    }

    /**
     * GET /payment-schedules/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $schedule = $this->paymentScheduleRepository->findById($id);

        if (!$schedule) {
            return $this->error('Payment schedule not found.', [], 404);
        }

        return $this->success('Payment schedule retrieved successfully.', $schedule);
    }

    /**
     * POST /payment-schedules
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['booking_id']) || empty($params['installment_name']) || empty($params['due_date']) || empty($params['amount'])) {
            return $this->error('Validation failed: booking_id, installment_name, due_date, and amount are required.');
        }

        $booking_id = intval($params['booking_id']);
        $amount = floatval($params['amount']);
        $paid_amount = isset($params['paid_amount']) ? floatval($params['paid_amount']) : 0.00;
        $balance_amount = $amount - $paid_amount;
        
        $payment_status = 'Pending';
        if ($paid_amount >= $amount) {
            $payment_status = 'Paid';
        } elseif ($paid_amount > 0) {
            $payment_status = 'Partially Paid';
        }

        $data = [
            'booking_id' => $booking_id,
            'installment_name' => sanitize_text_field($params['installment_name']),
            'due_date' => sanitize_text_field($params['due_date']),
            'amount' => $amount,
            'paid_amount' => $paid_amount,
            'balance_amount' => $balance_amount,
            'payment_status' => sanitize_text_field($params['payment_status'] ?? $payment_status)
        ];

        $formats = ['%d', '%s', '%s', '%f', '%f', '%f', '%s'];
        $inserted_id = $this->paymentScheduleRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create payment schedule.');
        }

        AuthService::logActivity(get_current_user_id(), 'PAYMENT_SCHEDULE_CREATE', "Created payment schedule $params[installment_name] ($inserted_id) for booking $booking_id");

        return $this->success('Payment schedule created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /payment-schedules/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $schedule = $this->paymentScheduleRepository->findById($id);

        if (!$schedule) {
            return $this->error('Payment schedule not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['installment_name', 'due_date', 'amount', 'paid_amount', 'payment_status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'amount' || $field === 'paid_amount') {
                    $data[$field] = floatval($params[$field]);
                    $formats[] = '%f';
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                    $formats[] = '%s';
                }
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        // Recalculate balance if amount or paid_amount is changing
        $new_amount = isset($data['amount']) ? $data['amount'] : floatval($schedule['amount']);
        $new_paid = isset($data['paid_amount']) ? $data['paid_amount'] : floatval($schedule['paid_amount']);
        $data['balance_amount'] = $new_amount - $new_paid;
        $formats[] = '%f';

        // Auto adjust status if not set
        if (!isset($data['payment_status'])) {
            if ($new_paid >= $new_amount) {
                $data['payment_status'] = 'Paid';
            } elseif ($new_paid > 0) {
                $data['payment_status'] = 'Partially Paid';
            } else {
                $data['payment_status'] = 'Pending';
            }
            $formats[] = '%s';
        }

        $updated = $this->paymentScheduleRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update payment schedule.');
        }

        AuthService::logActivity(get_current_user_id(), 'PAYMENT_SCHEDULE_UPDATE', "Updated payment schedule ID: $id");

        return $this->success('Payment schedule updated successfully.', $this->paymentScheduleRepository->findById($id));
    }

    /**
     * DELETE /payment-schedules/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $schedule = $this->paymentScheduleRepository->findById($id);

        if (!$schedule) {
            return $this->error('Payment schedule not found.', [], 404);
        }

        $deleted = $this->paymentScheduleRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete payment schedule.');
        }

        AuthService::logActivity(get_current_user_id(), 'PAYMENT_SCHEDULE_DELETE', "Soft deleted payment schedule ID: $id");

        return $this->success('Payment schedule deleted successfully.');
    }
}
