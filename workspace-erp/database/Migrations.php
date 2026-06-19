<?php
namespace WorkspaceErpApi\Database;

class Migrations {
    
    /**
     * Activate migrations - Creates all tables & user roles
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // JWT Secret Setup
        if (!get_option('workspace_erp_jwt_secret')) {
            update_option('workspace_erp_jwt_secret', bin2hex(random_bytes(32)));
        }

        // ==================== CRM & Lead Management ====================

        // 1. Leads Table
        $table = $wpdb->prefix . 'workspace_leads';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_code varchar(50) DEFAULT NULL,
            company_name varchar(150) NOT NULL,
            contact_person varchar(100) NOT NULL,
            email varchar(100) DEFAULT NULL,
            mobile varchar(20) DEFAULT NULL,
            source varchar(50) DEFAULT NULL,
            inquiry_type varchar(50) DEFAULT NULL,
            seats_required int DEFAULT NULL,
            budget_range varchar(100) DEFAULT NULL,
            preferred_location varchar(150) DEFAULT NULL,
            notes text DEFAULT NULL,
            assigned_to bigint(20) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'NEW',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 2. Opportunities Table
        $table = $wpdb->prefix . 'workspace_opportunities';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_id bigint(20) DEFAULT NULL,
            opportunity_name varchar(150) NOT NULL,
            estimated_value decimal(12,2) DEFAULT 0.00,
            probability int DEFAULT 0,
            stage varchar(50) NOT NULL DEFAULT 'QUALIFICATION',
            expected_close_date date DEFAULT NULL,
            notes text DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'OPEN',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 3. Proposals Table
        $table = $wpdb->prefix . 'workspace_proposals';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_id bigint(20) DEFAULT NULL,
            opportunity_id bigint(20) DEFAULT NULL,
            proposal_no varchar(50) NOT NULL,
            title varchar(150) NOT NULL,
            description text DEFAULT NULL,
            total_amount decimal(12,2) DEFAULT 0.00,
            valid_until date DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'DRAFT',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 4. Site Visits Table
        $table = $wpdb->prefix . 'workspace_site_visits';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_id bigint(20) DEFAULT NULL,
            visit_date datetime NOT NULL,
            building_id bigint(20) DEFAULT NULL,
            contact_person varchar(100) DEFAULT NULL,
            conducted_by bigint(20) DEFAULT NULL,
            feedback text DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'SCHEDULED',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // ==================== Enterprise Client Management ====================

        // 5. Clients Table
        $table = $wpdb->prefix . 'workspace_clients';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            client_code varchar(50) NOT NULL,
            company_name varchar(150) NOT NULL,
            industry varchar(100) DEFAULT NULL,
            contact_person varchar(100) DEFAULT NULL,
            email varchar(100) DEFAULT NULL,
            mobile varchar(20) DEFAULT NULL,
            gst_number varchar(50) DEFAULT NULL,
            pan_number varchar(20) DEFAULT NULL,
            address text DEFAULT NULL,
            city varchar(50) DEFAULT NULL,
            state varchar(50) DEFAULT NULL,
            contract_start date DEFAULT NULL,
            contract_end date DEFAULT NULL,
            total_seats int DEFAULT 0,
            monthly_rent decimal(12,2) DEFAULT 0.00,
            security_deposit decimal(12,2) DEFAULT 0.00,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY client_code (client_code)
        ) $charset_collate;");

        // ==================== Workspace Management ====================

        // 6. Buildings Table
        $table = $wpdb->prefix . 'workspace_buildings';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            building_name varchar(150) NOT NULL,
            address text DEFAULT NULL,
            city varchar(50) DEFAULT NULL,
            state varchar(50) DEFAULT NULL,
            total_floors int DEFAULT 0,
            total_seats int DEFAULT 0,
            amenities text DEFAULT NULL,
            contact_person varchar(100) DEFAULT NULL,
            contact_mobile varchar(20) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 7. Floors Table
        $table = $wpdb->prefix . 'workspace_floors';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            building_id bigint(20) NOT NULL,
            floor_name varchar(50) NOT NULL,
            floor_number int DEFAULT 0,
            total_seats int DEFAULT 0,
            area_sqft decimal(10,2) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 8. Workspaces Table (Cabins, Open Areas, etc.)
        $table = $wpdb->prefix . 'workspace_workspaces';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            building_id bigint(20) NOT NULL,
            floor_id bigint(20) NOT NULL,
            workspace_name varchar(100) NOT NULL,
            workspace_type varchar(50) NOT NULL DEFAULT 'CABIN',
            capacity int DEFAULT 0,
            rate_per_seat decimal(10,2) DEFAULT 0.00,
            amenities text DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'AVAILABLE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 9. Seats Table
        $table = $wpdb->prefix . 'workspace_seats';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            workspace_id bigint(20) NOT NULL,
            seat_number varchar(20) NOT NULL,
            seat_type varchar(30) NOT NULL DEFAULT 'DEDICATED',
            client_id bigint(20) DEFAULT NULL,
            employee_name varchar(100) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'AVAILABLE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 10. Meeting Rooms Table
        $table = $wpdb->prefix . 'workspace_meeting_rooms';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            building_id bigint(20) NOT NULL,
            floor_id bigint(20) DEFAULT NULL,
            room_name varchar(100) NOT NULL,
            capacity int DEFAULT 0,
            rate_per_hour decimal(10,2) DEFAULT 0.00,
            amenities text DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'AVAILABLE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 11. Meeting Room Bookings Table
        $table = $wpdb->prefix . 'workspace_room_bookings';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            room_id bigint(20) NOT NULL,
            client_id bigint(20) DEFAULT NULL,
            booked_by bigint(20) DEFAULT NULL,
            booking_date date NOT NULL,
            start_time time NOT NULL,
            end_time time NOT NULL,
            purpose varchar(200) DEFAULT NULL,
            attendees int DEFAULT 0,
            status varchar(20) NOT NULL DEFAULT 'CONFIRMED',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // ==================== Occupancy Management ====================

        // 12. Occupancy Table
        $table = $wpdb->prefix . 'workspace_occupancy';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            building_id bigint(20) NOT NULL,
            floor_id bigint(20) DEFAULT NULL,
            workspace_id bigint(20) DEFAULT NULL,
            seat_id bigint(20) DEFAULT NULL,
            client_id bigint(20) DEFAULT NULL,
            occupied_from date DEFAULT NULL,
            occupied_to date DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'OCCUPIED',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // ==================== Visitor Management ====================

        // 13. Visitors Table
        $table = $wpdb->prefix . 'workspace_visitors';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            visitor_name varchar(100) NOT NULL,
            company varchar(100) DEFAULT NULL,
            mobile varchar(20) NOT NULL,
            email varchar(100) DEFAULT NULL,
            id_type varchar(50) DEFAULT NULL,
            id_number varchar(50) DEFAULT NULL,
            photo varchar(255) DEFAULT NULL,
            visit_purpose varchar(200) DEFAULT NULL,
            host_client_id bigint(20) DEFAULT NULL,
            host_name varchar(100) DEFAULT NULL,
            building_id bigint(20) DEFAULT NULL,
            check_in datetime DEFAULT NULL,
            check_out datetime DEFAULT NULL,
            pass_code varchar(50) DEFAULT NULL,
            approved_by bigint(20) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'PENDING',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // ==================== Facility Management ====================

        // 14. Tickets Table
        $table = $wpdb->prefix . 'workspace_tickets';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            ticket_no varchar(50) NOT NULL,
            title varchar(200) NOT NULL,
            description text DEFAULT NULL,
            category varchar(50) DEFAULT NULL,
            priority varchar(20) NOT NULL DEFAULT 'MEDIUM',
            building_id bigint(20) DEFAULT NULL,
            floor_id bigint(20) DEFAULT NULL,
            raised_by bigint(20) DEFAULT NULL,
            assigned_to bigint(20) DEFAULT NULL,
            sla_deadline datetime DEFAULT NULL,
            resolved_at datetime DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'OPEN',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY ticket_no (ticket_no)
        ) $charset_collate;");

        // 15. Work Orders Table
        $table = $wpdb->prefix . 'workspace_work_orders';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            ticket_id bigint(20) DEFAULT NULL,
            vendor_id bigint(20) DEFAULT NULL,
            work_description text NOT NULL,
            scheduled_date date DEFAULT NULL,
            completed_date date DEFAULT NULL,
            cost decimal(10,2) DEFAULT 0.00,
            status varchar(20) NOT NULL DEFAULT 'PENDING',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 16. Maintenance Schedule Table
        $table = $wpdb->prefix . 'workspace_maintenance_schedule';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(200) NOT NULL,
            description text DEFAULT NULL,
            building_id bigint(20) DEFAULT NULL,
            frequency varchar(30) DEFAULT 'MONTHLY',
            next_due_date date DEFAULT NULL,
            assigned_to bigint(20) DEFAULT NULL,
            last_completed datetime DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // ==================== Asset Management ====================

        // 17. Assets Table
        $table = $wpdb->prefix . 'workspace_assets';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            asset_code varchar(50) NOT NULL,
            asset_name varchar(150) NOT NULL,
            category varchar(50) DEFAULT NULL,
            building_id bigint(20) DEFAULT NULL,
            floor_id bigint(20) DEFAULT NULL,
            purchase_date date DEFAULT NULL,
            purchase_cost decimal(12,2) DEFAULT 0.00,
            current_value decimal(12,2) DEFAULT 0.00,
            warranty_expiry date DEFAULT NULL,
            vendor_id bigint(20) DEFAULT NULL,
            qr_code varchar(100) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY asset_code (asset_code)
        ) $charset_collate;");

        // 18. Asset Allocations Table
        $table = $wpdb->prefix . 'workspace_asset_allocations';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            asset_id bigint(20) NOT NULL,
            allocated_to varchar(100) DEFAULT NULL,
            client_id bigint(20) DEFAULT NULL,
            building_id bigint(20) DEFAULT NULL,
            floor_id bigint(20) DEFAULT NULL,
            allocated_date date DEFAULT NULL,
            return_date date DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ALLOCATED',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // ==================== Vendor Management ====================

        // 19. Vendors Table
        $table = $wpdb->prefix . 'workspace_vendors';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            vendor_name varchar(150) NOT NULL,
            company_name varchar(150) DEFAULT NULL,
            service_type varchar(100) DEFAULT NULL,
            contact_person varchar(100) DEFAULT NULL,
            email varchar(100) DEFAULT NULL,
            mobile varchar(20) DEFAULT NULL,
            gst_number varchar(50) DEFAULT NULL,
            address text DEFAULT NULL,
            contract_start date DEFAULT NULL,
            contract_end date DEFAULT NULL,
            sla_terms text DEFAULT NULL,
            rating decimal(3,2) DEFAULT 0.00,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 20. Vendor Payments Table
        $table = $wpdb->prefix . 'workspace_vendor_payments';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            vendor_id bigint(20) NOT NULL,
            amount decimal(12,2) NOT NULL DEFAULT 0.00,
            payment_date date DEFAULT NULL,
            payment_method varchar(50) DEFAULT NULL,
            reference_no varchar(100) DEFAULT NULL,
            description text DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'PAID',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // ==================== Billing & Finance ====================

        // 21. Invoices Table
        $table = $wpdb->prefix . 'workspace_invoices';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            invoice_no varchar(50) NOT NULL,
            client_id bigint(20) NOT NULL,
            billing_type varchar(30) DEFAULT 'LEASE',
            billing_month varchar(10) DEFAULT NULL,
            base_amount decimal(12,2) DEFAULT 0.00,
            gst_percentage decimal(5,2) DEFAULT 18.00,
            gst_amount decimal(12,2) DEFAULT 0.00,
            total_amount decimal(12,2) DEFAULT 0.00,
            due_date date DEFAULT NULL,
            notes text DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'PENDING',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY invoice_no (invoice_no)
        ) $charset_collate;");

        // 22. Payments Table
        $table = $wpdb->prefix . 'workspace_payments';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            invoice_id bigint(20) NOT NULL,
            client_id bigint(20) NOT NULL,
            amount decimal(12,2) NOT NULL DEFAULT 0.00,
            payment_date date DEFAULT NULL,
            payment_method varchar(50) DEFAULT NULL,
            transaction_id varchar(100) DEFAULT NULL,
            gateway varchar(50) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'COMPLETED',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 23. Credit Notes Table
        $table = $wpdb->prefix . 'workspace_credit_notes';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            credit_note_no varchar(50) NOT NULL,
            invoice_id bigint(20) DEFAULT NULL,
            client_id bigint(20) NOT NULL,
            amount decimal(12,2) NOT NULL DEFAULT 0.00,
            reason text DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ISSUED',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // ==================== Sustainability Management ====================

        // 24. Energy Usage Table
        $table = $wpdb->prefix . 'workspace_energy_usage';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            building_id bigint(20) NOT NULL,
            reading_date date NOT NULL,
            meter_id varchar(50) DEFAULT NULL,
            consumption_kwh decimal(10,2) DEFAULT 0.00,
            cost decimal(10,2) DEFAULT 0.00,
            source varchar(50) DEFAULT 'GRID',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 25. Water Usage Table
        $table = $wpdb->prefix . 'workspace_water_usage';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            building_id bigint(20) NOT NULL,
            reading_date date NOT NULL,
            consumption_liters decimal(10,2) DEFAULT 0.00,
            cost decimal(10,2) DEFAULT 0.00,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 26. Waste Management Table
        $table = $wpdb->prefix . 'workspace_waste_management';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            building_id bigint(20) NOT NULL,
            collection_date date NOT NULL,
            waste_type varchar(50) DEFAULT 'GENERAL',
            quantity_kg decimal(10,2) DEFAULT 0.00,
            recycled_kg decimal(10,2) DEFAULT 0.00,
            disposal_method varchar(50) DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 27. Carbon Tracking Table
        $table = $wpdb->prefix . 'workspace_carbon_tracking';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            building_id bigint(20) NOT NULL,
            tracking_month varchar(10) NOT NULL,
            co2_emissions_kg decimal(10,2) DEFAULT 0.00,
            offset_kg decimal(10,2) DEFAULT 0.00,
            net_emissions_kg decimal(10,2) DEFAULT 0.00,
            notes text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // ==================== Smart Building ====================

        // 28. IoT Devices Table
        $table = $wpdb->prefix . 'workspace_iot_devices';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            device_name varchar(100) NOT NULL,
            device_type varchar(50) NOT NULL,
            building_id bigint(20) DEFAULT NULL,
            floor_id bigint(20) DEFAULT NULL,
            serial_number varchar(100) DEFAULT NULL,
            manufacturer varchar(100) DEFAULT NULL,
            installed_date date DEFAULT NULL,
            last_maintenance date DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 29. Sensor Data Table
        $table = $wpdb->prefix . 'workspace_sensor_data';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            device_id bigint(20) NOT NULL,
            sensor_type varchar(50) NOT NULL,
            value decimal(10,2) DEFAULT NULL,
            unit varchar(20) DEFAULT NULL,
            recorded_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 30. Access Logs Table
        $table = $wpdb->prefix . 'workspace_access_logs';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            person_name varchar(100) DEFAULT NULL,
            person_type varchar(30) DEFAULT 'EMPLOYEE',
            access_point varchar(100) DEFAULT NULL,
            building_id bigint(20) DEFAULT NULL,
            access_type varchar(20) DEFAULT 'ENTRY',
            method varchar(30) DEFAULT 'RFID',
            recorded_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // ==================== HR & Workforce ====================

        // 31. Employees Table
        $table = $wpdb->prefix . 'workspace_employees';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            employee_code varchar(50) NOT NULL,
            name varchar(100) NOT NULL,
            department varchar(50) DEFAULT NULL,
            designation varchar(100) DEFAULT NULL,
            mobile varchar(20) DEFAULT NULL,
            email varchar(100) DEFAULT NULL,
            joining_date date DEFAULT NULL,
            salary decimal(12,2) DEFAULT 0.00,
            shift varchar(20) DEFAULT 'DAY',
            building_id bigint(20) DEFAULT NULL,
            photo varchar(255) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY employee_code (employee_code)
        ) $charset_collate;");

        // 32. Attendance Table
        $table = $wpdb->prefix . 'workspace_attendance';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            employee_id bigint(20) NOT NULL,
            attendance_date date NOT NULL,
            check_in time DEFAULT NULL,
            check_out time DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'PRESENT',
            remarks text DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 33. Leaves Table
        $table = $wpdb->prefix . 'workspace_leaves';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            employee_id bigint(20) NOT NULL,
            leave_type varchar(30) NOT NULL,
            from_date date NOT NULL,
            to_date date NOT NULL,
            reason text DEFAULT NULL,
            approved_by bigint(20) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'PENDING',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 34. Shifts Table
        $table = $wpdb->prefix . 'workspace_shifts';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            shift_name varchar(50) NOT NULL,
            start_time time NOT NULL,
            end_time time NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // ==================== Community & Tenant Services ====================

        // 35. Announcements Table
        $table = $wpdb->prefix . 'workspace_announcements';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(200) NOT NULL,
            description text DEFAULT NULL,
            target_audience varchar(50) DEFAULT 'ALL',
            building_id bigint(20) DEFAULT NULL,
            published_at datetime DEFAULT NULL,
            expires_at datetime DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'ACTIVE',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 36. Events Table
        $table = $wpdb->prefix . 'workspace_events';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(200) NOT NULL,
            description text DEFAULT NULL,
            event_date datetime DEFAULT NULL,
            location varchar(200) DEFAULT NULL,
            building_id bigint(20) DEFAULT NULL,
            max_attendees int DEFAULT NULL,
            organizer varchar(100) DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'UPCOMING',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 37. Service Requests Table
        $table = $wpdb->prefix . 'workspace_service_requests';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            request_no varchar(50) NOT NULL,
            client_id bigint(20) DEFAULT NULL,
            request_type varchar(50) NOT NULL,
            description text DEFAULT NULL,
            building_id bigint(20) DEFAULT NULL,
            floor_id bigint(20) DEFAULT NULL,
            raised_by bigint(20) DEFAULT NULL,
            assigned_to bigint(20) DEFAULT NULL,
            priority varchar(20) NOT NULL DEFAULT 'MEDIUM',
            resolved_at datetime DEFAULT NULL,
            status varchar(20) NOT NULL DEFAULT 'OPEN',
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY request_no (request_no)
        ) $charset_collate;");

        // ==================== Notification & Logging ====================

        // 38. Notifications Table
        $table = $wpdb->prefix . 'workspace_notifications';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            type varchar(20) NOT NULL,
            recipient varchar(150) NOT NULL,
            subject varchar(150) DEFAULT NULL,
            message text NOT NULL,
            channel varchar(20) DEFAULT 'EMAIL',
            status varchar(20) NOT NULL DEFAULT 'SENT',
            sent_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at datetime DEFAULT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // 39. Activity Logs Table
        $table = $wpdb->prefix . 'workspace_activity_logs';
        dbDelta("CREATE TABLE $table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) DEFAULT NULL,
            action varchar(100) NOT NULL,
            details text DEFAULT NULL,
            ip_address varchar(50) DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;");

        // Register Roles & Seed Test Accounts
        self::register_roles();
        self::seed_test_accounts();
        self::seed_sample_records();
    }

    /**
     * Register Workspace ERP user roles
     */
    private static function register_roles() {
        $roles_to_remove = ['workspace_super_admin', 'workspace_sales_manager', 'workspace_facility_manager', 'workspace_finance_manager', 'workspace_hr_manager', 'workspace_tenant_admin', 'workspace_tenant_employee', 'workspace_security_staff', 'workspace_vendor'];
        foreach ($roles_to_remove as $r) { remove_role($r); }

        $super_admin_caps = [
            'read' => true, 'manage_workspace' => true, 'manage_users' => true,
            'manage_crm' => true, 'manage_clients' => true, 'manage_workspaces' => true,
            'manage_facilities' => true, 'manage_assets' => true, 'manage_vendors' => true,
            'manage_billing' => true, 'manage_sustainability' => true, 'manage_smartbuilding' => true,
            'manage_hr' => true, 'manage_community' => true, 'manage_visitors' => true,
            'view_reports' => true, 'view_dashboard' => true, 'manage_notifications' => true,
        ];

        $sales_caps = [
            'read' => true, 'view_dashboard' => true, 'manage_crm' => true, 'manage_clients' => true, 'view_reports' => true
        ];

        $facility_caps = [
            'read' => true, 'view_dashboard' => true, 'manage_facilities' => true,
            'manage_vendors' => true, 'manage_assets' => true, 'manage_visitors' => true, 'view_reports' => true
        ];

        $finance_caps = [
            'read' => true, 'view_dashboard' => true, 'manage_billing' => true, 'view_reports' => true
        ];

        $hr_caps = [
            'read' => true, 'view_dashboard' => true, 'manage_hr' => true, 'view_reports' => true
        ];

        $tenant_admin_caps = [
            'read' => true, 'view_dashboard' => true, 'manage_visitors' => true,
            'view_workspace' => true, 'manage_community' => true, 'view_billing' => true
        ];

        $tenant_employee_caps = [
            'read' => true, 'view_workspace' => true, 'manage_visitors' => true, 'view_billing' => true
        ];

        $security_caps = [
            'read' => true, 'manage_visitors' => true
        ];

        $vendor_caps = [
            'read' => true, 'view_assigned_orders' => true
        ];

        add_role('workspace_super_admin', 'Workspace Super Admin', $super_admin_caps);
        add_role('workspace_sales_manager', 'Sales Manager', $sales_caps);
        add_role('workspace_facility_manager', 'Facility Manager', $facility_caps);
        add_role('workspace_finance_manager', 'Finance Manager', $finance_caps);
        add_role('workspace_hr_manager', 'HR Manager', $hr_caps);
        add_role('workspace_tenant_admin', 'Tenant Admin', $tenant_admin_caps);
        add_role('workspace_tenant_employee', 'Tenant Employee', $tenant_employee_caps);
        add_role('workspace_security_staff', 'Security Staff', $security_caps);
        add_role('workspace_vendor', 'Vendor', $vendor_caps);

        // Ensure Administrator has all permissions
        $admin_role = get_role('administrator');
        if ($admin_role) {
            foreach ($super_admin_caps as $cap => $grant) {
                $admin_role->add_cap($cap);
            }
        }
    }

    /**
     * Seed test/demo accounts
     */
    private static function seed_test_accounts() {
        $superadmin_id = username_exists('workspace_superadmin');
        if ($superadmin_id) {
            wp_set_password('123456', $superadmin_id);
            $user = get_userdata($superadmin_id);
            if (!in_array('workspace_super_admin', $user->roles)) $user->set_role('workspace_super_admin');
            update_user_meta($superadmin_id, 'workspace_user_status', 'APPROVED');
        } else {
            $uid = wp_insert_user(['user_login' => 'workspace_superadmin', 'user_email' => 'superadmin@workspace.erp', 'user_pass' => '123456', 'display_name' => 'Workspace Super Admin', 'first_name' => 'Workspace Super Admin', 'role' => 'workspace_super_admin']);
            if (!is_wp_error($uid)) update_user_meta($uid, 'workspace_user_status', 'APPROVED');
        }

        self::create_test_user('workspace_sales', 'sales@workspace.erp', 'salespass123', 'Sales Manager', 'workspace_sales_manager');
        self::create_test_user('workspace_facility', 'facility@workspace.erp', 'facilitypass123', 'Facility Manager', 'workspace_facility_manager');
        self::create_test_user('workspace_finance', 'finance@workspace.erp', 'financepass123', 'Finance Manager', 'workspace_finance_manager');
        self::create_test_user('workspace_hr', 'hr@workspace.erp', 'hrpass123', 'HR Manager', 'workspace_hr_manager');
        self::create_test_user('workspace_tenant', 'tenant@workspace.erp', 'tenantpass123', 'Tenant Admin', 'workspace_tenant_admin');
        self::create_test_user('workspace_employee', 'employee@workspace.erp', 'employeepass123', 'Tenant Employee', 'workspace_tenant_employee');
        self::create_test_user('workspace_security', 'security@workspace.erp', 'securitypass123', 'Security Staff', 'workspace_security_staff');
        self::create_test_user('workspace_vendor_user', 'vendor@workspace.erp', 'vendorpass123', 'Vendor User', 'workspace_vendor');
    }

    private static function create_test_user(string $username, string $email, string $password, string $display_name, string $role) {
        $user_id = username_exists($username);
        if (!$user_id && !email_exists($email)) {
            $user_id = wp_insert_user(['user_login' => $username, 'user_email' => $email, 'user_pass' => $password, 'display_name' => $display_name, 'first_name' => $display_name, 'role' => $role]);
        }
        if ($user_id && !is_wp_error($user_id)) {
            update_user_meta($user_id, 'workspace_user_status', 'APPROVED');
        }
    }

    /**
     * Seed sample records for API testing
     */
    private static function seed_sample_records() {
        global $wpdb;

        // Only seed if buildings table is empty
        $table_buildings = $wpdb->prefix . 'workspace_buildings';
        if ((int)$wpdb->get_var("SELECT COUNT(*) FROM $table_buildings") > 0) return;

        // 1. Buildings
        $wpdb->insert($table_buildings, ['building_name' => 'Aurbis Tower Alpha', 'address' => '100 Business Park Rd, Outer Ring Road', 'city' => 'Bangalore', 'state' => 'Karnataka', 'total_floors' => 12, 'total_seats' => 1200, 'amenities' => 'Cafeteria, Gym, Parking, EV Charging, Lounge', 'status' => 'ACTIVE']);
        $building1 = $wpdb->insert_id;

        $wpdb->insert($table_buildings, ['building_name' => 'Aurbis Hub Beta', 'address' => '200 Tech Corridor, Whitefield', 'city' => 'Bangalore', 'state' => 'Karnataka', 'total_floors' => 8, 'total_seats' => 600, 'amenities' => 'Cafeteria, Parking, Conference Center', 'status' => 'ACTIVE']);
        $building2 = $wpdb->insert_id;

        // 2. Floors
        $table_floors = $wpdb->prefix . 'workspace_floors';
        $wpdb->insert($table_floors, ['building_id' => $building1, 'floor_name' => 'Ground Floor', 'floor_number' => 0, 'total_seats' => 100, 'area_sqft' => 8500.00, 'status' => 'ACTIVE']);
        $floor1 = $wpdb->insert_id;
        $wpdb->insert($table_floors, ['building_id' => $building1, 'floor_name' => 'First Floor', 'floor_number' => 1, 'total_seats' => 120, 'area_sqft' => 9200.00, 'status' => 'ACTIVE']);
        $floor2 = $wpdb->insert_id;

        // 3. Workspaces
        $table_ws = $wpdb->prefix . 'workspace_workspaces';
        $wpdb->insert($table_ws, ['building_id' => $building1, 'floor_id' => $floor1, 'workspace_name' => 'Cabin A-101', 'workspace_type' => 'CABIN', 'capacity' => 6, 'rate_per_seat' => 12000.00, 'status' => 'AVAILABLE']);
        $ws1 = $wpdb->insert_id;
        $wpdb->insert($table_ws, ['building_id' => $building1, 'floor_id' => $floor2, 'workspace_name' => 'Open Bay B-201', 'workspace_type' => 'OPEN_BAY', 'capacity' => 30, 'rate_per_seat' => 8500.00, 'status' => 'AVAILABLE']);

        // 4. Seats
        $table_seats = $wpdb->prefix . 'workspace_seats';
        for ($i = 1; $i <= 6; $i++) {
            $wpdb->insert($table_seats, ['workspace_id' => $ws1, 'seat_number' => "A101-S$i", 'seat_type' => 'DEDICATED', 'status' => ($i <= 4) ? 'OCCUPIED' : 'AVAILABLE']);
        }

        // 5. Meeting Rooms
        $table_mr = $wpdb->prefix . 'workspace_meeting_rooms';
        $wpdb->insert($table_mr, ['building_id' => $building1, 'floor_id' => $floor1, 'room_name' => 'Board Room - Infinity', 'capacity' => 20, 'rate_per_hour' => 2500.00, 'amenities' => 'Projector, Whiteboard, Video Conferencing', 'status' => 'AVAILABLE']);
        $wpdb->insert($table_mr, ['building_id' => $building1, 'floor_id' => $floor2, 'room_name' => 'Conference Room - Summit', 'capacity' => 10, 'rate_per_hour' => 1500.00, 'amenities' => 'TV Screen, Whiteboard', 'status' => 'AVAILABLE']);

        // 6. Clients
        $table_clients = $wpdb->prefix . 'workspace_clients';
        $wpdb->insert($table_clients, ['client_code' => 'CLI-001', 'company_name' => 'TechNova Solutions Pvt Ltd', 'industry' => 'IT Services', 'contact_person' => 'Arun Kumar', 'email' => 'arun@technova.io', 'mobile' => '9876543210', 'gst_number' => '29AABCT1234F1Z5', 'address' => '742 Silicon Block, Koramangala', 'city' => 'Bangalore', 'state' => 'Karnataka', 'contract_start' => '2026-01-01', 'contract_end' => '2027-12-31', 'total_seats' => 25, 'monthly_rent' => 212500.00, 'security_deposit' => 637500.00, 'status' => 'ACTIVE']);
        $client1 = $wpdb->insert_id;

        $wpdb->insert($table_clients, ['client_code' => 'CLI-002', 'company_name' => 'FinEdge Capital', 'industry' => 'FinTech', 'contact_person' => 'Priya Mehta', 'email' => 'priya@finedge.com', 'mobile' => '9123456789', 'gst_number' => '29AABCF5678G1Z8', 'address' => '55 Finance Hub, Indiranagar', 'city' => 'Bangalore', 'state' => 'Karnataka', 'contract_start' => '2026-03-01', 'contract_end' => '2027-02-28', 'total_seats' => 15, 'monthly_rent' => 127500.00, 'security_deposit' => 382500.00, 'status' => 'ACTIVE']);

        // 7. Leads
        $table_leads = $wpdb->prefix . 'workspace_leads';
        $wpdb->insert($table_leads, ['lead_code' => 'LEAD-001', 'company_name' => 'CloudSync Technologies', 'contact_person' => 'Ravi Sharma', 'email' => 'ravi@cloudsync.io', 'mobile' => '9988776655', 'source' => 'Website', 'inquiry_type' => 'Managed Office', 'seats_required' => 50, 'budget_range' => '₹4L - ₹6L/month', 'preferred_location' => 'Outer Ring Road', 'status' => 'NEW']);
        $wpdb->insert($table_leads, ['lead_code' => 'LEAD-002', 'company_name' => 'GreenLeaf Organics', 'contact_person' => 'Sneha Patel', 'email' => 'sneha@greenleaf.co', 'mobile' => '9112233445', 'source' => 'Broker', 'inquiry_type' => 'Coworking', 'seats_required' => 10, 'budget_range' => '₹80K - ₹1.2L/month', 'preferred_location' => 'Whitefield', 'status' => 'CONTACTED']);

        // 8. Invoices
        $table_invoices = $wpdb->prefix . 'workspace_invoices';
        $wpdb->insert($table_invoices, ['invoice_no' => 'INV-2026-001', 'client_id' => $client1, 'billing_type' => 'LEASE', 'billing_month' => '2026-06', 'base_amount' => 212500.00, 'gst_percentage' => 18.00, 'gst_amount' => 38250.00, 'total_amount' => 250750.00, 'due_date' => '2026-06-30', 'status' => 'PENDING']);

        // 9. Employees
        $table_emp = $wpdb->prefix . 'workspace_employees';
        $wpdb->insert($table_emp, ['employee_code' => 'EMP-001', 'name' => 'Mahesh Rao', 'department' => 'Facilities', 'designation' => 'Facility Executive', 'mobile' => '9876000001', 'email' => 'mahesh@aurbis.com', 'joining_date' => '2024-06-15', 'salary' => 45000.00, 'shift' => 'DAY', 'building_id' => $building1, 'status' => 'ACTIVE']);
        $wpdb->insert($table_emp, ['employee_code' => 'EMP-002', 'name' => 'Sunil Verma', 'department' => 'Security', 'designation' => 'Security Guard', 'mobile' => '9876000002', 'email' => 'sunil@aurbis.com', 'joining_date' => '2025-01-10', 'salary' => 25000.00, 'shift' => 'NIGHT', 'building_id' => $building1, 'status' => 'ACTIVE']);

        // 10. Vendors
        $table_vendors = $wpdb->prefix . 'workspace_vendors';
        $wpdb->insert($table_vendors, ['vendor_name' => 'CleanPro Services', 'company_name' => 'CleanPro Facilities Pvt Ltd', 'service_type' => 'Housekeeping', 'contact_person' => 'Ramesh Gupta', 'email' => 'ramesh@cleanpro.in', 'mobile' => '9800011122', 'contract_start' => '2026-01-01', 'contract_end' => '2026-12-31', 'rating' => 4.20, 'status' => 'ACTIVE']);
        $wpdb->insert($table_vendors, ['vendor_name' => 'SecureShield Guards', 'company_name' => 'SecureShield Pvt Ltd', 'service_type' => 'Security', 'contact_person' => 'Ajay Singh', 'email' => 'ajay@secureshield.in', 'mobile' => '9800022233', 'contract_start' => '2026-01-01', 'contract_end' => '2027-03-31', 'rating' => 4.50, 'status' => 'ACTIVE']);

        // 11. Assets
        $table_assets = $wpdb->prefix . 'workspace_assets';
        $wpdb->insert($table_assets, ['asset_code' => 'AST-001', 'asset_name' => 'Dell Monitor 27"', 'category' => 'IT Equipment', 'building_id' => $building1, 'floor_id' => $floor1, 'purchase_date' => '2025-06-10', 'purchase_cost' => 18500.00, 'current_value' => 14800.00, 'warranty_expiry' => '2028-06-10', 'status' => 'ACTIVE']);
        $wpdb->insert($table_assets, ['asset_code' => 'AST-002', 'asset_name' => 'Ergonomic Office Chair', 'category' => 'Furniture', 'building_id' => $building1, 'floor_id' => $floor1, 'purchase_date' => '2025-03-20', 'purchase_cost' => 12000.00, 'current_value' => 10800.00, 'warranty_expiry' => '2030-03-20', 'status' => 'ACTIVE']);

        // 12. Announcements
        $table_ann = $wpdb->prefix . 'workspace_announcements';
        $wpdb->insert($table_ann, ['title' => 'Scheduled Power Maintenance', 'description' => 'Power backup testing will be conducted on June 25, 2026 from 10 PM to 6 AM. Minimal disruption expected.', 'target_audience' => 'ALL', 'building_id' => $building1, 'published_at' => current_time('mysql'), 'status' => 'ACTIVE']);

        // 13. Visitors
        $table_vis = $wpdb->prefix . 'workspace_visitors';
        $wpdb->insert($table_vis, ['visitor_name' => 'Amit Patel', 'company' => 'CloudSync Technologies', 'mobile' => '9988776655', 'email' => 'amit@cloudsync.io', 'visit_purpose' => 'Business Meeting', 'host_client_id' => $client1, 'host_name' => 'Arun Kumar', 'building_id' => $building1, 'check_in' => current_time('mysql'), 'pass_code' => 'VIS-' . strtoupper(substr(md5(time()), 0, 8)), 'status' => 'CHECKED_IN']);

        // 14. Tickets
        $table_tickets = $wpdb->prefix . 'workspace_tickets';
        $wpdb->insert($table_tickets, ['ticket_no' => 'TKT-001', 'title' => 'AC not working in Cabin A-101', 'description' => 'The air conditioning unit in Cabin A-101 is blowing warm air. Tenant reported discomfort.', 'category' => 'HVAC', 'priority' => 'HIGH', 'building_id' => $building1, 'floor_id' => $floor1, 'status' => 'OPEN']);

        // 15. Energy Usage
        $table_energy = $wpdb->prefix . 'workspace_energy_usage';
        $wpdb->insert($table_energy, ['building_id' => $building1, 'reading_date' => current_time('Y-m-d'), 'meter_id' => 'MTR-ALPHA-001', 'consumption_kwh' => 2450.50, 'cost' => 19604.00, 'source' => 'GRID']);

        // 16. IoT Devices
        $table_iot = $wpdb->prefix . 'workspace_iot_devices';
        $wpdb->insert($table_iot, ['device_name' => 'Air Quality Sensor - GF', 'device_type' => 'AIR_QUALITY', 'building_id' => $building1, 'floor_id' => $floor1, 'serial_number' => 'AQS-001-2025', 'manufacturer' => 'SensorTech', 'installed_date' => '2025-08-15', 'status' => 'ACTIVE']);
        $wpdb->insert($table_iot, ['device_name' => 'Occupancy Counter - Entry', 'device_type' => 'OCCUPANCY_SENSOR', 'building_id' => $building1, 'floor_id' => $floor1, 'serial_number' => 'OCC-001-2025', 'manufacturer' => 'SmartSpace', 'installed_date' => '2025-09-01', 'status' => 'ACTIVE']);
    }
}
