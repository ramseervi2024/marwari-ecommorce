<?php
namespace CrmManagementApi\Controllers;

use CrmManagementApi\Repositories\CustomerRepository;
use CrmManagementApi\Services\AuthService;
use WP_REST_Request;

class CustomerController extends BaseController {
    private $customerRepository;

    public function __construct() {
        $this->customerRepository = new CustomerRepository();
    }

    /**
     * GET /customers
     */
    public function getCustomers(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'customer_code', 'company_name', 'contact_person', 'status'];
        $search_fields = ['customer_code', 'company_name', 'contact_person', 'email', 'mobile'];
        $extra_filters = [];

        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->customerRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Customers list retrieved.', $results);
    }

    /**
     * POST /customers
     */
    public function createCustomer(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['company_name']) || empty($params['email']) || empty($params['mobile'])) {
            return $this->error('company_name, email, and mobile are required.');
        }

        // Auto generate customer code
        global $wpdb;
        $table_name = $wpdb->prefix . 'crm_customers';
        $max_id = (int)$wpdb->get_var("SELECT MAX(id) FROM $table_name") + 1;
        $customer_code = 'CUST-' . date('Y') . '-' . sprintf('%04d', $max_id);

        $data = [
            'customer_code'  => $customer_code,
            'company_name'   => sanitize_text_field($params['company_name']),
            'contact_person' => sanitize_text_field($params['contact_person'] ?? ''),
            'mobile'         => sanitize_text_field($params['mobile']),
            'email'          => sanitize_email($params['email']),
            'gst_number'     => sanitize_text_field($params['gst_number'] ?? ''),
            'address'        => sanitize_textarea_field($params['address'] ?? ''),
            'city'           => sanitize_text_field($params['city'] ?? ''),
            'state'          => sanitize_text_field($params['state'] ?? ''),
            'status'         => sanitize_text_field($params['status'] ?? 'Active'),
            'user_id'        => !empty($params['user_id']) ? intval($params['user_id']) : null
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d'];

        $id = $this->customerRepository->create($data, $formats);
        if (!$id) {
            return $this->error('Failed to create customer.');
        }

        AuthService::logActivity(get_current_user_id(), 'CUSTOMER_CREATE', "Created customer: $customer_code ($data[company_name])");

        return $this->success('Customer created successfully.', $this->customerRepository->findById($id), 201);
    }

    /**
     * GET /customers/{id}
     */
    public function getCustomer(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $customer = $this->customerRepository->findById($id);
        if (!$customer) {
            return $this->error('Customer not found.', [], 404);
        }
        return $this->success('Customer retrieved.', $customer);
    }

    /**
     * PUT /customers/{id}
     */
    public function updateCustomer(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return $this->error('Customer not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = [
            'company_name'   => '%s',
            'contact_person' => '%s',
            'mobile'         => '%s',
            'email'          => '%s',
            'gst_number'     => '%s',
            'address'        => '%s',
            'city'           => '%s',
            'state'          => '%s',
            'status'         => '%s',
            'user_id'        => '%d'
        ];

        foreach ($fields as $key => $fmt) {
            if (isset($params[$key])) {
                if ($key === 'email') {
                    $data[$key] = sanitize_email($params[$key]);
                } elseif ($key === 'address') {
                    $data[$key] = sanitize_textarea_field($params[$key]);
                } elseif ($key === 'user_id') {
                    $data[$key] = intval($params[$key]);
                } else {
                    $data[$key] = sanitize_text_field($params[$key]);
                }
                $formats[] = $fmt;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->customerRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update customer.');
        }

        AuthService::logActivity(get_current_user_id(), 'CUSTOMER_UPDATE', "Updated customer ID: $id ($customer[customer_code])");

        return $this->success('Customer updated successfully.', $this->customerRepository->findById($id));
    }

    /**
     * DELETE /customers/{id}
     */
    public function deleteCustomer(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return $this->error('Customer not found.', [], 404);
        }

        if (!current_user_can('manage_crm_settings') && !current_user_can('view_crm_reports')) {
            return $this->error('Access Denied.', [], 403);
        }

        $deleted = $this->customerRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete customer.');
        }

        AuthService::logActivity(get_current_user_id(), 'CUSTOMER_DELETE', "Deleted customer ID: $id ($customer[customer_code])");

        return $this->success('Customer deleted successfully.');
    }
}
