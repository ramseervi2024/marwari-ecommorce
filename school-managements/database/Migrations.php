<?php
namespace SchoolManagementApi\Database;

class Migrations {
    
    /**
     * Activate migrations - Creates tables & user roles
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        // 1. JWT Secret Setup
        if (!get_option('school_management_jwt_secret')) {
            update_option('school_management_jwt_secret', bin2hex(random_bytes(32)));
        }
        
        // 2. Students Table
        $table_students = $wpdb->prefix . 'school_students';
        $sql_students = "CREATE TABLE $table_students (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            admission_no varchar(50) NOT NULL,
            roll_no varchar(50) DEFAULT NULL,
            first_name varchar(50) NOT NULL,
            last_name varchar(50) NOT NULL,
            gender varchar(20) DEFAULT NULL,
            dob date DEFAULT NULL,
            mobile varchar(20) DEFAULT NULL,
            email varchar(100) DEFAULT NULL,
            address text DEFAULT NULL,
            class_id bigint(20) DEFAULT NULL,
            section_id bigint(20) DEFAULT NULL,
            parent_id bigint(20) DEFAULT NULL,
            photo varchar(255) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY admission_no (admission_no)
        ) $charset_collate;";
        dbDelta($sql_students);

        // 3. Parents Table
        $table_parents = $wpdb->prefix . 'school_parents';
        $sql_parents = "CREATE TABLE $table_parents (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            father_name varchar(100) NOT NULL,
            mother_name varchar(100) DEFAULT NULL,
            mobile varchar(20) NOT NULL,
            email varchar(100) DEFAULT NULL,
            occupation varchar(100) DEFAULT NULL,
            address text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_parents);

        // 4. Teachers Table
        $table_teachers = $wpdb->prefix . 'school_teachers';
        $sql_teachers = "CREATE TABLE $table_teachers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            employee_code varchar(50) NOT NULL,
            name varchar(100) NOT NULL,
            mobile varchar(20) NOT NULL,
            email varchar(100) NOT NULL,
            qualification varchar(150) DEFAULT NULL,
            salary decimal(10,2) NOT NULL DEFAULT 0.00,
            joining_date date DEFAULT NULL,
            photo varchar(255) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY employee_code (employee_code)
        ) $charset_collate;";
        dbDelta($sql_teachers);

        // 5. Classes Table
        $table_classes = $wpdb->prefix . 'school_classes';
        $sql_classes = "CREATE TABLE $table_classes (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            class_name varchar(50) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_classes);

        // 6. Sections Table
        $table_sections = $wpdb->prefix . 'school_sections';
        $sql_sections = "CREATE TABLE $table_sections (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            section_name varchar(50) NOT NULL,
            class_id bigint(20) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_sections);

        // 7. Subjects Table
        $table_subjects = $wpdb->prefix . 'school_subjects';
        $sql_subjects = "CREATE TABLE $table_subjects (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            subject_name varchar(100) NOT NULL,
            subject_code varchar(50) DEFAULT NULL,
            class_id bigint(20) NOT NULL,
            teacher_id bigint(20) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_subjects);

        // 8. Attendance Table
        $table_attendance = $wpdb->prefix . 'school_attendance';
        $sql_attendance = "CREATE TABLE $table_attendance (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            student_id bigint(20) DEFAULT NULL,
            teacher_id bigint(20) DEFAULT NULL,
            attendance_date date NOT NULL,
            status varchar(20) NOT NULL, -- Present, Absent, Late, Half Day
            remarks text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_attendance);

        // 9. Exams Table
        $table_exams = $wpdb->prefix . 'school_exams';
        $sql_exams = "CREATE TABLE $table_exams (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            exam_name varchar(100) NOT NULL,
            exam_type varchar(50) DEFAULT NULL,
            start_date date DEFAULT NULL,
            end_date date DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_exams);

        // 10. Marks Table
        $table_marks = $wpdb->prefix . 'school_marks';
        $sql_marks = "CREATE TABLE $table_marks (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            exam_id bigint(20) NOT NULL,
            student_id bigint(20) NOT NULL,
            subject_id bigint(20) NOT NULL,
            marks_obtained decimal(5,2) NOT NULL DEFAULT 0.00,
            max_marks decimal(5,2) NOT NULL DEFAULT 100.00,
            remarks text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_marks);

        // 11. Fees Table
        $table_fees = $wpdb->prefix . 'school_fees';
        $sql_fees = "CREATE TABLE $table_fees (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            type varchar(20) NOT NULL, -- STRUCTURE or COLLECTION
            student_id bigint(20) DEFAULT NULL,
            class_id bigint(20) DEFAULT NULL,
            title varchar(150) NOT NULL,
            amount decimal(10,2) NOT NULL DEFAULT 0.00,
            due_date date DEFAULT NULL,
            status varchar(20) DEFAULT NULL, -- PAID, PENDING, PARTIAL
            payment_method varchar(50) DEFAULT NULL,
            transaction_id varchar(100) DEFAULT NULL,
            paid_at datetime DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_fees);

        // 12. Payroll Table
        $table_payroll = $wpdb->prefix . 'school_payroll';
        $sql_payroll = "CREATE TABLE $table_payroll (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            teacher_id bigint(20) NOT NULL,
            month int(2) NOT NULL,
            year int(4) NOT NULL,
            salary_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            allowance decimal(10,2) NOT NULL DEFAULT 0.00,
            deduction decimal(10,2) NOT NULL DEFAULT 0.00,
            net_salary decimal(10,2) NOT NULL DEFAULT 0.00,
            status varchar(20) NOT NULL DEFAULT 'PENDING', -- PAID, PENDING
            paid_date date DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_payroll);

        // 13. Library Table
        $table_library = $wpdb->prefix . 'school_library';
        $sql_library = "CREATE TABLE $table_library (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            type varchar(20) NOT NULL, -- BOOK or ISSUE
            title varchar(150) DEFAULT NULL,
            author varchar(100) DEFAULT NULL,
            isbn varchar(50) DEFAULT NULL,
            book_id bigint(20) DEFAULT NULL,
            student_id bigint(20) DEFAULT NULL,
            issue_date date DEFAULT NULL,
            return_date date DEFAULT NULL,
            actual_return_date date DEFAULT NULL,
            status varchar(20) NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_library);

        // 14. Transport Table
        $table_transport = $wpdb->prefix . 'school_transport';
        $sql_transport = "CREATE TABLE $table_transport (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            route_name varchar(100) NOT NULL,
            source varchar(100) NOT NULL,
            destination varchar(100) NOT NULL,
            vehicle_number varchar(50) NOT NULL,
            driver_name varchar(100) NOT NULL,
            driver_mobile varchar(20) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_transport);

        // 15. Events Table
        $table_events = $wpdb->prefix . 'school_events';
        $sql_events = "CREATE TABLE $table_events (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            type varchar(20) NOT NULL DEFAULT 'EVENT', -- EVENT or NOTICE
            title varchar(150) NOT NULL,
            description text DEFAULT NULL,
            event_date datetime DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_events);

        // 16. Notifications Table
        $table_notifications = $wpdb->prefix . 'school_notifications';
        $sql_notifications = "CREATE TABLE $table_notifications (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            type varchar(20) NOT NULL, -- EMAIL, SMS, PUSH, WHATSAPP
            recipient varchar(150) NOT NULL,
            subject varchar(150) DEFAULT NULL,
            message text NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'SENT',
            sent_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_notifications);

        // 17. Documents Table
        $table_documents = $wpdb->prefix . 'school_documents';
        $sql_documents = "CREATE TABLE $table_documents (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            related_id bigint(20) NOT NULL,
            related_type varchar(20) NOT NULL, -- STUDENT, TEACHER, PARENT
            document_type varchar(50) NOT NULL, -- Aadhaar, Birth Certificate, PAN, etc.
            file_url varchar(255) NOT NULL,
            media_id bigint(20) NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_documents);

        // 18. Activity Logs Table
        $table_logs = $wpdb->prefix . 'school_activity_logs';
        $sql_logs = "CREATE TABLE $table_logs (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            action varchar(100) NOT NULL,
            details text DEFAULT NULL,
            ip_address varchar(50) DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_logs);

        // 19. Register Roles and Capabilities
        self::register_roles();
        self::seed_test_accounts();
        self::seed_sample_records();
    }
    
    /**
     * Register School user roles
     */
    private static function register_roles() {
        remove_role('school_super_admin');
        remove_role('school_principal');
        remove_role('school_teacher');
        remove_role('school_accountant');
        remove_role('school_parent');
        remove_role('school_student');
        
        $super_admin_caps = [
            'read' => true,
            'manage_school' => true,
            'manage_users' => true,
            'manage_students' => true,
            'manage_teachers' => true,
            'manage_parents' => true,
            'manage_classes' => true,
            'manage_attendance' => true,
            'manage_fees' => true,
            'manage_exams' => true,
            'manage_library' => true,
            'manage_transport' => true,
            'view_reports' => true,
            'view_dashboard' => true,
        ];
        
        $principal_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_students' => true,
            'manage_teachers' => true,
            'view_reports' => true,
            'manage_school' => true,
        ];
        
        $teacher_caps = [
            'read' => true,
            'view_classes' => true,
            'manage_attendance' => true,
            'enter_marks' => true,
            'manage_homework' => true,
            'view_student_progress' => true,
        ];
        
        $accountant_caps = [
            'read' => true,
            'manage_fees' => true,
            'manage_expenses' => true,
            'manage_payroll' => true,
            'view_financial_reports' => true,
        ];
        
        $parent_caps = [
            'read' => true,
            'view_child_details' => true,
            'view_attendance' => true,
            'view_results' => true,
            'view_homework' => true,
            'view_fees' => true,
        ];
        
        $student_caps = [
            'read' => true,
            'view_attendance' => true,
            'view_results' => true,
            'view_homework' => true,
            'view_timetable' => true,
            'download_documents' => true,
        ];
        
        add_role('school_super_admin', 'School Super Admin', $super_admin_caps);
        add_role('school_principal', 'School Principal', $principal_caps);
        add_role('school_teacher', 'School Teacher', $teacher_caps);
        add_role('school_accountant', 'School Accountant', $accountant_caps);
        add_role('school_parent', 'School Parent', $parent_caps);
        add_role('school_student', 'School Student', $student_caps);

        // Ensure Administrator has all permissions
        $admin_role = get_role('administrator');
        if ($admin_role) {
            foreach ($super_admin_caps as $cap => $grant) {
                $admin_role->add_cap($cap);
            }
        }
    }

    private static function seed_test_accounts() {
        // Delete old school_admin if exists
        $old_admin_id = username_exists('school_admin');
        if ($old_admin_id) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
            wp_delete_user($old_admin_id);
        }
        
        // Ensure new schoolsuperadmin with password 123456 exists
        $super_admin_id = username_exists('schoolsuperadmin');
        if ($super_admin_id) {
            wp_set_password('123456', $super_admin_id);
            $user = get_userdata($super_admin_id);
            if (!in_array('school_super_admin', $user->roles)) {
                $user->set_role('school_super_admin');
            }
            update_user_meta($super_admin_id, 'school_user_status', 'APPROVED');
        } else {
            $user_id = wp_insert_user([
                'user_login' => 'schoolsuperadmin',
                'user_email' => 'admin@school.erp',
                'user_pass' => '123456',
                'display_name' => 'School Super Admin',
                'first_name' => 'School Super Admin',
                'role' => 'school_super_admin'
            ]);
            if (!is_wp_error($user_id)) {
                update_user_meta($user_id, 'school_user_status', 'APPROVED');
            }
        }

        self::create_test_user('school_principal', 'principal@school.erp', 'principalpass123', 'School Principal', 'school_principal');
        self::create_test_user('school_teacher', 'teacher@school.erp', 'teacherpass123', 'School Teacher', 'school_teacher');
        self::create_test_user('school_accountant', 'accountant@school.erp', 'accountantpass123', 'School Accountant', 'school_accountant');
        self::create_test_user('school_parent', 'parent@school.erp', 'parentpass123', 'School Parent', 'school_parent');
        self::create_test_user('school_student', 'student@school.erp', 'studentpass123', 'School Student', 'school_student');
    }

    /**
     * Helper to insert test accounts
     */
    private static function create_test_user(string $username, string $email, string $password, string $display_name, string $role) {
        $user_id = username_exists($username);
        if (!$user_id && !email_exists($email)) {
            $user_id = wp_insert_user([
                'user_login' => $username,
                'user_email' => $email,
                'user_pass' => $password,
                'display_name' => $display_name,
                'first_name' => $display_name,
                'role' => $role
            ]);
        }
        if ($user_id && !is_wp_error($user_id)) {
            update_user_meta($user_id, 'school_user_status', 'APPROVED');
        }
    }

    /**
     * Seed initial sample school records for API testing
     */
    private static function seed_sample_records() {
        global $wpdb;

        // 1. Seed Classes
        $table_classes = $wpdb->prefix . 'school_classes';
        if ((int)$wpdb->get_var("SELECT COUNT(*) FROM $table_classes") === 0) {
            $wpdb->insert($table_classes, ['class_name' => 'Grade 10', 'status' => 'ACTIVE']);
            $class_10_id = $wpdb->insert_id;
            $wpdb->insert($table_classes, ['class_name' => 'Grade 11', 'status' => 'ACTIVE']);
            $class_11_id = $wpdb->insert_id;
            $wpdb->insert($table_classes, ['class_name' => 'Grade 12', 'status' => 'ACTIVE']);
            $class_12_id = $wpdb->insert_id;

            // 2. Seed Sections
            $table_sections = $wpdb->prefix . 'school_sections';
            $wpdb->insert($table_sections, ['section_name' => 'Section A', 'class_id' => $class_10_id]);
            $sec_a_id = $wpdb->insert_id;
            $wpdb->insert($table_sections, ['section_name' => 'Section B', 'class_id' => $class_10_id]);
            $wpdb->insert($table_sections, ['section_name' => 'Section A', 'class_id' => $class_11_id]);

            // 3. Seed Teachers
            $table_teachers = $wpdb->prefix . 'school_teachers';
            $wpdb->insert($table_teachers, [
                'employee_code' => 'EMP1001',
                'name' => 'Dr. Robert Carter',
                'mobile' => '9876543210',
                'email' => 'robert.carter@school.erp',
                'qualification' => 'PhD in Mathematics',
                'salary' => 75000.00,
                'joining_date' => '2023-01-15',
                'status' => 'ACTIVE'
            ]);
            $teacher_id = $wpdb->insert_id;

            // 4. Seed Subjects
            $table_subjects = $wpdb->prefix . 'school_subjects';
            $wpdb->insert($table_subjects, [
                'subject_name' => 'Mathematics',
                'subject_code' => 'MATH101',
                'class_id' => $class_10_id,
                'teacher_id' => $teacher_id,
                'status' => 'ACTIVE'
            ]);
            $subject_id = $wpdb->insert_id;

            // 5. Seed Parents
            $table_parents = $wpdb->prefix . 'school_parents';
            $wpdb->insert($table_parents, [
                'father_name' => 'John Doe Sr.',
                'mother_name' => 'Jane Doe',
                'mobile' => '9988776655',
                'email' => 'parent@school.erp',
                'occupation' => 'Software Engineer',
                'address' => '742 Evergreen Terrace'
            ]);
            $parent_id = $wpdb->insert_id;

            // 6. Seed Students
            $table_students = $wpdb->prefix . 'school_students';
            $wpdb->insert($table_students, [
                'admission_no' => 'ADM2026001',
                'roll_no' => '101',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'gender' => 'Male',
                'dob' => '2010-05-14',
                'mobile' => '9000000001',
                'email' => 'student@school.erp',
                'address' => '742 Evergreen Terrace',
                'class_id' => $class_10_id,
                'section_id' => $sec_a_id,
                'parent_id' => $parent_id,
                'status' => 'ACTIVE'
            ]);
            $student_id = $wpdb->insert_id;

            // 7. Seed Attendance
            $table_attendance = $wpdb->prefix . 'school_attendance';
            $wpdb->insert($table_attendance, [
                'student_id' => $student_id,
                'attendance_date' => current_time('Y-m-d'),
                'status' => 'Present',
                'remarks' => 'On time'
            ]);

            // 8. Seed Exams
            $table_exams = $wpdb->prefix . 'school_exams';
            $wpdb->insert($table_exams, [
                'exam_name' => 'Term-1 Midterm Exams',
                'exam_type' => 'Written',
                'start_date' => '2026-09-10',
                'end_date' => '2026-09-17',
                'status' => 'ACTIVE'
            ]);
            $exam_id = $wpdb->insert_id;

            // 9. Seed Marks
            $table_marks = $wpdb->prefix . 'school_marks';
            $wpdb->insert($table_marks, [
                'exam_id' => $exam_id,
                'student_id' => $student_id,
                'subject_id' => $subject_id,
                'marks_obtained' => 92.50,
                'max_marks' => 100.00,
                'remarks' => 'Excellent performance'
            ]);

            // 10. Seed Fees Structures
            $table_fees = $wpdb->prefix . 'school_fees';
            $wpdb->insert($table_fees, [
                'type' => 'STRUCTURE',
                'class_id' => $class_10_id,
                'title' => 'Annual Tuition Fee Grade 10',
                'amount' => 45000.00,
                'due_date' => '2026-07-31',
                'status' => 'ACTIVE'
            ]);
            $fee_structure_id = $wpdb->insert_id;

            // Seed Collection
            $wpdb->insert($table_fees, [
                'type' => 'COLLECTION',
                'student_id' => $student_id,
                'title' => 'Grade 10 Annual Tuition payment installment 1',
                'amount' => 25000.00,
                'due_date' => null,
                'status' => 'PAID',
                'payment_method' => 'Razorpay',
                'transaction_id' => 'pay_rzp_mock12345',
                'paid_at' => current_time('mysql')
            ]);

            // 11. Seed Transport Route
            $table_transport = $wpdb->prefix . 'school_transport';
            $wpdb->insert($table_transport, [
                'route_name' => 'Route-3 Downtown to School',
                'source' => 'Downtown Plaza',
                'destination' => 'High School Campus',
                'vehicle_number' => 'MH-12-PQ-8899',
                'driver_name' => 'Garry Willis',
                'driver_mobile' => '9112233445',
                'status' => 'ACTIVE'
            ]);

            // 12. Seed Library Books
            $table_library = $wpdb->prefix . 'school_library';
            $wpdb->insert($table_library, [
                'type' => 'BOOK',
                'title' => 'Introduction to Calculus',
                'author' => 'I. A. Maron',
                'isbn' => '978-8123902345',
                'status' => 'AVAILABLE'
            ]);
            $book_id = $wpdb->insert_id;

            $wpdb->insert($table_library, [
                'type' => 'ISSUE',
                'book_id' => $book_id,
                'student_id' => $student_id,
                'issue_date' => '2026-06-01',
                'return_date' => '2026-06-15',
                'status' => 'ISSUED'
            ]);

            // 13. Seed Notice Board
            $table_events = $wpdb->prefix . 'school_events';
            $wpdb->insert($table_events, [
                'type' => 'NOTICE',
                'title' => 'Summer Vacation Announcement',
                'description' => 'The school will remain closed for summer holidays from June 20 to July 10, 2026.',
                'event_date' => null,
                'status' => 'ACTIVE'
            ]);

            $wpdb->insert($table_events, [
                'type' => 'EVENT',
                'title' => 'Annual Science Exhibition 2026',
                'description' => 'Students will present their science models in the main auditorium. Parents are invited.',
                'event_date' => '2026-08-05 10:00:00',
                'status' => 'ACTIVE'
            ]);
        }
    }
}
