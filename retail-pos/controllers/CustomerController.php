<?php
namespace RetailPosApi\Controllers;

use RetailPosApi\Repositories\CustomerRepository;
use RetailPosApi\Services\AuthService;
use WP_REST_Request;

class CustomerController extends BaseController {
    private $customerRepository;

    public function __construct() {
        $this->customerRepository = new CustomerRepository();
    }

    /**
     * GET /customers
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'customer_code', 'name', 'mobile', 'loyalty_points', 'total_purchases', 'status'];
        $search_fields = ['customer_code', 'name', 'mobile', 'email', 'gst_number'];

        $results = $this->customerRepository->findAll($params, $allowed_sorts, $search_fields);
        return $this->success('Customers retrieved successfully.', $results);
    }

    /**
     * GET /customers/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return $this->error('Customer not found.', [], 404);
        }

        return $this->success('Customer retrieved successfully.', $customer);
    }

    /**
     * POST /customers
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['name']) || empty($params['mobile'])) {
            return $this->error('Validation failed: name and mobile are required.');
        }

        // Generate unique customer code
        $code = 'CUST' . sprintf('%04d', rand(1000, 9999));
        while ($this->customerRepository->existsCustomerCode($code)) {
            $code = 'CUST' . sprintf('%04d', rand(1000, 9999));
        }

        $data = [
            'customer_code' => $code,
            'name' => sanitize_text_field($params['name']),
            'mobile' => sanitize_text_field($params['mobile']),
            'email' => sanitize_email($params['email'] ?? ''),
            'address' => sanitize_textarea_field($params['address'] ?? ''),
            'gst_number' => sanitize_text_field($params['gst_number'] ?? ''),
            'loyalty_points' => intval($params['loyalty_points'] ?? 0),
            'total_purchases' => floatval($params['total_purchases'] ?? 0.00),
            'status' => sanitize_text_field($params['status'] ?? 'ACTIVE')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%d', '%f', '%s'];
        $inserted_id = $this->customerRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to register customer.');
        }

        // Log initial loyalty points if seeded
        if ($data['loyalty_points'] > 0) {
            global $wpdb;
            $wpdb->insert($wpdb->prefix . 'pos_loyalty', [
                'customer_id' => $inserted_id,
                'points' => $data['loyalty_points'],
                'transaction_type' => 'EARNED',
                'remarks' => 'Initial loyalty points on registration'
            ], ['%d', '%d', '%s', '%s']);
        }

        AuthService::logActivity(get_current_user_id(), 'CUSTOMER_CREATE', "Registered customer code $code ($inserted_id)");

        return $this->success('Customer registered successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /customers/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return $this->error('Customer not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $string_fields = ['name', 'mobile', 'email', 'address', 'gst_number', 'status'];
        foreach ($string_fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'email') {
                    $data[$field] = sanitize_email($params[$field]);
                } else if ($field === 'address') {
                    $data[$field] = sanitize_textarea_field($params[$field]);
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                }
                $formats[] = '%s';
            }
        }

        if (isset($params['loyalty_points'])) {
            $data['loyalty_points'] = intval($params['loyalty_points']);
            $formats[] = '%d';
        }
        if (isset($params['total_purchases'])) {
            $data['total_purchases'] = floatval($params['total_purchases']);
            $formats[] = '%f';
        }

        if (isset($params['customer_code'])) {
            $code = sanitize_text_field($params['customer_code']);
            if ($code !== $customer['customer_code'] && $this->customerRepository->existsCustomerCode($code, $id)) {
                return $this->error('Duplicate check failed: customer_code is already assigned to another customer.');
            }
            $data['customer_code'] = $code;
            $formats[] = '%s';
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->customerRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update customer record.');
        }

        AuthService::logActivity(get_current_user_id(), 'CUSTOMER_UPDATE', "Updated customer record ID: $id");

        return $this->success('Customer updated successfully.', $this->customerRepository->findById($id));
    }

    /**
     * DELETE /customers/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return $this->error('Customer not found.', [], 404);
        }

        $deleted = $this->customerRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete customer.');
        }

        AuthService::logActivity(get_current_user_id(), 'CUSTOMER_DELETE', "Soft deleted customer ID: $id ($customer[customer_code])");

        return $this->success('Customer deleted successfully.');
    }

    /**
     * GET /customers/:id/points (Retrieve loyalty points status)
     */
    public function getPoints(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $customer = $this->customerRepository->findById($id);

        if (!$customer) {
            return $this->error('Customer not found.', [], 404);
        }

        return $this->success('Loyalty balance retrieved.', [
            'customer_id' => $id,
            'name' => $customer['name'],
            'points' => (int)$customer['loyalty_points']
        ]);
    }

    /**
     * POST /loyalty/redeem
     */
    public function redeemPoints(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_json_params();
        $customer_id = intval($params['customer_id'] ?? 0);
        $points = intval($params['points'] ?? 0);

        if (!$customer_id || $points <= 0) {
            return $this->error('Validation failed: customer_id and positive points count are required.');
        }

        $customer = $this->customerRepository->findById($customer_id);
        if (!$customer) {
            return $this->error('Customer not found.', [], 404);
        }

        $balance = (int)$customer['loyalty_points'];
        if ($balance < $points) {
            return $this->error("Insufficient balance. Customer only has $balance points.");
        }

        // Deduct points from customer profile
        $new_balance = $balance - $points;
        $this->customerRepository->update($customer_id, ['loyalty_points' => $new_balance], ['%d']);

        // Insert into ledger
        $wpdb->insert($wpdb->prefix . 'pos_loyalty', [
            'customer_id' => $customer_id,
            'points' => -$points,
            'transaction_type' => 'REDEEMED',
            'remarks' => sanitize_text_field($params['remarks'] ?? 'Loyalty points redeemed')
        ], ['%d', '%d', '%s', '%s']);

        AuthService::logActivity(get_current_user_id(), 'LOYALTY_REDEEM', "Redeemed $points points for customer ID: $customer_id");

        return $this->success("Redeemed $points loyalty points successfully. New Balance: $new_balance", [
            'customer_id' => $customer_id,
            'points_redeemed' => $points,
            'new_balance' => $new_balance
        ]);
    }

    /**
     * GET /loyalty (Ledger view)
     */
    public function getLoyaltyLedger(WP_REST_Request $request) {
        global $wpdb;
        $params = $request->get_params();

        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 10;
        $offset = ($page - 1) * $limit;

        $where = ["1=1"];
        $args = [];

        if (isset($params['customer_id'])) {
            $where[] = "l.customer_id = %d";
            $args[] = intval($params['customer_id']);
        }

        $where_clause = implode(" AND ", $where);

        $total_query = "SELECT COUNT(*) FROM {$wpdb->prefix}pos_loyalty l WHERE $where_clause";
        $total_count = !empty($args) ? (int)$wpdb->get_var($wpdb->prepare($total_query, $args)) : (int)$wpdb->get_var($total_query);

        $data_query = "SELECT l.*, c.name as customer_name, c.customer_code 
                       FROM {$wpdb->prefix}pos_loyalty l
                       JOIN {$wpdb->prefix}pos_customers c ON l.customer_id = c.id
                       WHERE $where_clause
                       ORDER BY l.id DESC
                       LIMIT %d OFFSET %d";

        $data_args = array_merge($args, [$limit, $offset]);
        $rows = $wpdb->get_results($wpdb->prepare($data_query, $data_args), ARRAY_A);

        return $this->success('Loyalty program ledger retrieved.', [
            'total' => $total_count,
            'page' => $page,
            'limit' => $limit,
            'pages' => ceil($total_count / $limit),
            'data' => $rows ?: []
        ]);
    }
}
