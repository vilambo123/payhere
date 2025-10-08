<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
    }

    /**
     * Show login page
     */
    public function login() {
        // If already logged in, redirect to admin
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            header('Location: ' . base_url('index.php/admin'));
            exit;
        }
        
        $data['page_title'] = 'Admin Login';
        $data['error'] = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
        unset($_SESSION['login_error']);
        
        $this->load->view('auth/login', $data);
    }

    /**
     * Process login
     */
    public function do_login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . base_url('index.php/auth/login'));
            exit;
        }
        
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        
        // Simple authentication (you can enhance this with database)
        // Default credentials: admin / admin123
        $valid_username = 'admin';
        $valid_password = 'admin123'; // In production, use password_hash() and password_verify()
        
        if ($username === $valid_username && $password === $valid_password) {
            // Login successful
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_login_time'] = time();
            
            header('Location: ' . base_url('index.php/admin'));
            exit;
        } else {
            // Login failed
            $_SESSION['login_error'] = 'Invalid username or password';
            header('Location: ' . base_url('index.php/auth/login'));
            exit;
        }
    }

    /**
     * Logout
     */
    public function logout() {
        // Clear session
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_username']);
        unset($_SESSION['admin_login_time']);
        
        $_SESSION['login_success'] = 'You have been logged out successfully';
        
        header('Location: ' . base_url('index.php/auth/login'));
        exit;
    }
}
