<?php
namespace GymErpApi\Controllers;
use GymErpApi\Repositories\PaymentRepository;
use WP_REST_Request;

class PaymentController extends BaseController {
    private $repo;
    public function __construct() { $this->repo = new PaymentRepository(); }

    public function getPayments(WP_REST_Request $request) {
        return $this->success('Payments.', $this->repo->findAll($request->get_params(), ['invoice_number'], ['invoice_number','payment_date']));
    }
    public function recordPayment(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['member_id']) || empty($p['amount'])) return $this->error('Member and Amount required.');
        $id = $this->repo->create([
            'invoice_number' => 'INV-' . strtoupper(uniqid()),
            'member_id' => $p['member_id'], 'membership_id' => $p['membership_id'] ?? null,
            'amount' => $p['amount'], 'payment_date' => $p['payment_date'] ?? current_time('Y-m-d'),
            'payment_mode' => $p['payment_mode'] ?? 'Cash'
        ], ['%s','%d','%d','%f','%s','%s']);
        return $id ? $this->success('Payment recorded.', ['id' => $id]) : $this->error('Failed.');
    }
}
