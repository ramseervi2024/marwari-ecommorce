<?php
namespace RealEstateManagementApi\Controllers;

use RealEstateManagementApi\Repositories\SiteVisitRepository;
use RealEstateManagementApi\Repositories\LeadRepository;
use RealEstateManagementApi\Repositories\PropertyRepository;
use RealEstateManagementApi\Services\AuthService;
use WP_REST_Request;

class SiteVisitController extends BaseController {
    private $siteVisitRepository;
    private $leadRepository;
    private $propertyRepository;

    public function __construct() {
        $this->siteVisitRepository = new SiteVisitRepository();
        $this->leadRepository = new LeadRepository();
        $this->propertyRepository = new PropertyRepository();
    }

    /**
     * GET /site-visits
     */
    public function getAll(WP_REST_Request $request) {
        $params = $request->get_params();
        $allowed_sorts = ['id', 'lead_id', 'property_id', 'visit_date', 'status', 'created_at'];
        $search_fields = ['status', 'transport_required'];
        
        $extra_filters = [];
        if (isset($params['lead_id'])) {
            $extra_filters['lead_id'] = intval($params['lead_id']);
        }
        if (isset($params['property_id'])) {
            $extra_filters['property_id'] = intval($params['property_id']);
        }
        if (isset($params['status'])) {
            $extra_filters['status'] = sanitize_text_field($params['status']);
        }

        $results = $this->siteVisitRepository->findAll($params, $allowed_sorts, $search_fields, $extra_filters);
        
        // Enrich data with lead and property details
        if (!empty($results['data'])) {
            foreach ($results['data'] as &$row) {
                $lead = $this->leadRepository->findById(intval($row['lead_id']));
                $row['lead_name'] = $lead ? $lead['name'] : '';
                $row['lead_mobile'] = $lead ? $lead['mobile'] : '';
                
                $property = $this->propertyRepository->findById(intval($row['property_id']));
                $row['property_unit'] = $property ? $property['unit_number'] : '';
                $row['project_name'] = $property ? $property['project_name'] : '';
            }
        }

        return $this->success('Site visits retrieved successfully.', $results);
    }

    /**
     * POST /site-visits
     */
    public function create(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['lead_id']) || empty($params['property_id']) || empty($params['visit_date'])) {
            return $this->error('Validation failed: lead_id, property_id, and visit_date are required.');
        }

        $data = [
            'lead_id' => intval($params['lead_id']),
            'property_id' => intval($params['property_id']),
            'sales_executive_id' => !empty($params['sales_executive_id']) ? intval($params['sales_executive_id']) : null,
            'visit_date' => sanitize_text_field($params['visit_date']),
            'visit_time' => sanitize_text_field($params['visit_time'] ?? '12:00:00'),
            'transport_required' => sanitize_text_field($params['transport_required'] ?? 'No'),
            'feedback' => sanitize_textarea_field($params['feedback'] ?? ''),
            'status' => sanitize_text_field($params['status'] ?? 'Scheduled')
        ];

        $formats = ['%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s'];
        $inserted_id = $this->siteVisitRepository->create($data, $formats);

        if (!$inserted_id) {
            return $this->error('Failed to create site visit record.');
        }

        // Set lead status to "Site Visit Scheduled"
        $this->leadRepository->update(intval($params['lead_id']), [
            'lead_status' => 'Site Visit Scheduled',
            'follow_up_date' => $data['visit_date']
        ], ['%s', '%s']);

        AuthService::logActivity(get_current_user_id(), 'SITE_VISIT_CREATE', "Scheduled site visit ID $inserted_id for lead $params[lead_id]");

        return $this->success('Site visit scheduled successfully.', array_merge(['id' => $inserted_id], $data), 201);
    }

    /**
     * PUT /site-visits/:id
     */
    public function update(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $visit = $this->siteVisitRepository->findById($id);

        if (!$visit) {
            return $this->error('Site visit record not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];
        
        $fields = ['sales_executive_id', 'visit_date', 'visit_time', 'transport_required', 'feedback', 'status'];
        foreach ($fields as $field) {
            if (isset($params[$field])) {
                if ($field === 'sales_executive_id') {
                    $data[$field] = !empty($params[$field]) ? intval($params[$field]) : null;
                    $formats[] = '%d';
                } elseif ($field === 'feedback') {
                    $data[$field] = sanitize_textarea_field($params[$field]);
                    $formats[] = '%s';
                } else {
                    $data[$field] = sanitize_text_field($params[$field]);
                    $formats[] = '%s';
                }
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $updated = $this->siteVisitRepository->update($id, $data, $formats);
        if (!$updated) {
            return $this->error('Failed to update site visit record.');
        }

        AuthService::logActivity(get_current_user_id(), 'SITE_VISIT_UPDATE', "Updated site visit ID: $id");

        return $this->success('Site visit updated successfully.', $this->siteVisitRepository->findById($id));
    }

    /**
     * DELETE /site-visits/:id
     */
    public function delete(WP_REST_Request $request) {
        $id = intval($request->get_param('id'));
        $visit = $this->siteVisitRepository->findById($id);

        if (!$visit) {
            return $this->error('Site visit record not found.', [], 404);
        }

        $deleted = $this->siteVisitRepository->delete($id);
        if (!$deleted) {
            return $this->error('Failed to delete site visit.');
        }

        AuthService::logActivity(get_current_user_id(), 'SITE_VISIT_DELETE', "Soft deleted site visit ID: $id");

        return $this->success('Site visit deleted successfully.');
    }
}
