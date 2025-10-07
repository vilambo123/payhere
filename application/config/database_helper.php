<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Simple Database Helper Class
 * Provides basic database operations using mysqli
 */
class Database {
    
    private static $instance = null;
    private $connection;
    private $config;
    
    private function __construct() {
        // Load database config
        require APPPATH . 'config/database.php';
        $this->config = $db['default'];
        
        // Connect to database
        $this->connect();
    }
    
    /**
     * Get database instance (Singleton pattern)
     */
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    /**
     * Connect to database
     */
    private function connect() {
        $this->connection = new mysqli(
            $this->config['hostname'],
            $this->config['username'],
            $this->config['password'],
            $this->config['database'],
            $this->config['port']
        );
        
        if ($this->connection->connect_error) {
            die('Database connection failed: ' . $this->connection->connect_error);
        }
        
        // Set charset
        $this->connection->set_charset($this->config['char_set']);
    }
    
    /**
     * Get mysqli connection
     */
    public function get_connection() {
        return $this->connection;
    }
    
    /**
     * Escape string
     */
    public function escape($value) {
        if ($value === null) {
            return null;
        }
        return $this->connection->real_escape_string($value);
    }
    
    /**
     * Execute query
     */
    public function query($sql) {
        $result = $this->connection->query($sql);
        
        if (!$result && $this->config['db_debug']) {
            die('Query failed: ' . $this->connection->error . '<br>SQL: ' . $sql);
        }
        
        return $result;
    }
    
    /**
     * Insert data
     */
    public function insert($table, $data) {
        $fields = array_keys($data);
        $values = array_values($data);
        
        // Escape values
        foreach ($values as $key => $value) {
            if ($value === null) {
                $values[$key] = "NULL";
            } else {
                $values[$key] = "'" . $this->escape($value) . "'";
            }
        }
        
        $sql = "INSERT INTO `{$table}` (`" . implode('`, `', $fields) . "`) 
                VALUES (" . implode(', ', $values) . ")";
        
        if ($this->query($sql)) {
            return $this->connection->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update data
     */
    public function update($table, $data, $where) {
        $set = [];
        foreach ($data as $field => $value) {
            if ($value === null) {
                $set[] = "`{$field}` = NULL";
            } else {
                $set[] = "`{$field}` = '" . $this->escape($value) . "'";
            }
        }
        
        $where_clause = $this->build_where($where);
        
        $sql = "UPDATE `{$table}` SET " . implode(', ', $set) . " WHERE {$where_clause}";
        
        return $this->query($sql);
    }
    
    /**
     * Delete data
     */
    public function delete($table, $where) {
        $where_clause = $this->build_where($where);
        $sql = "DELETE FROM `{$table}` WHERE {$where_clause}";
        return $this->query($sql);
    }
    
    /**
     * Get single row
     */
    public function get_where($table, $where) {
        $where_clause = $this->build_where($where);
        $sql = "SELECT * FROM `{$table}` WHERE {$where_clause} LIMIT 1";
        $result = $this->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Get all rows
     */
    public function get_all($table, $where = [], $limit = null, $offset = 0, $order_by = null) {
        $sql = "SELECT * FROM `{$table}`";
        
        if (!empty($where)) {
            $sql .= " WHERE " . $this->build_where($where);
        }
        
        if ($order_by) {
            $sql .= " ORDER BY {$order_by}";
        }
        
        if ($limit !== null) {
            $sql .= " LIMIT {$offset}, {$limit}";
        }
        
        $result = $this->query($sql);
        $rows = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        
        return $rows;
    }
    
    /**
     * Count rows
     */
    public function count($table, $where = []) {
        $sql = "SELECT COUNT(*) as total FROM `{$table}`";
        
        if (!empty($where)) {
            $sql .= " WHERE " . $this->build_where($where);
        }
        
        $result = $this->query($sql);
        
        if ($result) {
            $row = $result->fetch_assoc();
            return (int)$row['total'];
        }
        
        return 0;
    }
    
    /**
     * Build WHERE clause
     */
    private function build_where($where) {
        $conditions = [];
        
        foreach ($where as $field => $value) {
            $conditions[] = "`{$field}` = '" . $this->escape($value) . "'";
        }
        
        return implode(' AND ', $conditions);
    }
    
    /**
     * Close connection
     */
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
    
    /**
     * Destructor
     */
    public function __destruct() {
        $this->close();
    }
}
