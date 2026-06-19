<?php
namespace WorkspaceErpApi\Routes;

use WorkspaceErpApi\Controllers\CrmController;
use WorkspaceErpApi\Middleware\RoleMiddleware;

class CrmRoutes {
    public static function register() {
        $controller = new CrmController();
        $namespace = 'workspace-erp/v1';

        register_rest_route($namespace, '/crm/leads', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexLeads'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_crm')
        ]);

        register_rest_route($namespace, '/crm/leads', [
            'methods' => 'POST',
            'callback' => [$controller, 'createLead'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_crm')
        ]);

        register_rest_route($namespace, '/crm/leads/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateLead'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_crm')
        ]);

        register_rest_route($namespace, '/crm/leads/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteLead'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_crm')
        ]);

        register_rest_route($namespace, '/crm/opportunities', [
            'methods' => 'GET',
            'callback' => [$controller, 'indexOpportunities'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_crm')
        ]);
    }
}
