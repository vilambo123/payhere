<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->check_login();
    }
    
    /**
     * Check if admin is logged in
     */
    private function check_login() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . base_url('index.php/auth/login'));
            exit;
        }
    }

    /**
     * Admin dashboard - View all inquiries
     */
    public function index() {
        $data['page_title'] = 'Admin Dashboard - Loan Inquiries';
        
        try {
            $inquiry_model = new Inquiry_model();
            
            // Get filter parameters
            $status = isset($_GET['status']) ? $_GET['status'] : '';
            $loan_type = isset($_GET['loan_type']) ? $_GET['loan_type'] : '';
            
            $filters = [];
            if (!empty($status)) {
                $filters['status'] = $status;
            }
            if (!empty($loan_type)) {
                $filters['loan_type'] = $loan_type;
            }
            
            // Get inquiries
            $data['inquiries'] = $inquiry_model->get_all($filters);
            $data['statistics'] = $inquiry_model->get_statistics();
            $data['current_status'] = $status;
            $data['current_loan_type'] = $loan_type;
            
        } catch (Exception $e) {
            $data['error'] = $e->getMessage();
            $data['inquiries'] = [];
            $data['statistics'] = [];
        }
        
        $this->load->view('admin/dashboard', $data);
    }
    
    /**
     * Update inquiry status
     */
    public function update_status($id) {
        header('Content-Type: application/json');
        
        if (!isset($_POST['status'])) {
            echo json_encode(['success' => false, 'message' => 'Status is required']);
            return;
        }
        
        try {
            $inquiry_model = new Inquiry_model();
            $result = $inquiry_model->update_status($id, $_POST['status']);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update status']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * View/Edit inquiry details
     */
    public function view($id) {
        $data['page_title'] = 'View Inquiry #' . $id;
        
        try {
            $inquiry_model = new Inquiry_model();
            $data['inquiry'] = $inquiry_model->get_by_id($id);
            
            if (!$data['inquiry']) {
                $data['error'] = 'Inquiry not found';
            }
            
        } catch (Exception $e) {
            $data['error'] = $e->getMessage();
            $data['inquiry'] = null;
        }
        
        $this->load->view('admin/view_inquiry', $data);
    }
    
    /**
     * Update inquiry data
     */
    public function update($id) {
        header('Content-Type: application/json');
        
        if (empty($_POST)) {
            echo json_encode(['success' => false, 'message' => 'No data provided']);
            return;
        }
        
        try {
            $inquiry_model = new Inquiry_model();
            
            // Prepare update data
            $data = [];
            $allowed_fields = ['name', 'email', 'phone', 'ic_number', 'loan_type', 'loan_amount', 'monthly_income', 'message', 'status'];
            
            foreach ($allowed_fields as $field) {
                if (isset($_POST[$field])) {
                    $data[$field] = $_POST[$field];
                }
            }
            
            if (empty($data)) {
                echo json_encode(['success' => false, 'message' => 'No valid data to update']);
                return;
            }
            
            $result = $inquiry_model->update($id, $data);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Inquiry updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update inquiry']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Delete inquiry
     */
    public function delete($id) {
        header('Content-Type: application/json');
        
        try {
            $inquiry_model = new Inquiry_model();
            $result = $inquiry_model->delete($id);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Inquiry deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete inquiry']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
