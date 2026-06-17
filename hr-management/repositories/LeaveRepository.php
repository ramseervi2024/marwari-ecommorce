<?php
namespace HrManagementApi\Repositories;

class LeaveRepository extends BaseRepository {
    
    public function __construct() {
        parent::__construct('leaves', false);
    }

    /**
     * Get Leave balances of an employee
     */
    public function getLeaveBalance(int $employee_id): ?array {
        global $wpdb;
        $table = $wpdb->prefix . 'hr_leave_balances';
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE employee_id = %d", $employee_id), ARRAY_A);
        return $row ?: null;
    }

    /**
     * Initialize/Update leave balance
     */
    public function updateLeaveBalance(int $employee_id, array $data): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'hr_leave_balances';
        
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE employee_id = %d", $employee_id));
        if ($exists) {
            $formats = [];
            foreach ($data as $k => $v) {
                $formats[] = '%d';
            }
            $result = $wpdb->update($table, $data, ['employee_id' => $employee_id], $formats, ['%d']);
            return $result !== false;
        } else {
            $data['employee_id'] = $employee_id;
            $formats = ['%d', '%d', '%d', '%d', '%d'];
            $result = $wpdb->insert($table, $data, $formats);
            return $result !== false;
        }
    }

    /**
     * Deduct leaves from balance when approved
     */
    public function deductLeaveBalance(int $employee_id, string $leave_type, int $days): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'hr_leave_balances';
        
        $column = '';
        $op = '-';
        if (strcasecmp($leave_type, 'Casual') === 0) {
            $column = 'casual_leaves';
        } elseif (strcasecmp($leave_type, 'Medical') === 0) {
            $column = 'medical_leaves';
        } elseif (strcasecmp($leave_type, 'Earned') === 0) {
            $column = 'earned_leaves';
        } elseif (strcasecmp($leave_type, 'Unpaid') === 0) {
            $column = 'unpaid_leaves';
            $op = '+'; // Increments unpaid leaves count
        }

        if (empty($column)) {
            return false;
        }

        $query = $wpdb->prepare("
            UPDATE $table 
            SET $column = GREATEST(0, $column $op %d) 
            WHERE employee_id = %d
        ", $days, $employee_id);

        return $wpdb->query($query) !== false;
    }
}
