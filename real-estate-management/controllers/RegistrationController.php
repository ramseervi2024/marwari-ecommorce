<?php
namespace RealEstateManagementApi\Controllers;

use RealEstateManagementApi\Repositories\RegistrationRepository;
use RealEstateManagementApi\Repositories\BookingRepository;
use RealEstateManagementApi\Repositories\CustomerRepository;
use RealEstateManagementApi\Repositories\PropertyRepository;
use RealEstateManagementApi\Services\AuthService;
use WP_REST_Request;

class RegistrationController extends BaseController {
    private $registrationRepository;
    private $bookingRepository;
    private $customerRepository;
    private $propertyRepository;

    public function __construct() {
        $this->registrationRepository = new RegistrationRepository();
        $this->bookingRepository = new BookingRepository();
        $this->customerRepository = new CustomerRepository();
        $this->propertyRepository = new PropertyRepository();
    }

    /**
     * GET /registrations
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'booking_id', 'registration_date', 'registration_cost', 'handover_date', 'status', 'created_at'];
        $search_fields = ['status'];
        
        $extra_filters = [];
        if (isset($params['booking_id'])) {
            $extra_filters['booking_id'] = intval($params['booking_id']);
        }
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->registrationRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        
        // Enrich data with booking/customer details
        if (!empty($results['data'])) {
            foreach ($results['data'] as &$row) {
                $bkg = $this->bookingRepository->findById(intval($row['booking_id']));
                $row['booking_number'] = $bkg ? $bkg['booking_number'] : '';
                
                if ($bkg) {
                    $cust = $this->customerRepository->findById(intval($bkg['customer_id']));
                    $row['customer_name'] = $cust ? $cust['name'] : '';
                    
                    $prop = $this->propertyRepository->findById(intval($bkg['property_id']));
                    $row['property_unit'] = $prop ? $prop['unit_number'] : '';
                    $row['project_name'] = $prop ? $prop['project_name'] : '';
                } else {
                    $row['customer_name'] = '';
                    $row['property_unit'] = '';
                    $row['project_name'] = '';
                }
            }
        }

        return $this->success('Registrations retrieved successfully.', $results);
    }

    /**
     * POST /registrations
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['booking_id'])) {
            return $this->error('Validation failed: booking_id is required.');
        }

        $data = [
            'booking_id' => intval($params['booking_id']),
            'registration_date' => !empty($params['registration_date']) ? sanitize_text_field($params['registration_date']) : null,
            'registration_cost' => isset($params['registration_cost']) ? floatval($params['registration_cost']) : 0.00,
            'handover_date' => !empty($params['handover_date']) ? sanitize_text_field($params['handover_date']) : null,
            'status' => sanitize_text_field($params['status'] ?? 'Pending')
        ];

        $formats = ['%d', '%s', '%f', '%s', '%s'];
        $inserted_id = $this->registrationRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create registration record.');
        }

        AuthService::logActivity(get_current_user_id(), 'REGISTRATION_CREATE', "Created registration record $inserted_id for booking $params[booking_id]");

        return $this->success('Registration record created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /registrations/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $reg = $this->registrationRepository->findById($id);

        if (!$reg) {
            return $this->error('Registration record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['registration_date', 'registration_cost', 'handover_date', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'registration_cost') {
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

        $updated = $this->registrationRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update registration record.');
        }

        // If status is Handed-Over (or Completed), update property status to Sold
        if (isset($data['status']) && ($data['status'] === 'Handed-Over' || $data['status'] === 'Completed')) {
            $bkg = $this->bookingRepository->findById(intval($reg['booking_id']));
            if ($bkg) {
                $this->propertyRepository->update(intval($bkg['property_id']), ['status' => 'Sold'], ['%s']);
            }
        }

        AuthService::logActivity(get_current_user_id(), 'REGISTRATION_UPDATE', "Updated registration ID: $id");

        return $this->success('Registration record updated successfully.', $this->registrationRepository->findById($id));
    }
}
