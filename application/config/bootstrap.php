<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Simple Bootstrap for CodeIgniter-like structure
 */

// Load configuration
require_once APPPATH.'config/config.php';
require_once APPPATH.'config/routes.php';

// Simple autoloader for controllers
spl_autoload_register(function ($class) {
    $file = APPPATH.'controllers/'.$class.'.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Base Controller
class CI_Controller {
    public $load;
    public $input;
    public $form_validation;
    
    public function __construct() {
        // Initialize core components
        $this->load = new CI_Loader();
        $this->input = new CI_Input();
        $this->form_validation = new CI_Form_validation();
        
        // Store instance for get_instance()
        self::$instance =& $this;
    }
    
    private static $instance;
    
    public static function &get_instance() {
        return self::$instance;
    }
}

// Create a simple load property
class CI_Loader {
    private $ci;
    
    public function __construct() {
        // No need to get instance here, will be set by controller
    }
    
    public function helper($helper) {
        // Helpers are already loaded via functions
        return $this;
    }
    
    public function library($library) {
        // Libraries are already loaded
        return $this;
    }
    
    public function view($view, $data = array()) {
        extract($data);
        $view_file = VIEWPATH.str_replace('.', '/', $view).'.php';
        if (file_exists($view_file)) {
            include $view_file;
        }
    }
    
    public function model($model) {
        // Model loading placeholder
        return $this;
    }
}

// Helper functions
function base_url($uri = '') {
    global $config;
    $base = rtrim($config['base_url'], '/');
    if (empty($uri)) {
        return $base . '/';
    }
    return $base . '/' . ltrim($uri, '/');
}

function &get_instance() {
    return CI_Controller::get_instance();
}

// Simple Input class
class CI_Input {
    public function post($key = NULL, $xss_clean = FALSE) {
        if ($key === NULL) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : NULL;
    }
    
    public function get($key = NULL, $xss_clean = FALSE) {
        if ($key === NULL) {
            return $_GET;
        }
        return isset($_GET[$key]) ? $_GET[$key] : NULL;
    }
}

// Simple Form Validation class
class CI_Form_validation {
    private $rules = array();
    private $errors = array();
    private $ci;
    
    public function __construct() {
        // No need to get instance here
    }
    
    public function set_rules($field, $label, $rules) {
        $this->rules[$field] = array(
            'label' => $label,
            'rules' => $rules
        );
        return $this;
    }
    
    public function run() {
        $this->errors = array(); // Reset errors
        
        foreach ($this->rules as $field => $rule) {
            $value = isset($_POST[$field]) ? trim($_POST[$field]) : '';
            $rules = explode('|', $rule['rules']);
            
            foreach ($rules as $r) {
                $r = trim($r);
                if ($r === 'required' && empty($value)) {
                    $this->errors[] = $rule['label'].' is required.';
                } elseif ($r === 'valid_email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[] = $rule['label'].' must be a valid email.';
                } elseif ($r === 'numeric' && !empty($value) && !is_numeric($value)) {
                    $this->errors[] = $rule['label'].' must be numeric.';
                }
            }
        }
        
        return empty($this->errors);
    }
    
    public function error_array() {
        return $this->errors;
    }
}

function validation_errors($prefix = '<p>', $suffix = '</p>') {
    $CI =& get_instance();
    if (isset($CI->form_validation)) {
        $errors = $CI->form_validation->error_array();
        if (!empty($errors)) {
            return $prefix . implode($suffix . $prefix, $errors) . $suffix;
        }
    }
    return '';
}

// Route handling
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];
$base_path = str_replace(basename($script_name), '', $script_name);
$uri = str_replace($base_path, '', $request_uri);
$uri = trim(parse_url($uri, PHP_URL_PATH), '/');
$uri = str_replace($config['index_page'].'/', '', $uri);

// Match route
$controller = $route['default_controller'];
$method = 'index';

if (!empty($uri)) {
    foreach ($route as $pattern => $target) {
        if ($pattern !== 'default_controller' && $pattern !== '404_override' && $pattern !== 'translate_uri_dashes') {
            if ($uri === $pattern) {
                list($controller, $method) = explode('/', $target);
                break;
            }
        }
    }
    
    if ($controller === $route['default_controller'] && !empty($uri)) {
        $segments = explode('/', $uri);
        $controller = ucfirst($segments[0]);
        $method = isset($segments[1]) ? $segments[1] : 'index';
    }
}

// Load and execute controller
$controller = ucfirst($controller);
if (class_exists($controller)) {
    $instance = new $controller();
    
    if (method_exists($instance, $method)) {
        $instance->$method();
    }
}
