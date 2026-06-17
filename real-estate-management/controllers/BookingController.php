<?php
namespace RealEstateManagementApi\Controllers;

use RealEstateManagementApi\Repositories\BookingRepository;
use RealEstateManagementApi\Repositories\CustomerRepository;
use RealEstateManagementApi\Repositories\PropertyRepository;
use RealEstateManagementApi\Repositories\BrokerRepository;
use RealEstateManagementApi\Services\AuthService;
use WP_REST_Request;

class BookingController extends BaseController {
    private $bookingRepository;
    private $customerRepository;
    private $propertyRepository;
    private $brokerRepository;

    public function __construct() {
        $this->bookingRepository = new BookingRepository();
        $this->customerRepository = new CustomerRepository();
        $this->propertyRepository = new PropertyRepository();
        $this->brokerRepository = new BrokerRepository();
    }

    /**
     * GET /bookings
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'booking_number', 'booking_date', 'final_price', 'status', 'created_at'];
        $search_fields = ['booking_number', 'status'];
        
        $extra_filters = [];
        if (isset($params['customer_id'])) {
            $extra_filters['customer_id'] = intval($params['customer_id']);
        }
        if (isset($params['property_id'])) {
            $extra_filters['property_id'] = intval($params['property_id']);
        }
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->bookingRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        
        // Enrich booking list with details
        if (!empty($results['data'])) {
            foreach ($results['data'] as &$row) {
                $cust = $this->customerRepository->findById(intval($row['customer_id']));
                $row['customer_name'] = $cust ? $cust['name'] : '';
                
                $prop = $this->propertyRepository->findById(intval($row['property_id']));
                $row['property_unit'] = $prop ? $prop['unit_number'] : '';
                $row['project_name'] = $prop ? $prop['project_name'] : '';
                $row['property_price'] = $prop ? $prop['price'] : 0.00;

                if (!empty($row['broker_id'])) {
                    $brk = $this->brokerRepository->findById(intval($row['broker_id']));
                    $row['broker_name'] = $brk ? $brk['broker_name'] : '';
                } else {
                    $row['broker_name'] = '';
                }
            }
        }

        return $this->success('Bookings retrieved successfully.', $results);
    }

    /**
     * GET /bookings/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $booking = $this->bookingRepository->findById($id);

        if (!$booking) {
            return $this->error('Booking not found.', [], 404);
        }

        // Add detailed fields
        $cust = $this->customerRepository->findById(intval($booking['customer_id']));
        $booking['customer_name'] = $cust ? $cust['name'] : '';
        
        $prop = $this->propertyRepository->findById(intval($booking['property_id']));
        $booking['property_unit'] = $prop ? $prop['unit_number'] : '';
        $booking['project_name'] = $prop ? $prop['project_name'] : '';

        if (!empty($booking['broker_id'])) {
            $brk = $this->brokerRepository->findById(intval($booking['broker_id']));
            $booking['broker_name'] = $brk ? $brk['broker_name'] : '';
        }

        return $this->success('Booking retrieved successfully.', $booking);
    }

    /**
     * POST /bookings
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['customer_id']) || empty($params['property_id']) || empty($params['agreement_value'])) {
            return $this->error('Validation failed: customer_id, property_id, and agreement_value are required.');
        }

        $customer_id = intval($params['customer_id']);
        $property_id = intval($params['property_id']);
        $agreement_value = floatval($params['agreement_value']);
        $discount = isset($params['discount']) ? floatval($params['discount']) : 0.00;
        $booking_amount = isset($params['booking_amount']) ? floatval($params['booking_amount']) : 0.00;
        $final_price = $agreement_value - $discount;

        // Verify customer and property exist
        $customer = $this->customerRepository->findById($customer_id);
        if (!$customer) {
            return $this->error('Customer does not exist.');
        }

        $property = $this->propertyRepository->findById($property_id);
        if (!$property) {
            return $this->error('Property unit does not exist.');
        }

        if ($property['status'] === 'Booked' || $property['status'] === 'Sold') {
            return $this->error('This property unit is already Booked/Sold.');
        }

        // Generate booking number
        $booking_number = 'BKG-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
        while ($this->bookingRepository->existsBookingNumber($booking_number)) {
            $booking_number = 'BKG-' . date('Y') . '-' . sprintf('%04d', rand(1000, 9999));
        }

        $broker_id = !empty($params['broker_id']) ? intval($params['broker_id']) : null;

        $data = [
            'booking_number' => $booking_number,
            'customer_id' => $customer_id,
            'property_id' => $property_id,
            'booking_date' => !empty($params['booking_date']) ? sanitize_text_field($params['booking_date']) : current_time('Y-m-d'),
            'booking_amount' => $booking_amount,
            'agreement_value' => $agreement_value,
            'discount' => $discount,
            'final_price' => $final_price,
            'broker_id' => $broker_id,
            'status' => sanitize_text_field($params['status'] ?? 'Confirmed')
        ];

        $formats = ['%s', '%d', '%d', '%s', '%f', '%f', '%f', '%f', '%d', '%s'];
        $inserted_id = $this->bookingRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create property booking.');
        }

        // 1. Update Property Status to Booked
        $this->propertyRepository->update($property_id, ['status' => 'Booked'], ['%s']);

        // 2. Trigger auto broker commission calculation if broker exists
        global $wpdb;
        if (!empty($broker_id)) {
            $broker = $this->brokerRepository->findById($broker_id);
            if ($broker) {
                $commission_percentage = floatval($broker['commission_percentage']);
                $commission_amount = ($commission_percentage * $final_price) / 100.00;
                
                $table_commissions = $wpdb->prefix . 'realestate_commissions';
                $wpdb->insert($table_commissions, [
                    'broker_id' => $broker_id,
                    'booking_id' => $inserted_id,
                    'commission_percentage' => $commission_percentage,
                    'commission_amount' => $commission_amount,
                    'paid_amount' => 0.00,
                    'balance_amount' => $commission_amount,
                    'payment_status' => 'Pending'
                ]);
            }
        }

        // 3. Create default payment installments
        $table_schedules = $wpdb->prefix . 'realestate_payment_schedules';
        // Installment 1: Booking Token
        $wpdb->insert($table_schedules, [
            'booking_id' => $inserted_id,
            'installment_name' => 'Booking Token Amount',
            'due_date' => $data['booking_date'],
            'amount' => $booking_amount,
            'paid_amount' => $booking_amount,
            'balance_amount' => 0.00,
            'payment_status' => 'Paid'
        ]);
        // Installment 2: Agreement Value Balance
        $balance_val = $final_price - $booking_amount;
        $due_date = date('Y-m-d', strtotime('+30 days', strtotime($data['booking_date'])));
        if ($balance_val > 0) {
            $wpdb->insert($table_schedules, [
                'booking_id' => $inserted_id,
                'installment_name' => 'Agreement Value Balance Installment',
                'due_date' => $due_date,
                'amount' => $balance_val,
                'paid_amount' => 0.00,
                'balance_amount' => $balance_val,
                'payment_status' => 'Pending'
            ]);
        }

        // 4. Create pending registration record
        $table_registrations = $wpdb->prefix . 'realestate_registrations';
        $wpdb->insert($table_registrations, [
            'booking_id' => $inserted_id,
            'status' => 'Pending'
        ]);

        AuthService::logActivity(get_current_user_id(), 'BOOKING_CREATE', "Created booking $booking_number ($inserted_id) for customer $customer_id");

        return $this->success('Booking recorded successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /bookings/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $booking = $this->bookingRepository->findById($id);

        if (!$booking) {
            return $this->error('Booking not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['booking_date', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                $data[$field] = sanitize_text_field($params[$field]);
                $formats[] = '%s';
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->bookingRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update booking.');
        }

        // If status changed to Cancelled, free up property unit
        if (isset($data['status']) && $data['status'] === 'Cancelled') {
            $this->propertyRepository->update(intval($booking['property_id']), ['status' => 'Available'], ['%s']);
            
            // Cancel payment schedules
            global $wpdb;
            $table_schedules = $wpdb->prefix . 'realestate_payment_schedules';
            $wpdb->update($table_schedules, ['payment_status' => 'Cancelled'], ['booking_id' => $id]);

            // Cancel registrations
            $table_registrations = $wpdb->prefix . 'realestate_registrations';
            $wpdb->update($table_registrations, ['status' => 'Cancelled'], ['booking_id' => $id]);
        }

        AuthService::logActivity(get_current_user_id(), 'BOOKING_UPDATE', "Updated booking ID: $id status/date");

        return $this->success('Booking updated successfully.', $this->bookingRepository->findById($id));
    }

    /**
     * DELETE /bookings/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $booking = $this->bookingRepository->findById($id);

        if (!$booking) {
            return $this->error('Booking not found.', [], 404);
        }

        // Free up unit
        $this->propertyRepository->update(intval($booking['property_id']), ['status' => 'Available'], ['%s']);

        $deleted = $this->bookingRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete booking.');
        }

        AuthService::logActivity(get_current_user_id(), 'BOOKING_DELETE', "Soft deleted booking ID: $id ($booking[booking_number])");

        return $this->success('Booking deleted successfully.');
    }
}
