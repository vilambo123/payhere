<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
    }

    /**
     * Landing page
     */
    public function index() {
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
        
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
        $this->form_validation->set_rules('phone', 'Phone', 'required|trim');
        $this->form_validation->set_rules('loan_amount', 'Loan Amount', 'required|numeric');
        $this->form_validation->set_rules('loan_type', 'Loan Type', 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'message' => strip_tags(validation_errors())
            ]);
        } else {
            // Prepare data for database
            $data = [
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'loan_amount' => $this->input->post('loan_amount'),
                'loan_type' => $this->input->post('loan_type'),
                'monthly_income' => $this->input->post('income') ? $this->input->post('income') : null,
                'message' => $this->input->post('message'),
                'status' => 'pending'
            ];

            try {
                // Save to database
                $inquiry_model = new Inquiry_model();
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
                        'message' => 'Failed to save your inquiry. Please try again.'
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Database error: ' . $e->getMessage()
                ]);
            }
        }
    }
}
