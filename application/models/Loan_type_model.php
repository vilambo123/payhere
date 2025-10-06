<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loan_type_model {
    
    private $db;
    private $table = 'loan_types';
    
    public function __construct() {
        $this->db = Database::get_instance();
    }
    
    /**
     * Get all active loan types
     * 
     * @return array Loan types
     */
    public function get_all_active() {
        return $this->db->get_all($this->table, ['is_active' => 1], null, 0, 'id ASC');
    }
    
    /**
     * Get loan type by type name
     * 
     * @param string $type_name Type name (personal, business, home, car)
     * @return array|null Loan type data
     */
    public function get_by_type($type_name) {
        return $this->db->get_where($this->table, ['type_name' => $type_name, 'is_active' => 1]);
    }
    
    /**
     * Get loan type by ID
     * 
     * @param int $id Loan type ID
     * @return array|null Loan type data
     */
    public function get_by_id($id) {
        return $this->db->get_where($this->table, ['id' => $id]);
    }
    
    /**
     * Get all loan types (including inactive)
     * 
     * @return array All loan types
     */
    public function get_all() {
        return $this->db->get_all($this->table, [], null, 0, 'id ASC');
    }
    
    /**
     * Update loan type
     * 
     * @param int $id Loan type ID
     * @param array $data Update data
     * @return bool Success status
     */
    public function update($id, $data) {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }
    
    /**
     * Get loan types formatted for dropdown
     * 
     * @return array Formatted for select options
     */
    public function get_for_dropdown() {
        $types = $this->get_all_active();
        $dropdown = [];
        
        foreach ($types as $type) {
            $dropdown[$type['type_name']] = $type['display_name'];
        }
        
        return $dropdown;
    }
    
    /**
     * Get loan type statistics
     * 
     * @return array Statistics
     */
    public function get_statistics() {
        $types = $this->get_all_active();
        $stats = [];
        
        foreach ($types as $type) {
            $stats[$type['type_name']] = [
                'name' => $type['display_name'],
                'min_amount' => $type['min_amount'],
                'max_amount' => $type['max_amount'],
                'interest_rate' => $type['min_interest_rate'],
                'max_tenure' => $type['max_tenure_years'],
            ];
        }
        
        return $stats;
    }
}
