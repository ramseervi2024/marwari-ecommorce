<?php
namespace GymErpApi\Controllers;
use GymErpApi\Repositories\MemberRepository;
use WP_REST_Request;

class MemberController extends BaseController {
    private $repo;
    public function __construct() { $this->repo = new MemberRepository(); }

    public function getMembers(WP_REST_Request $request) {
        return $this->success('Members.', $this->repo->findAll($request->get_params(), ['member_id','name','mobile'], ['name','member_id']));
    }
    public function getMember(WP_REST_Request $request) {
        $m = $this->repo->findById((int)$request->get_param('id'));
        return $m ? $this->success('Member.', $m) : $this->error('Not found', [], 404);
    }
    public function createMember(WP_REST_Request $request) {
        $p = $request->get_json_params();
        if (empty($p['name']) || empty($p['mobile'])) return $this->error('Name and Mobile required.');
        $data = [
            'member_id' => $this->repo->generateMemberId(), 'name' => $p['name'],
            'mobile' => $p['mobile'], 'email' => $p['email'] ?? '',
            'gender' => $p['gender'] ?? '', 'dob' => $p['dob'] ?? null,
            'address' => $p['address'] ?? '', 'height_cm' => $p['height_cm'] ?? null,
            'weight_kg' => $p['weight_kg'] ?? null, 'medical_history' => $p['medical_history'] ?? ''
        ];
        $id = $this->repo->create($data, ['%s','%s','%s','%s','%s','%s','%s','%f','%f','%s']);
        return $id ? $this->success('Created.', ['id' => $id, 'member_id' => $data['member_id']]) : $this->error('Failed.');
    }
    public function updateMember(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $p = $request->get_json_params();
        $fields = ['name'=>'%s','mobile'=>'%s','email'=>'%s','gender'=>'%s','dob'=>'%s','address'=>'%s','height_cm'=>'%f','weight_kg'=>'%f','medical_history'=>'%s'];
        $data = []; $formats = [];
        foreach ($fields as $f => $fmt) { if (isset($p[$f])) { $data[$f] = $p[$f]; $formats[] = $fmt; } }
        return $this->repo->update($id, $data, $formats) ? $this->success('Updated.') : $this->error('Failed.');
    }
    public function deleteMember(WP_REST_Request $request) {
        return $this->repo->delete((int)$request->get_param('id')) ? $this->success('Deleted.') : $this->error('Failed.');
    }
}
