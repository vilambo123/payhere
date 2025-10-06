<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model {
    
    private $db;
    private $settings_table = 'site_settings';
    
    public function __construct() {
        $this->db = Database::get_instance();
    }
    
    /**
     * Get all settings as key-value array
     * 
     * @return array Settings array
     */
    public function get_all_settings() {
        $settings = [];
        $results = $this->db->get_all($this->settings_table);
        
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        return $settings;
    }
    
    /**
     * Get single setting by key
     * 
     * @param string $key Setting key
     * @param mixed $default Default value if not found
     * @return mixed Setting value
     */
    public function get_setting($key, $default = null) {
        $result = $this->db->get_where($this->settings_table, ['setting_key' => $key]);
        
        if ($result) {
            return $result['setting_value'];
        }
        
        return $default;
    }
    
    /**
     * Update or insert setting
     * 
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @return bool Success status
     */
    public function update_setting($key, $value) {
        $existing = $this->db->get_where($this->settings_table, ['setting_key' => $key]);
        
        if ($existing) {
            return $this->db->update($this->settings_table, ['setting_value' => $value], ['setting_key' => $key]);
        } else {
            return $this->db->insert($this->settings_table, [
                'setting_key' => $key,
                'setting_value' => $value
            ]);
        }
    }
    
    /**
     * Get settings grouped by category
     * 
     * @return array Categorized settings
     */
    public function get_categorized_settings() {
        $all_settings = $this->get_all_settings();
        
        return [
            'site' => [
                'name' => isset($all_settings['site_name']) ? $all_settings['site_name'] : 'QuickLoan',
                'email' => isset($all_settings['site_email']) ? $all_settings['site_email'] : 'info@quickloan.com',
                'phone' => isset($all_settings['site_phone']) ? $all_settings['site_phone'] : '+60 3-1234 5678',
                'address' => isset($all_settings['site_address']) ? $all_settings['site_address'] : 'Kuala Lumpur, Malaysia',
            ],
            'social' => [
                'facebook' => isset($all_settings['social_facebook']) ? $all_settings['social_facebook'] : '#',
                'twitter' => isset($all_settings['social_twitter']) ? $all_settings['social_twitter'] : '#',
                'instagram' => isset($all_settings['social_instagram']) ? $all_settings['social_instagram'] : '#',
                'linkedin' => isset($all_settings['social_linkedin']) ? $all_settings['social_linkedin'] : '#',
            ],
            'features' => [
                'enable_notifications' => isset($all_settings['enable_notifications']) ? $all_settings['enable_notifications'] : '1',
                'maintenance_mode' => isset($all_settings['maintenance_mode']) ? $all_settings['maintenance_mode'] : '0',
            ]
        ];
    }
}
