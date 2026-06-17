<?php
namespace JewelleryManagementApi\Controllers;

use JewelleryManagementApi\Repositories\LoyaltyRepository;
use JewelleryManagementApi\Repositories\CustomerRepository;
use JewelleryManagementApi\Services\AuthService;
use WP_REST_Request;

class LoyaltyController extends BaseController {
    private $repo;
    private $customerRepo;

    public function __construct() {
        $this->repo = new LoyaltyRepository();
        $this->customerRepo = new CustomerRepository();
    }

    public function index(WP_REST_Request $request) {
        $limit = intval($request->get_param('limit') ?: 100);
        $offset = intval($request->get_param('offset') ?: 0);
        $items = $this->repo->all($limit, $offset);
        return $this->success('Loyalty logs retrieved successfully.', $items);
    }

    public function get(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Loyalty log not found.', [], 404);
        }
        return $this->success('Loyalty log retrieved successfully.', $item);
    }

    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();
        if (empty($params['customer_id'])) {
            return $this->error('Validation failed: customer_id is required.');
        }

        $customer_id = intval($params['customer_id']);
        $customer = $this->customerRepo->find($customer_id);
        if (!$customer) {
            return $this->error('Customer not found.');
        }

        $points_earned = intval($params['points_earned'] ?? 0);
        $points_redeemed = intval($params['points_redeemed'] ?? 0);
        
        // Compute new loyalty balance
        $current_points = intval($customer['loyalty_points']);
        $new_points = $current_points + $points_earned - $points_redeemed;
        if ($new_points < 0) {
            return $this->error('Cannot redeem points. Customer balance is insufficient.');
        }

        $params['customer_id'] = $customer_id;
        $params['points_earned'] = $points_earned;
        $params['points_redeemed'] = $points_redeemed;
        $params['membership_level'] = sanitize_text_field($params['membership_level'] ?? (($new_points > 100) ? 'Platinum' : (($new_points > 50) ? 'Gold' : 'Silver')));
        $params['created_at'] = current_time('mysql');
        $params['updated_at'] = current_time('mysql');

        $id = $this->repo->create($params);
        if (!$id) {
            return $this->error('Failed to create loyalty log.');
        }

        // Update customer points
        $this->customerRepo->update($customer_id, ['loyalty_points' => $new_points, 'updated_at' => current_time('mysql')]);

        AuthService::logActivity(get_current_user_id(), 'LOYALTY_UPDATE', "Customer ID $customer_id balance updated. Earned: $points_earned, Redeemed: $points_redeemed, Balance: $new_points");

        return $this->success('Loyalty log recorded successfully.', array_merge(['id' => $id], $params), 201);
    }

    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Loyalty log not found.', [], 404);
        }

        $params = $request->get_json_params();
        $updates = [];
        if (isset($params['membership_level'])) $updates['membership_level'] = sanitize_text_field($params['membership_level']);
        $updates['updated_at'] = current_time('mysql');

        if (!$this->repo->update($id, $updates)) {
            return $this->error('Failed to update loyalty log.');
        }

        return $this->success('Loyalty log updated successfully.', array_merge($item, $updates));
    }

    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $item = $this->repo->find($id);
        if (!$item) {
            return $this->error('Loyalty log not found.', [], 404);
        }
        if (!$this->repo->delete($id)) {
            return $this->error('Failed to delete loyalty log.');
        }

        return $this->success('Loyalty log deleted successfully.');
    }
}
