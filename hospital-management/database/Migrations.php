<?php
namespace HospitalManagementApi\Database;

class Migrations {
    
    /**
     * Activate migrations - Creates tables & user roles
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        
        // 1. JWT Secret Setup
        if (!get_option('hospital_management_jwt_secret')) {
            update_option('hospital_management_jwt_secret', bin2hex(random_bytes(32)));
        }
        
        // 2. Patients Table
        $table_patients = $wpdb->prefix . 'hospital_patients';
        $sql_patients = "CREATE TABLE $table_patients (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            patient_code varchar(50) NOT NULL,
            name varchar(100) NOT NULL,
            gender varchar(20) DEFAULT NULL,
            dob date DEFAULT NULL,
            mobile varchar(20) DEFAULT NULL,
            email varchar(100) DEFAULT NULL,
            address text DEFAULT NULL,
            blood_group varchar(10) DEFAULT NULL,
            emergency_contact varchar(100) DEFAULT NULL,
            insurance_number varchar(100) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY patient_code (patient_code)
        ) $charset_collate;";
        dbDelta($sql_patients);

        // 3. Doctors Table
        $table_doctors = $wpdb->prefix . 'hospital_doctors';
        $sql_doctors = "CREATE TABLE $table_doctors (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            doctor_code varchar(50) NOT NULL,
            name varchar(100) NOT NULL,
            specialization varchar(150) NOT NULL,
            qualification varchar(150) DEFAULT NULL,
            mobile varchar(20) NOT NULL,
            email varchar(100) NOT NULL,
            consultation_fee decimal(10,2) NOT NULL DEFAULT 0.00,
            experience int(3) NOT NULL DEFAULT 0,
            schedule text DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY doctor_code (doctor_code)
        ) $charset_collate;";
        dbDelta($sql_doctors);

        // 4. Appointments Table
        $table_appointments = $wpdb->prefix . 'hospital_appointments';
        $sql_appointments = "CREATE TABLE $table_appointments (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            patient_id bigint(20) NOT NULL,
            doctor_id bigint(20) NOT NULL,
            appointment_date date NOT NULL,
            appointment_time time NOT NULL,
            appointment_type varchar(50) DEFAULT 'General', -- General, Telemedicine, Followup
            status varchar(20) NOT NULL DEFAULT 'Scheduled', -- Scheduled, Completed, Cancelled, No Show
            remarks text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_appointments);

        // 5. OPD Visits Table
        $table_opd = $wpdb->prefix . 'hospital_opd';
        $sql_opd = "CREATE TABLE $table_opd (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            patient_id bigint(20) NOT NULL,
            doctor_id bigint(20) NOT NULL,
            visit_date date NOT NULL,
            symptoms text DEFAULT NULL,
            diagnosis text DEFAULT NULL,
            prescription text DEFAULT NULL,
            consultation_fee decimal(10,2) NOT NULL DEFAULT 0.00,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_opd);

        // 6. IPD Admissions Table
        $table_ipd = $wpdb->prefix . 'hospital_ipd';
        $sql_ipd = "CREATE TABLE $table_ipd (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            patient_id bigint(20) NOT NULL,
            doctor_id bigint(20) NOT NULL,
            admission_date datetime NOT NULL,
            discharge_date datetime DEFAULT NULL,
            ward varchar(100) DEFAULT NULL, -- General, ICU, Deluxe, Semi-Private
            room_number varchar(50) DEFAULT NULL,
            bed_number varchar(50) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ADMITTED', -- ADMITTED, DISCHARGED
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_ipd);

        // 7. Prescriptions Table
        $table_prescriptions = $wpdb->prefix . 'hospital_prescriptions';
        $sql_prescriptions = "CREATE TABLE $table_prescriptions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            patient_id bigint(20) NOT NULL,
            doctor_id bigint(20) NOT NULL,
            medicine text NOT NULL,
            dosage varchar(150) DEFAULT NULL,
            duration varchar(100) DEFAULT NULL,
            instructions text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_prescriptions);

        // 8. Billing Table
        $table_billing = $wpdb->prefix . 'hospital_billing';
        $sql_billing = "CREATE TABLE $table_billing (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            patient_id bigint(20) NOT NULL,
            bill_number varchar(50) NOT NULL,
            consultation_charges decimal(10,2) NOT NULL DEFAULT 0.00,
            room_charges decimal(10,2) NOT NULL DEFAULT 0.00,
            lab_charges decimal(10,2) NOT NULL DEFAULT 0.00,
            medicine_charges decimal(10,2) NOT NULL DEFAULT 0.00,
            other_charges decimal(10,2) NOT NULL DEFAULT 0.00,
            discount decimal(10,2) NOT NULL DEFAULT 0.00,
            tax decimal(10,2) NOT NULL DEFAULT 0.00,
            total_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            paid_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            due_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            status varchar(20) NOT NULL DEFAULT 'PENDING', -- PAID, PENDING, PARTIAL
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY bill_number (bill_number)
        ) $charset_collate;";
        dbDelta($sql_billing);

        // 9. Pharmacy Inventory Table
        $table_pharmacy = $wpdb->prefix . 'hospital_pharmacy';
        $sql_pharmacy = "CREATE TABLE $table_pharmacy (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            medicine_name varchar(150) NOT NULL,
            batch_number varchar(50) NOT NULL,
            manufacturer varchar(100) DEFAULT NULL,
            purchase_price decimal(10,2) NOT NULL DEFAULT 0.00,
            selling_price decimal(10,2) NOT NULL DEFAULT 0.00,
            quantity int(11) NOT NULL DEFAULT 0,
            expiry_date date NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_pharmacy);

        // 10. Lab Tests Catalog Table
        $table_lab_tests = $wpdb->prefix . 'hospital_lab_tests';
        $sql_lab_tests = "CREATE TABLE $table_lab_tests (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            test_name varchar(150) NOT NULL,
            test_code varchar(50) NOT NULL,
            price decimal(10,2) NOT NULL DEFAULT 0.00,
            description text DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY test_code (test_code)
        ) $charset_collate;";
        dbDelta($sql_lab_tests);

        // 11. Lab Reports Table
        $table_lab_reports = $wpdb->prefix . 'hospital_lab_reports';
        $sql_lab_reports = "CREATE TABLE $table_lab_reports (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            patient_id bigint(20) NOT NULL,
            doctor_id bigint(20) NOT NULL,
            test_id bigint(20) NOT NULL,
            report_file varchar(255) DEFAULT NULL,
            remarks text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_lab_reports);

        // 12. Doctor Schedules/Shifts Table
        $table_schedules = $wpdb->prefix . 'hospital_schedules';
        $sql_schedules = "CREATE TABLE $table_schedules (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            doctor_id bigint(20) NOT NULL,
            day varchar(20) NOT NULL, -- Monday, Tuesday, etc.
            start_time time NOT NULL,
            end_time time NOT NULL,
            availability varchar(20) NOT NULL DEFAULT 'AVAILABLE', -- AVAILABLE, ON_LEAVE
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_schedules);

        // 13. Documents/Records Table
        $table_documents = $wpdb->prefix . 'hospital_documents';
        $sql_documents = "CREATE TABLE $table_documents (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            related_id bigint(20) NOT NULL,
            related_type varchar(20) NOT NULL, -- PATIENT, DOCTOR, PRESCRIPTION, REPORT
            document_type varchar(50) NOT NULL, -- Prescription Scan, Lab PDF, ID Proof
            file_url varchar(255) NOT NULL,
            media_id bigint(20) NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql_documents);

        // 14. Activity Logs Table
        $table_logs = $wpdb->prefix . 'hospital_activity_logs';
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

        // 14.5 Self-correct patient email link for default test user
        $wpdb->query("UPDATE {$wpdb->prefix}hospital_patients SET email = 'patient@hospital.erp' WHERE email = 'priya.nair@yahoo.com' AND deleted_at IS NULL");

        // 15. Register Custom Roles and Capabilities
        self::register_roles();
        self::seed_test_accounts();
        self::seed_sample_records();
    }
    
    /**
     * Register Custom Hospital ERP Roles
     */
    private static function register_roles() {
        remove_role('hospital_super_admin');
        remove_role('hospital_doctor');
        remove_role('hospital_receptionist');
        remove_role('hospital_pharmacist');
        remove_role('hospital_lab_technician');
        remove_role('hospital_patient');
        
        $super_admin_caps = [
            'read' => true,
            'manage_hospital' => true,
            'manage_users' => true,
            'manage_patients' => true,
            'manage_doctors' => true,
            'manage_appointments' => true,
            'manage_opd_ipd' => true,
            'manage_billing' => true,
            'manage_pharmacy' => true,
            'manage_laboratory' => true,
            'view_reports' => true,
            'view_dashboard' => true,
        ];
        
        $doctor_caps = [
            'read' => true,
            'view_dashboard' => true,
            'view_patients' => true,
            'write_prescriptions' => true,
            'view_lab_reports' => true,
            'manage_appointments' => true,
        ];
        
        $receptionist_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_patients' => true,
            'manage_appointments' => true,
            'manage_billing' => true,
        ];
        
        $pharmacist_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_pharmacy' => true,
            'view_prescriptions' => true,
        ];
        
        $lab_tech_caps = [
            'read' => true,
            'view_dashboard' => true,
            'manage_laboratory' => true,
            'upload_reports' => true,
        ];
        
        $patient_caps = [
            'read' => true,
            'view_own_medical' => true,
            'book_appointment' => true,
        ];
        
        add_role('hospital_super_admin', 'Hospital Super Admin', $super_admin_caps);
        add_role('hospital_doctor', 'Hospital Doctor', $doctor_caps);
        add_role('hospital_receptionist', 'Hospital Receptionist', $receptionist_caps);
        add_role('hospital_pharmacist', 'Hospital Pharmacist', $pharmacist_caps);
        add_role('hospital_lab_technician', 'Hospital Lab Technician', $lab_tech_caps);
        add_role('hospital_patient', 'Hospital Patient', $patient_caps);

        // Ensure WordPress Admin has permissions
        $admin_role = get_role('administrator');
        if ($admin_role) {
            foreach ($super_admin_caps as $cap => $grant) {
                $admin_role->add_cap($cap);
            }
        }
    }

    /**
     * Seed credentials for testing roles
     */
    private static function seed_test_accounts() {
        $super_admin_id = username_exists('hospitalsuperadmin');
        if ($super_admin_id) {
            wp_set_password('123456', $super_admin_id);
            $user = get_userdata($super_admin_id);
            if (!in_array('hospital_super_admin', $user->roles)) {
                $user->set_role('hospital_super_admin');
            }
            update_user_meta($super_admin_id, 'hospital_user_status', 'APPROVED');
        } else {
            $user_id = wp_insert_user([
                'user_login' => 'hospitalsuperadmin',
                'user_email' => 'admin@hospital.erp',
                'user_pass' => '123456',
                'display_name' => 'Hospital Super Admin',
                'first_name' => 'Hospital Super Admin',
                'role' => 'hospital_super_admin'
            ]);
            if (!is_wp_error($user_id)) {
                update_user_meta($user_id, 'hospital_user_status', 'APPROVED');
            }
        }

        self::create_test_user('hospital_doctor', 'doctor@hospital.erp', 'doctorpass123', 'Hospital Doctor', 'hospital_doctor');
        self::create_test_user('hospital_receptionist', 'receptionist@hospital.erp', 'receptionistpass123', 'Hospital Receptionist', 'hospital_receptionist');
        self::create_test_user('hospital_pharmacist', 'pharmacist@hospital.erp', 'pharmacistpass123', 'Hospital Pharmacist', 'hospital_pharmacist');
        self::create_test_user('hospital_lab_technician', 'labtech@hospital.erp', 'labpass123', 'Hospital Lab Tech', 'hospital_lab_technician');
        self::create_test_user('hospital_patient', 'patient@hospital.erp', 'patientpass123', 'Hospital Patient User', 'hospital_patient');
    }

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
            update_user_meta($user_id, 'hospital_user_status', 'APPROVED');
        }
    }

    /**
     * Seed initial sample clinic records for API testing
     */
    private static function seed_sample_records() {
        global $wpdb;

        // 1. Seed Doctors
        $table_doctors = $wpdb->prefix . 'hospital_doctors';
        if ((int)$wpdb->get_var("SELECT COUNT(*) FROM $table_doctors") === 0) {
            $wpdb->insert($table_doctors, [
                'doctor_code' => 'DOC1001',
                'name' => 'Dr. Ramesh Sharma',
                'specialization' => 'Cardiology',
                'qualification' => 'MBBS, MD, DM (Cardiology)',
                'mobile' => '9876543211',
                'email' => 'ramesh.sharma@hospital.erp',
                'consultation_fee' => 500.00,
                'experience' => 12,
                'status' => 'ACTIVE'
            ]);
            $doc1_id = $wpdb->insert_id;

            $wpdb->insert($table_doctors, [
                'doctor_code' => 'DOC1002',
                'name' => 'Dr. Anjali Verma',
                'specialization' => 'Pediatrics',
                'qualification' => 'MBBS, DCH, DNB',
                'mobile' => '9876543212',
                'email' => 'anjali.verma@hospital.erp',
                'consultation_fee' => 400.00,
                'experience' => 8,
                'status' => 'ACTIVE'
            ]);
            $doc2_id = $wpdb->insert_id;

            // 2. Seed Patients
            $table_patients = $wpdb->prefix . 'hospital_patients';
            $wpdb->insert($table_patients, [
                'patient_code' => 'PAT2026001',
                'name' => 'Amit Patel',
                'gender' => 'Male',
                'dob' => '1984-06-15',
                'mobile' => '9123456780',
                'email' => 'amit.patel@gmail.com',
                'address' => '102, Shivalik Residency, Ahmedabad, Gujarat',
                'blood_group' => 'O+',
                'emergency_contact' => 'Sonal Patel - 9123456781',
                'insurance_number' => 'INS-CARDIAC-9871',
                'status' => 'ACTIVE'
            ]);
            $pat1_id = $wpdb->insert_id;

            $wpdb->insert($table_patients, [
                'patient_code' => 'PAT2026002',
                'name' => 'Priya Nair',
                'gender' => 'Female',
                'dob' => '1992-11-20',
                'mobile' => '9123456782',
                'email' => 'patient@hospital.erp',
                'address' => '504, Windsor Heights, Mumbai, MH',
                'blood_group' => 'A-',
                'emergency_contact' => 'Rajesh Nair - 9123456783',
                'insurance_number' => 'INS-MAX-7762',
                'status' => 'ACTIVE'
            ]);
            $pat2_id = $wpdb->insert_id;

            // 3. Seed Schedules
            $table_schedules = $wpdb->prefix . 'hospital_schedules';
            $wpdb->insert($table_schedules, [
                'doctor_id' => $doc1_id,
                'day' => 'Monday',
                'start_time' => '09:00:00',
                'end_time' => '13:00:00',
                'availability' => 'AVAILABLE'
            ]);
            $wpdb->insert($table_schedules, [
                'doctor_id' => $doc2_id,
                'day' => 'Tuesday',
                'start_time' => '10:00:00',
                'end_time' => '14:00:00',
                'availability' => 'AVAILABLE'
            ]);

            // 4. Seed Appointments
            $table_appointments = $wpdb->prefix . 'hospital_appointments';
            $wpdb->insert($table_appointments, [
                'patient_id' => $pat1_id,
                'doctor_id' => $doc1_id,
                'appointment_date' => current_time('Y-m-d'),
                'appointment_time' => '10:30:00',
                'appointment_type' => 'General',
                'status' => 'Scheduled',
                'remarks' => 'Routine cardiac checkup consultation'
            ]);
            $wpdb->insert($table_appointments, [
                'patient_id' => $pat2_id,
                'doctor_id' => $doc2_id,
                'appointment_date' => current_time('Y-m-d'),
                'appointment_time' => '11:15:00',
                'appointment_type' => 'Followup',
                'status' => 'Completed',
                'remarks' => 'Pediatric follow-up check'
            ]);

            // 5. Seed OPD Visit
            $table_opd = $wpdb->prefix . 'hospital_opd';
            $wpdb->insert($table_opd, [
                'patient_id' => $pat2_id,
                'doctor_id' => $doc2_id,
                'visit_date' => current_time('Y-m-d'),
                'symptoms' => 'Mild cold, cough and fever for 2 days.',
                'diagnosis' => 'Viral Bronchitis',
                'prescription' => 'Paracetamol 250mg TDS x 3 days, Levocetirizine 5mg HS x 5 days',
                'consultation_fee' => 400.00
            ]);

            // 6. Seed IPD Admission
            $table_ipd = $wpdb->prefix . 'hospital_ipd';
            $wpdb->insert($table_ipd, [
                'patient_id' => $pat1_id,
                'doctor_id' => $doc1_id,
                'admission_date' => date('Y-m-d H:i:s', strtotime('-1 days')),
                'discharge_date' => null,
                'ward' => 'ICU',
                'room_number' => 'ICU-B',
                'bed_number' => 'Bed-04',
                'status' => 'ADMITTED'
            ]);

            // 7. Seed Prescriptions
            $table_prescriptions = $wpdb->prefix . 'hospital_prescriptions';
            $wpdb->insert($table_prescriptions, [
                'patient_id' => $pat1_id,
                'doctor_id' => $doc1_id,
                'medicine' => 'Atorvastatin 40mg, Aspirin 75mg',
                'dosage' => 'Atorvastatin: 1 tablet daily (night), Aspirin: 1 tablet daily (morning)',
                'duration' => '30 Days',
                'instructions' => 'Take Atorvastatin after dinner. Report immediately if muscle pain occurs.'
            ]);

            // 8. Seed Pharmacy Medicines
            $table_pharmacy = $wpdb->prefix . 'hospital_pharmacy';
            $wpdb->insert($table_pharmacy, [
                'medicine_name' => 'Paracetamol 650mg (Dolo)',
                'batch_number' => 'BT88291',
                'manufacturer' => 'Micro Labs Ltd',
                'purchase_price' => 12.00,
                'selling_price' => 30.00,
                'quantity' => 150,
                'expiry_date' => '2027-10-31',
                'status' => 'ACTIVE'
            ]);
            $wpdb->insert($table_pharmacy, [
                'medicine_name' => 'Amoxicillin 500mg',
                'batch_number' => 'AMX12290',
                'manufacturer' => 'Cipla Ltd',
                'purchase_price' => 45.00,
                'selling_price' => 85.00,
                'quantity' => 80,
                'expiry_date' => '2026-12-31',
                'status' => 'ACTIVE'
            ]);

            // 9. Seed Lab Tests Catalog
            $table_lab_tests = $wpdb->prefix . 'hospital_lab_tests';
            $wpdb->insert($table_lab_tests, [
                'test_name' => 'Complete Blood Count (CBC)',
                'test_code' => 'CBC01',
                'price' => 350.00,
                'description' => 'Evaluates overall health and detects a wide range of disorders.',
                'status' => 'ACTIVE'
            ]);
            $cbc_id = $wpdb->insert_id;

            $wpdb->insert($table_lab_tests, [
                'test_name' => 'Lipid Profile',
                'test_code' => 'LIP02',
                'price' => 650.00,
                'description' => 'Measures cholesterol and triglycerides levels in blood.',
                'status' => 'ACTIVE'
            ]);
            $lipid_id = $wpdb->insert_id;

            // 10. Seed Lab Report
            $table_lab_reports = $wpdb->prefix . 'hospital_lab_reports';
            $wpdb->insert($table_lab_reports, [
                'patient_id' => $pat2_id,
                'doctor_id' => $doc2_id,
                'test_id' => $cbc_id,
                'report_file' => 'https://domain.com/wp-content/uploads/2026/06/cbc_report_pat2.pdf',
                'remarks' => 'All parameters within standard reference intervals. Hemoglobin: 13.8 g/dL.'
            ]);

            // 11. Seed Bill
            $table_billing = $wpdb->prefix . 'hospital_billing';
            $wpdb->insert($table_billing, [
                'patient_id' => $pat2_id,
                'bill_number' => 'BILL-2026001',
                'consultation_charges' => 400.00,
                'room_charges' => 0.00,
                'lab_charges' => 350.00,
                'medicine_charges' => 85.00,
                'other_charges' => 0.00,
                'discount' => 50.00,
                'tax' => 45.00,
                'total_amount' => 830.00,
                'paid_amount' => 830.00,
                'due_amount' => 0.00,
                'status' => 'PAID'
            ]);
        }
    }
}
