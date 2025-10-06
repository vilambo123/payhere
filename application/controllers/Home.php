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
        $data['page_title'] = 'Financial Loan Solutions | Quick & Easy Loan Approval';
        $data['meta_description'] = 'Get instant loan approval with competitive rates. Personal loans, business loans, and more. Apply online in minutes.';
        
        $this->load->view('layouts/header', $data);
        $this->load->view('home/index', $data);
        $this->load->view('layouts/footer');
    }

    /**
     * Handle contact/inquiry form submission
     */
    public function submit_inquiry() {
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
                'message' => validation_errors()
            ]);
        } else {
            // Here you would typically save to database or send email
            // For now, we'll just return success
            $data = [
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'loan_amount' => $this->input->post('loan_amount'),
                'loan_type' => $this->input->post('loan_type'),
                'message' => $this->input->post('message'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            // You can save to database here
            // $this->load->model('Inquiry_model');
            // $this->Inquiry_model->save($data);

            echo json_encode([
                'success' => true,
                'message' => 'Thank you! We will contact you shortly.'
            ]);
        }
    }
}
