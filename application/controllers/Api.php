<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    private $api_key;
    
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        
        // Set API key - Change this to a secure key in production
        $this->api_key = 'your_secure_api_key_here_change_in_production';
    }

    /**
     * Verify API key from request headers or query parameter
     * 
     * @return bool True if valid, false otherwise
     */
    private function verify_api_key() {
        // Check Authorization header
        $headers = getallheaders();
        $auth_header = isset($headers['Authorization']) ? $headers['Authorization'] : 
                      (isset($headers['authorization']) ? $headers['authorization'] : '');
        
        if (!empty($auth_header)) {
            // Support "Bearer TOKEN" format
            if (strpos($auth_header, 'Bearer ') === 0) {
                $token = substr($auth_header, 7);
                return $token === $this->api_key;
            }
            return $auth_header === $this->api_key;
        }
        
        // Check query parameter as fallback
        $api_key = isset($_GET['api_key']) ? $_GET['api_key'] : '';
        return $api_key === $this->api_key;
    }

    /**
     * Send JSON response
     * 
     * @param array $data Response data
     * @param int $status_code HTTP status code
     */
    private function json_response($data, $status_code = 200) {
        http_response_code($status_code);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * API endpoint to get all inquiries
     * 
     * GET /api/inquiries
     * 
     * Query Parameters:
     * - api_key: Your API key (required if not in Authorization header)
     * - status: Filter by status (pending, contacted, approved, rejected)
     * - loan_type: Filter by loan type (personal, business, home, car)
     * - is_exported: Filter by export status (0 = not exported, 1 = exported)
     * - only_unexported: If set to 1, returns only unexported inquiries
     * - limit: Limit number of results
     * - offset: Offset for pagination
     * - auto_mark: If set to 1, automatically marks returned inquiries as exported
     */
    public function inquiries() {
        // Verify API key
        if (!$this->verify_api_key()) {
            $this->json_response([
                'success' => false,
                'error' => 'Unauthorized - Invalid or missing API key',
                'message' => 'Please provide a valid API key in the Authorization header or api_key query parameter'
            ], 401);
        }
        
        try {
            $inquiry_model = new Inquiry_model();
            
            // Build filters from query parameters
            $filters = [];
            
            if (isset($_GET['status']) && !empty($_GET['status'])) {
                $filters['status'] = $_GET['status'];
            }
            
            if (isset($_GET['loan_type']) && !empty($_GET['loan_type'])) {
                $filters['loan_type'] = $_GET['loan_type'];
            }
            
            if (isset($_GET['limit']) && is_numeric($_GET['limit'])) {
                $filters['limit'] = intval($_GET['limit']);
            }
            
            if (isset($_GET['offset']) && is_numeric($_GET['offset'])) {
                $filters['offset'] = intval($_GET['offset']);
            }
            
            // Handle export filter
            $only_unexported = isset($_GET['only_unexported']) && $_GET['only_unexported'] == '1';
            
            if ($only_unexported) {
                $filters['is_exported'] = 0;
            } elseif (isset($_GET['is_exported']) && in_array($_GET['is_exported'], ['0', '1'])) {
                $filters['is_exported'] = intval($_GET['is_exported']);
            }
            
            // Get inquiries
            $inquiries = $inquiry_model->get_all($filters);
            
            // Auto-mark as exported if requested
            $auto_mark = isset($_GET['auto_mark']) && $_GET['auto_mark'] == '1';
            if ($auto_mark && !empty($inquiries)) {
                $ids = array_column($inquiries, 'id');
                $inquiry_model->mark_as_exported($ids);
            }
            
            $this->json_response([
                'success' => true,
                'count' => count($inquiries),
                'filters' => $filters,
                'auto_marked' => $auto_mark,
                'data' => $inquiries
            ], 200);
            
        } catch (Exception $e) {
            $this->json_response([
                'success' => false,
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to mark inquiries as exported
     * 
     * POST /api/mark_exported
     * 
     * Body Parameters (JSON or form-data):
     * - ids: Array of inquiry IDs to mark as exported
     * 
     * Example JSON body:
     * {
     *   "ids": [1, 2, 3, 4, 5]
     * }
     */
    public function mark_exported() {
        // Verify API key
        if (!$this->verify_api_key()) {
            $this->json_response([
                'success' => false,
                'error' => 'Unauthorized - Invalid or missing API key'
            ], 401);
        }
        
        try {
            // Get JSON input
            $json_input = file_get_contents('php://input');
            $data = json_decode($json_input, true);
            
            // Also check POST data as fallback
            if (empty($data)) {
                $data = $_POST;
            }
            
            if (!isset($data['ids']) || !is_array($data['ids'])) {
                $this->json_response([
                    'success' => false,
                    'error' => 'Bad Request',
                    'message' => 'Parameter "ids" is required and must be an array'
                ], 400);
            }
            
            $ids = array_map('intval', $data['ids']);
            
            if (empty($ids)) {
                $this->json_response([
                    'success' => false,
                    'error' => 'Bad Request',
                    'message' => 'At least one inquiry ID is required'
                ], 400);
            }
            
            $inquiry_model = new Inquiry_model();
            $result = $inquiry_model->mark_as_exported($ids);
            
            if ($result) {
                $this->json_response([
                    'success' => true,
                    'message' => 'Inquiries marked as exported successfully',
                    'marked_count' => count($ids),
                    'ids' => $ids
                ], 200);
            } else {
                $this->json_response([
                    'success' => false,
                    'error' => 'Failed to mark inquiries as exported'
                ], 500);
            }
            
        } catch (Exception $e) {
            $this->json_response([
                'success' => false,
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to reset export flag
     * 
     * POST /api/reset_export
     * 
     * Body Parameters (JSON or form-data):
     * - ids: Array of inquiry IDs to reset (optional, resets all if empty)
     * 
     * Example JSON body:
     * {
     *   "ids": [1, 2, 3]
     * }
     */
    public function reset_export() {
        // Verify API key
        if (!$this->verify_api_key()) {
            $this->json_response([
                'success' => false,
                'error' => 'Unauthorized - Invalid or missing API key'
            ], 401);
        }
        
        try {
            // Get JSON input
            $json_input = file_get_contents('php://input');
            $data = json_decode($json_input, true);
            
            // Also check POST data as fallback
            if (empty($data)) {
                $data = $_POST;
            }
            
            $ids = [];
            if (isset($data['ids']) && is_array($data['ids'])) {
                $ids = array_map('intval', $data['ids']);
            }
            
            $inquiry_model = new Inquiry_model();
            $result = $inquiry_model->reset_export_flag($ids);
            
            if ($result) {
                $this->json_response([
                    'success' => true,
                    'message' => empty($ids) ? 'All inquiries export flags reset' : 'Export flags reset successfully',
                    'reset_count' => empty($ids) ? 'all' : count($ids),
                    'ids' => $ids
                ], 200);
            } else {
                $this->json_response([
                    'success' => false,
                    'error' => 'Failed to reset export flags'
                ], 500);
            }
            
        } catch (Exception $e) {
            $this->json_response([
                'success' => false,
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API documentation endpoint
     * 
     * GET /api
     */
    public function index() {
        $this->json_response([
            'name' => 'Loan System API',
            'version' => '1.0.0',
            'endpoints' => [
                [
                    'method' => 'GET',
                    'path' => '/api/inquiries',
                    'description' => 'Get all inquiries with optional filters',
                    'parameters' => [
                        'api_key' => 'API key (required if not in Authorization header)',
                        'status' => 'Filter by status (pending, contacted, approved, rejected)',
                        'loan_type' => 'Filter by loan type (personal, business, home, car)',
                        'is_exported' => 'Filter by export status (0 or 1)',
                        'only_unexported' => 'Return only unexported inquiries (1 = yes)',
                        'limit' => 'Limit number of results',
                        'offset' => 'Offset for pagination',
                        'auto_mark' => 'Automatically mark as exported (1 = yes)'
                    ]
                ],
                [
                    'method' => 'POST',
                    'path' => '/api/mark_exported',
                    'description' => 'Mark specific inquiries as exported',
                    'body' => [
                        'ids' => 'Array of inquiry IDs (required)'
                    ]
                ],
                [
                    'method' => 'POST',
                    'path' => '/api/reset_export',
                    'description' => 'Reset export flag for specific inquiries or all',
                    'body' => [
                        'ids' => 'Array of inquiry IDs (optional, resets all if empty)'
                    ]
                ]
            ],
            'authentication' => [
                'type' => 'API Key',
                'methods' => [
                    'Authorization header: Bearer YOUR_API_KEY',
                    'Query parameter: ?api_key=YOUR_API_KEY'
                ]
            ]
        ], 200);
    }
}
