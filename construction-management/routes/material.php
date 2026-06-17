<?php
namespace ConstructionManagementApi\Routes;

use ConstructionManagementApi\Controllers\MaterialController;
use ConstructionManagementApi\Middleware\RoleMiddleware;

class MaterialRoutes {
    
    public static function register() {
        $controller = new MaterialController();
        $namespace = 'construction-management/v1';

        // GET /materials
        register_rest_route($namespace, '/materials', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAll'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // GET /materials/:id
        register_rest_route($namespace, '/materials/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$controller, 'getById'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /materials
        register_rest_route($namespace, '/materials', [
            'methods' => 'POST',
            'callback' => [$controller, 'create'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_materials')
        ]);

        // PUT /materials/:id
        register_rest_route($namespace, '/materials/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'update'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_materials')
        ]);

        // DELETE /materials/:id
        register_rest_route($namespace, '/materials/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'delete'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_materials')
        ]);

        // --- SUPPLIERS ---

        // GET /suppliers
        register_rest_route($namespace, '/suppliers', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAllSuppliers'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /suppliers
        register_rest_route($namespace, '/suppliers', [
            'methods' => 'POST',
            'callback' => [$controller, 'createSupplier'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_materials')
        ]);

        // PUT /suppliers/:id
        register_rest_route($namespace, '/suppliers/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateSupplier'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_materials')
        ]);

        // DELETE /suppliers/:id
        register_rest_route($namespace, '/suppliers/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteSupplier'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_materials')
        ]);

        // --- PURCHASES (P.O.) ---

        // GET /purchases
        register_rest_route($namespace, '/purchases', [
            'methods' => 'GET',
            'callback' => [$controller, 'getAllPurchases'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);

        // POST /purchases
        register_rest_route($namespace, '/purchases', [
            'methods' => 'POST',
            'callback' => [$controller, 'createPurchase'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_materials')
        ]);

        // PUT /purchases/:id
        register_rest_route($namespace, '/purchases/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updatePurchase'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_materials')
        ]);

        // DELETE /purchases/:id
        register_rest_route($namespace, '/purchases/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deletePurchase'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_materials')
        ]);
    }
}
