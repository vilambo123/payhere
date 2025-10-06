<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
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
