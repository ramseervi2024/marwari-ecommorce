<?php
namespace SchoolManagementApi\Controllers;

use SchoolManagementApi\Repositories\LibraryRepository;
use SchoolManagementApi\Services\AuthService;
use WP_REST_Request;

class LibraryController extends BaseController {
    private $repository;

    public function __construct() {
        $this->repository = new LibraryRepository();
    }

    // --- Books CRUD ---

    public function getBooks(WP_REST_Request $request) {
        $params = $request->get_params();
        $filters = ['type' => 'BOOK'];
        if (!empty($params['status'])) {
            $filters['status'] = sanitize_text_field($params['status']);
        }
        $result = $this->repository->findAll($params, ['id', 'title', 'author', 'isbn', 'status'], ['title', 'author', 'isbn'], $filters);
        return $this->success('Library books fetched successfully', $result);
    }

    public function createBook(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['title']) || empty($params['author'])) {
            return $this->error('Validation failed: title and author are required.');
        }

        $data = [
            'type' => 'BOOK',
            'title' => sanitize_text_field($params['title']),
            'author' => sanitize_text_field($params['author']),
            'isbn' => isset($params['isbn']) ? sanitize_text_field($params['isbn']) : null,
            'book_id' => null,
            'student_id' => null,
            'issue_date' => null,
            'return_date' => null,
            'actual_return_date' => null,
            'status' => 'AVAILABLE',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s'];
        $id = $this->repository->create($data, $formats);

        if (!$id) {
            return $this->error('Failed to register book.');
        }

        AuthService::logActivity(get_current_user_id(), 'CREATE_BOOK', "Added book: {$params['title']} (ID: $id)");
        return $this->success('Book registered successfully', array_merge(['id' => $id], $data), 201);
    }

    public function updateBook(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $book = $this->repository->findById($id);

        if (!$book || $book['type'] !== 'BOOK') {
            return $this->error('Book not found.', [], 404);
        }

        $params = $request->get_json_params();
        $data = [];
        $formats = [];

        $fields = ['title' => '%s', 'author' => '%s', 'isbn' => '%s', 'status' => '%s'];
        foreach ($fields as $field => $format) {
            if (isset($params[$field])) {
                $data[$field] = sanitize_text_field($params[$field]);
                $formats[] = $format;
            }
        }

        if (empty($data)) {
            return $this->error('No parameters to update.');
        }

        $data['updated_at'] = current_time('mysql');
        $formats[] = '%s';

        $this->repository->update($id, $data, $formats);
        return $this->success('Book details updated successfully', $this->repository->findById($id));
    }

    public function deleteBook(WP_REST_Request $request) {
        $id = (int)$request->get_param('id');
        $book = $this->repository->findById($id);

        if (!$book || $book['type'] !== 'BOOK') {
            return $this->error('Book not found.', [], 404);
        }

        $this->repository->delete($id);
        return $this->success('Book removed from catalog');
    }

    // --- Book Issues & Returns ---

    public function issueBook(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['book_id']) || empty($params['student_id'])) {
            return $this->error('Validation failed: book_id and student_id are required.');
        }

        $book = $this->repository->findById((int)$params['book_id']);
        if (!$book || $book['type'] !== 'BOOK' || $book['status'] !== 'AVAILABLE') {
            return $this->error('Book is currently not available for issue.');
        }

        // Create Issue log
        $data = [
            'type' => 'ISSUE',
            'title' => null,
            'author' => null,
            'isbn' => null,
            'book_id' => (int)$params['book_id'],
            'student_id' => (int)$params['student_id'],
            'issue_date' => current_time('Y-m-d'),
            'return_date' => isset($params['return_date']) ? sanitize_text_field($params['return_date']) : date('Y-m-d', strtotime('+14 days')),
            'actual_return_date' => null,
            'status' => 'ISSUED',
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ];

        $formats = ['%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s'];
        $id = $this->repository->create($data, $formats);

        if (!$id) {
            return $this->error('Failed to record book issue.');
        }

        // Set book status to ISSUED
        $this->repository->update((int)$params['book_id'], ['status' => 'ISSUED', 'updated_at' => current_time('mysql')], ['%s', '%s']);

        AuthService::logActivity(get_current_user_id(), 'BOOK_ISSUE', "Issued book ID: {$params['book_id']} to student ID: {$params['student_id']}");
        return $this->success('Book issued successfully', array_merge(['id' => $id], $data), 201);
    }

    public function returnBook(WP_REST_Request $request) {
        $params = $request->get_json_params();

        if (empty($params['issue_id'])) {
            return $this->error('Validation failed: issue_id is required.');
        }

        $issue = $this->repository->findById((int)$params['issue_id']);
        if (!$issue || $issue['type'] !== 'ISSUE' || $issue['status'] !== 'ISSUED') {
            return $this->error('Active book issue entry not found.');
        }

        // Update issue record
        $this->repository->update((int)$params['issue_id'], [
            'status' => 'RETURNED',
            'actual_return_date' => current_time('Y-m-d'),
            'updated_at' => current_time('mysql')
        ], ['%s', '%s', '%s']);

        // Set book status back to AVAILABLE
        $this->repository->update((int)$issue['book_id'], [
            'status' => 'AVAILABLE',
            'updated_at' => current_time('mysql')
        ], ['%s', '%s']);

        AuthService::logActivity(get_current_user_id(), 'BOOK_RETURN', "Returned book ID: {$issue['book_id']} from issue record ID: {$params['issue_id']}");
        return $this->success('Book returned successfully');
    }
}
