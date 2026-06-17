<?php
namespace RealEstateManagementApi\Controllers;

use RealEstateManagementApi\Repositories\CommissionRepository;
use RealEstateManagementApi\Repositories\BrokerRepository;
use RealEstateManagementApi\Repositories\BookingRepository;
use RealEstateManagementApi\Services\AuthService;
use WP_REST_Request;

class CommissionController extends BaseController {
    private $commissionRepository;
    private $brokerRepository;
    private $bookingRepository;

    public function __construct() {
        $this->commissionRepository = new CommissionRepository();
        $this->brokerRepository = new BrokerRepository();
        $this->bookingRepository = new BookingRepository();
    }

    /**
     * GET /commissions
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'broker_id', 'booking_id', 'commission_amount', 'paid_amount', 'balance_amount', 'payment_status', 'created_at'];
        $search_fields = ['payment_status'];
        
        $extra_filters = [];
        if (isset($params['broker_id'])) {
            $extra_filters['broker_id'] = intval($params['broker_id']);
        }
        if (isset($params['booking_id'])) {
            $extra_filters['booking_id'] = intval($params['booking_id']);
        }
        if (isset($params['payment_status'])) {
            $extra_filters['payment_status'] = sanitize_text_field($params['payment_status']);
        }

        $results = $this->commissionRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        
        // Enrich data with broker and booking information
        if (!empty($results['data'])) {
            foreach ($results['data'] as &$row) {
                $brk = $this->brokerRepository->findById(intval($row['broker_id']));
                $row['broker_name'] = $brk ? $brk['broker_name'] : '';
                $row['broker_code'] = $brk ? $brk['broker_code'] : '';
                
                $bkg = $this->bookingRepository->findById(intval($row['booking_id']));
                $row['booking_number'] = $bkg ? $bkg['booking_number'] : '';
            }
        }

        return $this->success('Broker commissions retrieved successfully.', $results);
    }

    /**
     * POST /commissions
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['broker_id']) || empty($params['booking_id']) || empty($params['commission_amount'])) {
            return $this->error('Validation failed: broker_id, booking_id, and commission_amount are required.');
        }

        $broker_id = intval($params['broker_id']);
        $booking_id = intval($params['booking_id']);
        $commission_amount = floatval($params['commission_amount']);
        $paid_amount = isset($params['paid_amount']) ? floatval($params['paid_amount']) : 0.00;
        $balance_amount = $commission_amount - $paid_amount;
        
        $payment_status = 'Pending';
        if ($paid_amount >= $commission_amount) {
            $payment_status = 'Paid';
        } elseif ($paid_amount > 0) {
            $payment_status = 'Approved';
        }

        $data = [
            'broker_id' => $broker_id,
            'booking_id' => $booking_id,
            'commission_percentage' => isset($params['commission_percentage']) ? floatval($params['commission_percentage']) : 0.00,
            'commission_amount' => $commission_amount,
            'paid_amount' => $paid_amount,
            'balance_amount' => $balance_amount,
            'payment_status' => sanitize_text_field($params['payment_status'] ?? $payment_status)
        ];

        $formats = ['%d', '%d', '%f', '%f', '%f', '%f', '%s'];
        $inserted_id = $this->commissionRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to record broker commission.');
        }

        AuthService::logActivity(get_current_user_id(), 'COMMISSION_CREATE', "Recorded commission ID $inserted_id ($commission_amount) for broker $broker_id");

        return $this->success('Commission record created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /commissions/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $commission = $this->commissionRepository->findById($id);

        if (!$commission) {
            return $this->error('Commission record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['commission_percentage', 'commission_amount', 'paid_amount', 'payment_status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'commission_percentage' || $field === 'commission_amount' || $field === 'paid_amount') {
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

        // Adjust balance
        $new_amount = isset($data['commission_amount']) ? $data['commission_amount'] : floatval($commission['commission_amount']);
        $new_paid = isset($data['paid_amount']) ? $data['paid_amount'] : floatval($commission['paid_amount']);
        $data['balance_amount'] = $new_amount - $new_paid;
        $formats[] = '%f';

        // Auto status if not set
        if (!isset($data['payment_status'])) {
            if ($new_paid >= $new_amount) {
                $data['payment_status'] = 'Paid';
            } elseif ($new_paid > 0) {
                $data['payment_status'] = 'Approved';
            } else {
                $data['payment_status'] = 'Pending';
            }
            $formats[] = '%s';
        }

        $updated = $this->commissionRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update commission record.');
        }

        AuthService::logActivity(get_current_user_id(), 'COMMISSION_UPDATE', "Updated commission ID: $id");

        return $this->success('Commission record updated successfully.', $this->commissionRepository->findById($id));
    }

    /**
     * DELETE /commissions/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $commission = $this->commissionRepository->findById($id);

        if (!$commission) {
            return $this->error('Commission record not found.', [], 404);
        }

        $deleted = $this->commissionRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete commission record.');
        }

        AuthService::logActivity(get_current_user_id(), 'COMMISSION_DELETE', "Soft deleted commission ID: $id");

        return $this->success('Commission record deleted successfully.');
    }
}
