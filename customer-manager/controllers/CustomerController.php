<?php
namespace CustomerManager\Controllers;

use CustomerManager\Repositories\CustomerRepository;
use CustomerManager\Models\Customer;
use WP_REST_Request;
use WP_REST_Response;

class CustomerController {
    
    private CustomerRepository $repository;

    public function __construct() {
        $this->repository = new CustomerRepository();
    }

    /**
     * Retrieve a list of active customers with pagination, filtering, search, and sort options.
     */
    public function index(WP_REST_Request $request): WP_REST_Response {
        $params = [
            'page' => $request->get_param('page'),
            'limit' => $request->get_param('limit'),
            'search' => $request->get_param('search'),
            'sort' => $request->get_param('sort'),
            'order' => $request->get_param('order'),
            'status' => $request->get_param('status'),
        ];

        $results = $this->repository->findAll($params);
        
        $limit = isset($params['limit']) ? max(1, (int)$params['limit']) : 10;
        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $total_pages = ceil($results['total'] / $limit);

        // Serialize results
        $serialized = array_map(fn($c) => $c->toArray(), $results['data']);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Customers retrieved successfully',
            'data' => [
                'customers' => $serialized,
                'pagination' => [
                    'total' => $results['total'],
                    'page' => $page,
                    'limit' => $limit,
                    'pages' => $total_pages
                ]
            ]
        ], 200);
    }

    /**
     * Get a single customer by ID.
     */
    public function show(WP_REST_Request $request): WP_REST_Response {
        $id = (int)$request->get_param('id');
        $customer = $this->repository->findById($id);

        if (!$customer) {
            return new WP_REST_Response([
                'success' => false,
                'message' => "Customer with ID $id not found."
            ], 404);
        }

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Customer retrieved successfully',
            'data' => $customer->toArray()
        ], 200);
    }

    /**
     * Create a new customer.
     */
    public function create(WP_REST_Request $request): WP_REST_Response {
        $first_name = trim($request->get_param('first_name') ?: '');
        $last_name = trim($request->get_param('last_name') ?: '');
        $email = sanitize_email($request->get_param('email') ?: '');
        $phone = trim($request->get_param('phone') ?: '');

        // Validation Rules
        $errors = [];
        if (strlen($first_name) < 2) {
            $errors[] = 'first_name is required and must be at least 2 characters.';
        }
        if (strlen($last_name) < 2) {
            $errors[] = 'last_name is required and must be at least 2 characters.';
        }
        if (!is_email($email)) {
            $errors[] = 'email is required and must be a valid email address.';
        }
        if (empty($phone)) {
            $errors[] = 'phone number is required.';
        }

        if (!empty($errors)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 400);
        }

        // Uniqueness constraints check
        if ($this->repository->findByEmail($email)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Unique constraint failed: A customer with this email address already exists.'
            ], 409);
        }

        if ($this->repository->findByPhone($phone)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Unique constraint failed: A customer with this phone number already exists.'
            ], 409);
        }

        // Populate Model
        $customer = new Customer();
        $customer->first_name = $first_name;
        $customer->last_name = $last_name;
        $customer->email = $email;
        $customer->phone = $phone;
        $customer->address = sanitize_text_field($request->get_param('address'));
        $customer->city = sanitize_text_field($request->get_param('city'));
        $customer->state = sanitize_text_field($request->get_param('state'));
        $customer->country = sanitize_text_field($request->get_param('country'));
        $customer->postal_code = sanitize_text_field($request->get_param('postal_code'));
        $customer->status = strtoupper($request->get_param('status')) === 'INACTIVE' ? 'INACTIVE' : 'ACTIVE';

        $inserted_id = $this->repository->create($customer);

        if (!$inserted_id) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Failed to create customer record.'
            ], 500);
        }

        $new_customer = $this->repository->findById($inserted_id);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Customer created successfully',
            'data' => $new_customer->toArray()
        ], 201);
    }

    /**
     * Update an existing customer.
     */
    public function update(WP_REST_Request $request): WP_REST_Response {
        $id = (int)$request->get_param('id');
        $customer = $this->repository->findById($id);

        if (!$customer) {
            return new WP_REST_Response([
                'success' => false,
                'message' => "Customer with ID $id not found."
            ], 404);
        }

        $data = $request->get_json_params() ?: $request->get_body_params() ?: [];
        if (empty($data)) {
            // fallback for simple form requests
            $data = $request->get_params();
        }

        // Validate fields if provided
        $errors = [];
        if (isset($data['first_name']) && strlen(trim($data['first_name'])) < 2) {
            $errors[] = 'first_name must be at least 2 characters.';
        }
        if (isset($data['last_name']) && strlen(trim($data['last_name'])) < 2) {
            $errors[] = 'last_name must be at least 2 characters.';
        }
        if (isset($data['email'])) {
            $email = sanitize_email($data['email']);
            if (!is_email($email)) {
                $errors[] = 'email must be a valid email address.';
            } else {
                $duplicate = $this->repository->findByEmail($email, $id);
                if ($duplicate) {
                    return new WP_REST_Response([
                        'success' => false,
                        'message' => 'Unique constraint failed: A customer with this email address already exists.'
                    ], 409);
                }
                $data['email'] = $email;
            }
        }
        if (isset($data['phone'])) {
            $phone = trim($data['phone']);
            $duplicate = $this->repository->findByPhone($phone, $id);
            if ($duplicate) {
                return new WP_REST_Response([
                    'success' => false,
                    'message' => 'Unique constraint failed: A customer with this phone number already exists.'
                ], 409);
            }
            $data['phone'] = $phone;
        }

        if (!empty($errors)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ], 400);
        }

        // Sanitization
        foreach ($data as $key => $val) {
            if ($key === 'address') {
                $data[$key] = sanitize_textarea_field($val);
            } elseif (in_array($key, ['city', 'state', 'country', 'postal_code'])) {
                $data[$key] = sanitize_text_field($val);
            } elseif ($key === 'status') {
                $data[$key] = strtoupper($val) === 'INACTIVE' ? 'INACTIVE' : 'ACTIVE';
            }
        }

        $updated = $this->repository->update($id, $data);

        if (!$updated) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'No fields updated or database transaction failed.'
            ], 500);
        }

        $updated_customer = $this->repository->findById($id);

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Customer updated successfully',
            'data' => $updated_customer->toArray()
        ], 200);
    }

    /**
     * Soft delete a customer.
     */
    public function delete(WP_REST_Request $request): WP_REST_Response {
        $id = (int)$request->get_param('id');
        $customer = $this->repository->findById($id);

        if (!$customer) {
            return new WP_REST_Response([
                'success' => false,
                'message' => "Customer with ID $id not found."
            ], 404);
        }

        $deleted = $this->repository->delete($id);

        if (!$deleted) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Failed to delete customer record.'
            ], 500);
        }

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Customer deleted successfully'
        ], 200);
    }

    /**
     * Export all customers to a downloadable CSV stream.
     */
    public function export(WP_REST_Request $request) {
        $result = $this->repository->findAll(['limit' => 999999]); // Load all customers without pagination
        $customers = $result['data'];

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=customers-' . date('Y-m-d') . '.csv');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        
        // Write header
        fputcsv($output, [
            'ID', 'First Name', 'Last Name', 'Email', 'Phone', 
            'Address', 'City', 'State', 'Country', 'Postal Code', 'Status', 'Created At'
        ]);

        foreach ($customers as $c) {
            fputcsv($output, [
                $c->id,
                $c->first_name,
                $c->last_name,
                $c->email,
                $c->phone,
                $c->address,
                $c->city,
                $c->state,
                $c->country,
                $c->postal_code,
                $c->status,
                $c->created_at
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Import customers from an uploaded CSV file.
     */
    public function import(WP_REST_Request $request): WP_REST_Response {
        if (empty($_FILES['file'])) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'No file uploaded. Please upload a valid CSV file.'
            ], 400);
        }

        $file = $_FILES['file'];
        if (pathinfo($file['name'], PATHINFO_EXTENSION) !== 'csv') {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Invalid file format. Only CSV files are supported.'
            ], 400);
        }

        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Failed to open uploaded file.'
            ], 500);
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Empty CSV file.'
            ], 400);
        }

        // Clean headers (remove BOM/non-ascii, strip whitespace, lowercase)
        $headers = array_map(function($h) {
            $h = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h);
            return strtolower(trim($h));
        }, $headers);

        $imported = 0;
        $failed = 0;
        $errors = [];
        $row_num = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $row_num++;
            
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Ensure columns count matches headers count
            if (count($row) !== count($headers)) {
                $failed++;
                $errors[] = "Row $row_num: Column count mismatch (Expected " . count($headers) . ", got " . count($row) . ").";
                continue;
            }

            $data = array_combine($headers, $row);
            
            // Normalize mapping
            $first_name = trim($data['first_name'] ?? $data['first name'] ?? '');
            $last_name = trim($data['last_name'] ?? $data['last name'] ?? '');
            $email = sanitize_email($data['email'] ?? '');
            $phone = trim($data['phone'] ?? '');

            // Validation rules
            if (strlen($first_name) < 2 || strlen($last_name) < 2) {
                $failed++;
                $errors[] = "Row $row_num: First and last name must be at least 2 characters.";
                continue;
            }
            if (!is_email($email)) {
                $failed++;
                $errors[] = "Row $row_num: Invalid email format '$email'.";
                continue;
            }
            if (empty($phone)) {
                $failed++;
                $errors[] = "Row $row_num: Phone number is required.";
                continue;
            }

            // Constraint checks
            if ($this->repository->findByEmail($email)) {
                $failed++;
                $errors[] = "Row $row_num: Duplicate constraint. Email '$email' already exists in database.";
                continue;
            }
            if ($this->repository->findByPhone($phone)) {
                $failed++;
                $errors[] = "Row $row_num: Duplicate constraint. Phone '$phone' already exists in database.";
                continue;
            }

            // Create customer model
            $customer = new Customer();
            $customer->first_name = $first_name;
            $customer->last_name = $last_name;
            $customer->email = $email;
            $customer->phone = $phone;
            $customer->address = sanitize_textarea_field($data['address'] ?? '');
            $customer->city = sanitize_text_field($data['city'] ?? '');
            $customer->state = sanitize_text_field($data['state'] ?? '');
            $customer->country = sanitize_text_field($data['country'] ?? '');
            
            $postal_col = $data['postal_code'] ?? $data['postal code'] ?? $data['zip'] ?? '';
            $customer->postal_code = sanitize_text_field($postal_col);
            
            $status_col = strtoupper(trim($data['status'] ?? 'ACTIVE'));
            $customer->status = $status_col === 'INACTIVE' ? 'INACTIVE' : 'ACTIVE';

            $inserted_id = $this->repository->create($customer);
            if ($inserted_id) {
                $imported++;
            } else {
                $failed++;
                $errors[] = "Row $row_num: Database insert failed.";
            }
        }
        fclose($handle);

        return new WP_REST_Response([
            'success' => true,
            'message' => "CSV import complete. Successfully imported: $imported records. Failed: $failed records.",
            'data' => [
                'imported_count' => $imported,
                'failed_count' => $failed,
                'errors' => $errors
            ]
        ], 200);
    }
}
