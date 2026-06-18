<?php
namespace GymErpApi\Controllers;
use GymErpApi\Repositories\MembershipRepository;
use GymErpApi\Repositories\PaymentRepository;
use WP_REST_Request;

class MembershipController extends BaseController {
    private $repo;
    private $payRepo;
    public function __construct() { $this->repo = new MembershipRepository(); $this->payRepo = new PaymentRepository(); }

    public function getMemberships(WP_REST_Request $request) {
        $this->repo->expireOldMemberships();
        return $this->success('Memberships.', $this->repo->getActiveMemberships());
    }
    public function getExpiring(WP_REST_Request $request) {
        return $this->success('Expiring Soon.', $this->repo->getExpiringSoon());
    }
    public function assignPlan(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['member_id']) || empty($p['plan_id']) || empty($p['start_date']) || empty($p['end_date'])) return $this->error('Missing fields.');
        $id = $this->repo->create([
            'member_id' => $p['member_id'], 'plan_id' => $p['plan_id'],
            'trainer_id' => $p['trainer_id'] ?? null, 'start_date' => $p['start_date'],
            'end_date' => $p['end_date']
        ], ['%d','%d','%d','%s','%s']);
        
        if ($id && !empty($p['amount_paid'])) {
            $this->payRepo->create([
                'invoice_number' => 'INV-' . strtoupper(uniqid()),
                'member_id' => $p['member_id'], 'membership_id' => $id,
                'amount' => $p['amount_paid'], 'payment_date' => current_time('Y-m-d'),
                'payment_mode' => $p['payment_mode'] ?? 'Cash'
            ], ['%s','%d','%d','%f','%s','%s']);
        }
        return $id ? $this->success('Membership assigned.', ['id' => $id]) : $this->error('Failed.');
    }
}
