<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Download extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        
        // Load language helper
        require_once APPPATH . 'helpers/language_helper.php';
    }

    /**
     * Download page - Google Play Store style
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
        
        // Get APK download URL
        $data['apk_url'] = $settings_model->get_setting('apk_download_url');
        if (!$data['apk_url']) {
            $data['apk_url'] = 'https://google.com'; // Fallback
        }
        
        // Get loan types
        $data['loan_types'] = $loan_type_model->get_all_active();
        $data['loan_stats'] = $loan_type_model->get_statistics();
        
        // Page meta
        $data['page_title'] = 'Download App - ' . $settings['site']['name'];
        $data['meta_description'] = 'Download our mobile app for easier loan management and quick applications.';
        
        // Language support
        $data['current_lang'] = $current_lang;
        $data['translations_json'] = get_language_json();
        
        // Load standalone download page (no header/footer)
        $this->load->view('download/index', $data);
    }
}
