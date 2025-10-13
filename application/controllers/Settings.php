<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {

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
     * Settings management page
     */
    public function index() {
        $data['page_title'] = 'Site Settings Management';
        
        try {
            $settings_model = new Settings_model();
            $loan_type_model = new Loan_type_model();
            
            // Get all settings
            $data['settings'] = $settings_model->get_categorized_settings();
            $data['all_settings'] = $settings_model->get_all_settings();
            
            // Get loan types
            $data['loan_types'] = $loan_type_model->get_all();
            
        } catch (Exception $e) {
            $data['error'] = $e->getMessage();
        }
        
        $this->load->view('admin/settings', $data);
    }
    
    /**
     * Update site settings
     */
    public function update() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        try {
            $settings_model = new Settings_model();
            $updated = 0;
            
            // Update each posted setting
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'setting_') === 0) {
                    $setting_key = str_replace('setting_', '', $key);
                    if ($settings_model->update_setting($setting_key, $value)) {
                        $updated++;
                    }
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => "Successfully updated {$updated} settings",
                'updated_count' => $updated
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Update loan type
     */
    public function update_loan_type($id) {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        try {
            $loan_type_model = new Loan_type_model();
            
            $data = [
                'display_name' => $_POST['display_name'],
                'min_amount' => $_POST['min_amount'],
                'max_amount' => $_POST['max_amount'],
                'min_interest_rate' => $_POST['min_interest_rate'],
                'max_tenure_years' => $_POST['max_tenure_years'],
                'description' => $_POST['description'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            if ($loan_type_model->update($id, $data)) {
                echo json_encode(['success' => true, 'message' => 'Loan type updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update loan type']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
