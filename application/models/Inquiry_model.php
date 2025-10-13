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
     * @param array $filters Optional filters (status, loan_type, limit, offset, is_exported)
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
        
        if (isset($filters['is_exported'])) {
            $where['is_exported'] = $filters['is_exported'];
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
     * Update inquiry data
     * 
     * @param int $id Inquiry ID
     * @param array $data Data to update
     * @return bool Success status
     */
    public function update($id, $data) {
        return $this->db->update($this->table, $data, ['id' => $id]);
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
    
    /**
     * Check if user has pending application
     * 
     * @param string $email Email address
     * @param string $ic_number IC number (optional)
     * @param string $phone Phone number (optional)
     * @return array|null Pending application or null
     */
    public function check_pending_application($email, $ic_number = null, $phone = null) {
        $conditions = [];
        
        if (!empty($email)) {
            $conditions[] = "`email` = '" . $this->db->escape($email) . "'";
        }
        
        if (!empty($ic_number)) {
            $conditions[] = "`ic_number` = '" . $this->db->escape($ic_number) . "'";
        }
        
        if (!empty($phone)) {
            $conditions[] = "`phone` = '" . $this->db->escape($phone) . "'";
        }
        
        if (empty($conditions)) {
            return null;
        }
        
        $sql = "SELECT * FROM `{$this->table}` 
                WHERE `status` = 'pending' 
                AND (" . implode(' OR ', $conditions) . ") 
                ORDER BY `created_at` DESC LIMIT 1";
        
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Get application by IC number
     * 
     * @param string $ic_number IC number
     * @return array List of applications
     */
    public function get_by_ic($ic_number) {
        return $this->db->get_all($this->table, ['ic_number' => $ic_number], null, 0, 'created_at DESC');
    }
    
    /**
     * Mark inquiries as exported
     * 
     * @param array $ids Array of inquiry IDs
     * @return bool Success status
     */
    public function mark_as_exported($ids) {
        if (empty($ids) || !is_array($ids)) {
            return false;
        }
        
        $ids_string = implode(',', array_map('intval', $ids));
        $sql = "UPDATE `{$this->table}` 
                SET `is_exported` = 1, `exported_at` = NOW() 
                WHERE `id` IN ($ids_string)";
        
        $result = $this->db->query($sql);
        return $result !== false;
    }
    
    /**
     * Get unexported inquiries
     * 
     * @param array $filters Optional filters (status, loan_type, limit, offset)
     * @return array List of unexported inquiries
     */
    public function get_unexported($filters = []) {
        $filters['is_exported'] = 0;
        return $this->get_all($filters);
    }
    
    /**
     * Reset export flag for testing/debugging
     * 
     * @param array $ids Array of inquiry IDs (optional, resets all if empty)
     * @return bool Success status
     */
    public function reset_export_flag($ids = []) {
        if (empty($ids)) {
            // Reset all
            $sql = "UPDATE `{$this->table}` 
                    SET `is_exported` = 0, `exported_at` = NULL";
        } else {
            $ids_string = implode(',', array_map('intval', $ids));
            $sql = "UPDATE `{$this->table}` 
                    SET `is_exported` = 0, `exported_at` = NULL 
                    WHERE `id` IN ($ids_string)";
        }
        
        $result = $this->db->query($sql);
        return $result !== false;
    }
}
