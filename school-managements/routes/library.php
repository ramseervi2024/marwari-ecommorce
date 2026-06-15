<?php
namespace SchoolManagementApi\Routes;

use SchoolManagementApi\Controllers\LibraryController;
use SchoolManagementApi\Middleware\RoleMiddleware;

class LibraryRoutes {
    
    public static function register() {
        $controller = new LibraryController();
        $namespace = 'school-management/v1';

        // Books
        register_rest_route($namespace, '/library/books', [
            'methods' => 'GET',
            'callback' => [$controller, 'getBooks'],
            'permission_callback' => RoleMiddleware::hasCapability('read')
        ]);
        register_rest_route($namespace, '/library/books', [
            'methods' => 'POST',
            'callback' => [$controller, 'createBook'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_library')
        ]);
        register_rest_route($namespace, '/library/books/(?P<id>\d+)', [
            'methods' => 'PUT',
            'callback' => [$controller, 'updateBook'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_library')
        ]);
        register_rest_route($namespace, '/library/books/(?P<id>\d+)', [
            'methods' => 'DELETE',
            'callback' => [$controller, 'deleteBook'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_library')
        ]);

        // Issue
        register_rest_route($namespace, '/library/issue', [
            'methods' => 'POST',
            'callback' => [$controller, 'issueBook'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_library')
        ]);

        // Return
        register_rest_route($namespace, '/library/return', [
            'methods' => 'POST',
            'callback' => [$controller, 'returnBook'],
            'permission_callback' => RoleMiddleware::hasCapability('manage_library')
        ]);
    }
}
