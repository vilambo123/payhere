<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inquiry_model {
    
    private $db;
    private $table = 'inquiries';
    
    public function __construct() {
        $this->db = Database::get_instance();
    }
    
    /**
     * Save new inquiry to database
     * 
     * @param array $data Inquiry data
     * @return int|bool Insert ID on success, false on failure
     */
    public function save($data) {
        // Add metadata
        $data['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $data['user_agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Get inquiry by ID
     * 
     * @param int $id Inquiry ID
     * @return array|null Inquiry data or null
     */
    public function get_by_id($id) {
        return $this->db->get_where($this->table, ['id' => $id]);
    }
    
    /**
     * Get all inquiries with optional filters
     * 
     * @param array $filters Optional filters (status, loan_type, limit, offset)
     * @return array List of inquiries
     */
    public function get_all($filters = []) {
        $where = [];
        $limit = isset($filters['limit']) ? $filters['limit'] : null;
        $offset = isset($filters['offset']) ? $filters['offset'] : 0;
        
        if (isset($filters['status'])) {
            $where['status'] = $filters['status'];
        }
        
        if (isset($filters['loan_type'])) {
            $where['loan_type'] = $filters['loan_type'];
        }
        
        return $this->db->get_all($this->table, $where, $limit, $offset, 'created_at DESC');
    }
    
    /**
     * Update inquiry status
     * 
     * @param int $id Inquiry ID
     * @param string $status New status
     * @return bool Success status
     */
    public function update_status($id, $status) {
        return $this->db->update($this->table, ['status' => $status], ['id' => $id]);
    }
    
    /**
     * Count total inquiries
     * 
     * @param array $filters Optional filters
     * @return int Total count
     */
    public function count_all($filters = []) {
        $where = [];
        
        if (isset($filters['status'])) {
            $where['status'] = $filters['status'];
        }
        
        if (isset($filters['loan_type'])) {
            $where['loan_type'] = $filters['loan_type'];
        }
        
        return $this->db->count($this->table, $where);
    }
    
    /**
     * Get inquiries by email
     * 
     * @param string $email Email address
     * @return array List of inquiries
     */
    public function get_by_email($email) {
        return $this->db->get_all($this->table, ['email' => $email], null, 0, 'created_at DESC');
    }
    
    /**
     * Delete inquiry
     * 
     * @param int $id Inquiry ID
     * @return bool Success status
     */
    public function delete($id) {
        return $this->db->delete($this->table, ['id' => $id]);
    }
    
    /**
     * Get statistics
     * 
     * @return array Statistics data
     */
    public function get_statistics() {
        $stats = [];
        
        // Total inquiries
        $stats['total'] = $this->count_all();
        
        // By status
        $stats['pending'] = $this->count_all(['status' => 'pending']);
        $stats['contacted'] = $this->count_all(['status' => 'contacted']);
        $stats['approved'] = $this->count_all(['status' => 'approved']);
        $stats['rejected'] = $this->count_all(['status' => 'rejected']);
        
        // By loan type
        $stats['personal'] = $this->count_all(['loan_type' => 'personal']);
        $stats['business'] = $this->count_all(['loan_type' => 'business']);
        $stats['home'] = $this->count_all(['loan_type' => 'home']);
        $stats['car'] = $this->count_all(['loan_type' => 'car']);
        
        return $stats;
    }
}
