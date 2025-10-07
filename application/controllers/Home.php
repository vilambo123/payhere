<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        
        // Load Malaysian validation helper
        require_once APPPATH . 'helpers/malaysian_validation_helper.php';
        
        // Load language helper
        require_once APPPATH . 'helpers/language_helper.php';
    }

    /**
     * Landing page
     */
    public function index() {
        // Get current language
        $current_lang = get_current_language();
        
        // Load settings and loan types from database
        $settings_model = new Settings_model();
        $loan_type_model = new Loan_type_model();
        
        // Get all settings
        $settings = $settings_model->get_categorized_settings();
        $data['settings'] = $settings;
        $data['site'] = $settings['site'];
        $data['social'] = $settings['social'];
        
        // Get loan types
        $data['loan_types'] = $loan_type_model->get_all_active();
        $data['loan_stats'] = $loan_type_model->get_statistics();
        
        // Page meta
        $data['page_title'] = $settings['site']['name'] . ' - Financial Loan Solutions | Quick & Easy Loan Approval';
        $data['meta_description'] = 'Get instant loan approval with competitive rates. Personal loans, business loans, and more. Apply online in minutes.';
        
        // Language support
        $data['current_lang'] = $current_lang;
        $data['translations_json'] = get_language_json();
        
        $this->load->view('layouts/header', $data);
        $this->load->view('home/index', $data);
        $this->load->view('layouts/footer', $data);
    }

    /**
     * Handle contact/inquiry form submission
     */
    public function submit_inquiry() {
        // Set JSON header
        header('Content-Type: application/json');
        
        // Check if POST data exists
        if (empty($_POST)) {
            echo json_encode([
                'success' => false,
                'message' => 'No POST data received. Method: ' . $_SERVER['REQUEST_METHOD']
            ]);
            exit;
        }
        
        try {
            $this->load->helper('form');
            $this->load->library('form_validation');

            $this->form_validation->set_rules('name', 'Name', 'required|trim|min_length[3]|max_length[100]');
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
            $this->form_validation->set_rules('phone', 'Phone', 'required|trim');
            $this->form_validation->set_rules('ic_number', 'IC Number', 'required|trim');
            $this->form_validation->set_rules('loan_amount', 'Loan Amount', 'required|numeric|greater_than[999]');
            $this->form_validation->set_rules('loan_type', 'Loan Type', 'required');

            if ($this->form_validation->run() == FALSE) {
                $errors = validation_errors();
                error_log('Validation errors: ' . $errors);
                echo json_encode([
                    'success' => false,
                    'message' => strip_tags($errors)
                ]);
                return;
            }
            
            // Additional Malaysian-specific validations
            $phone = $this->input->post('phone');
            if (!validate_malaysian_phone($phone)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please provide a valid Malaysian phone number (e.g., 012-345-6789 or +6012-345-6789)'
                ]);
                return;
            }
            
            // Validate IC number
            $ic_number = $this->input->post('ic_number');
            if (!validate_malaysian_ic($ic_number)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please provide a valid Malaysian IC number (e.g., 901231-01-5678)'
                ]);
                return;
            }
            
            // Validate name format
            $name = $this->input->post('name');
            if (!validate_malaysian_name($name)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Please enter a valid name (letters, spaces, and common Malaysian name characters only)'
                ]);
                return;
            }
            
            // Validate loan amount range for loan type
            $loan_amount = $this->input->post('loan_amount');
            $loan_type = $this->input->post('loan_type');
            $amount_validation = validate_loan_amount_range($loan_amount, $loan_type);
            
            if (!$amount_validation['valid']) {
                echo json_encode([
                    'success' => false,
                    'message' => $amount_validation['message']
                ]);
                return;
            }
            
            // Check for existing pending application
            $email = $this->input->post('email');
            $inquiry_model = new Inquiry_model();
            $pending = $inquiry_model->check_pending_application($email, $ic_number);
            
            if ($pending) {
                $submission_date = date('d M Y', strtotime($pending['created_at']));
                echo json_encode([
                    'success' => false,
                    'message' => "We have received your application submitted on {$submission_date}. Our team is currently reviewing your request and will contact you within 2-3 business days. We appreciate your patience. If you have any urgent queries, please call us at " . (isset($GLOBALS['site_phone']) ? $GLOBALS['site_phone'] : '+60 3-1234 5678') . ".",
                    'pending_application_id' => $pending['id'],
                    'submission_date' => $submission_date
                ]);
                return;
            }
            
            // Prepare data for database (with formatted phone and IC)
            $data = [
                'name' => ucwords(strtolower($this->input->post('name'))), // Proper case
                'email' => strtolower($this->input->post('email')), // Lowercase email
                'phone' => format_malaysian_phone($this->input->post('phone')), // Format phone
                'ic_number' => format_malaysian_ic($ic_number), // Format IC
                'loan_amount' => $this->input->post('loan_amount'),
                'loan_type' => $this->input->post('loan_type'),
                'monthly_income' => $this->input->post('income') ? $this->input->post('income') : null,
                'message' => $this->input->post('message'),
                'status' => 'pending'
            ];

            // Check if database helper is loaded
            if (!class_exists('Database')) {
                throw new Exception('Database helper not loaded');
            }

            // Save to database
            $insert_id = $inquiry_model->save($data);
            
            if ($insert_id) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Thank you! We have received your application and will contact you shortly.',
                    'inquiry_id' => $insert_id
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to save your inquiry. Please check database connection.'
                ]);
            }
        } catch (Exception $e) {
            // Log error for debugging
            error_log('Submit inquiry error: ' . $e->getMessage());
            
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        
        // Make sure script stops here
        exit;
    }
}
