<?php
namespace RealEstateManagementApi\Controllers;

use RealEstateManagementApi\Repositories\PropertyRepository;
use RealEstateManagementApi\Repositories\ProjectRepository;
use RealEstateManagementApi\Services\AuthService;
use WP_REST_Request;

class PropertyController extends BaseController {
    private $propertyRepository;
    private $projectRepository;

    public function __construct() {
        $this->propertyRepository = new PropertyRepository();
        $this->projectRepository = new ProjectRepository();
    }

    /**
     * GET /properties
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'project_id', 'project_name', 'tower', 'unit_number', 'property_type', 'price', 'status', 'created_at'];
        $search_fields = ['project_name', 'tower', 'unit_number', 'property_type'];
        
        $extra_filters = [];
        if (isset($params['project_id'])) {
            $extra_filters['project_id'] = intval($params['project_id']);
        }
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }
        if (isset($params['property_type'])) {
            $extra_filters['property_type'] = sanitize_text_field($params['property_type']);
        }

        $results = $this->propertyRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        return $this->success('Properties retrieved successfully.', $results);
    }

    /**
     * GET /properties/:id
     */
    public function getById(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $property = $this->propertyRepository->findById($id);

        if (!$property) {
            return $this->error('Property not found.', [], 404);
        }

        return $this->success('Property retrieved successfully.', $property);
    }

    /**
     * POST /properties
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['project_id']) || empty($params['unit_number'])) {
            return $this->error('Validation failed: project_id and unit_number are required.');
        }

        $project_id = intval($params['project_id']);
        $project = $this->projectRepository->findById($project_id);
        $project_name = $project ? $project['project_name'] : '';

        $data = [
            'project_id' => $project_id,
            'project_name' => $project_name,
            'tower' => sanitize_text_field($params['tower'] ?? ''),
            'unit_number' => sanitize_text_field($params['unit_number']),
            'property_type' => sanitize_text_field($params['property_type'] ?? 'Apartment'),
            'area_sqft' => isset($params['area_sqft']) ? floatval($params['area_sqft']) : 0.00,
            'bedrooms' => isset($params['bedrooms']) ? intval($params['bedrooms']) : 0,
            'floor' => isset($params['floor']) ? intval($params['floor']) : 0,
            'price' => isset($params['price']) ? floatval($params['price']) : 0.00,
            'status' => sanitize_text_field($params['status'] ?? 'Available')
        ];

        $formats = ['%d', '%s', '%s', '%s', '%s', '%f', '%d', '%d', '%f', '%s'];
        $inserted_id = $this->propertyRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create property.');
        }

        AuthService::logActivity(get_current_user_id(), 'PROPERTY_CREATE', "Created property unit $params[unit_number] ($inserted_id)");

        return $this->success('Property created successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /properties/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $property = $this->propertyRepository->findById($id);

        if (!$property) {
            return $this->error('Property not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['project_id', 'tower', 'unit_number', 'property_type', 'area_sqft', 'bedrooms', 'floor', 'price', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'project_id') {
                    $project_id = intval($params[$field]);
                    $project = $this->projectRepository->findById($project_id);
                    $data['project_id'] = $project_id;
                    $data['project_name'] = $project ? $project['project_name'] : '';
                    $formats[] = '%d';
                    $formats[] = '%s';
                } elseif ($field === 'bedrooms' || $field === 'floor') {
                    $data[$field] = intval($params[$field]);
                    $formats[] = '%d';
                } elseif ($field === 'area_sqft' || $field === 'price') {
                    $data[$field] = floatval($params[$field]);
                    $formats[] = '%f';
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                    $formats[] = '%s';
                }
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->propertyRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update property.');
        }

        AuthService::logActivity(get_current_user_id(), 'PROPERTY_UPDATE', "Updated property ID: $id");

        return $this->success('Property updated successfully.', $this->propertyRepository->findById($id));
    }

    /**
     * DELETE /properties/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $property = $this->propertyRepository->findById($id);

        if (!$property) {
            return $this->error('Property not found.', [], 404);
        }

        $deleted = $this->propertyRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete property.');
        }

        AuthService::logActivity(get_current_user_id(), 'PROPERTY_DELETE', "Soft deleted property ID: $id ($property[unit_number])");

        return $this->success('Property deleted successfully.');
    }
}
