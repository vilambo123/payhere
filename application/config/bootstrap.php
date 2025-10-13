<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Simple Bootstrap for CodeIgniter-like structure
 */

// Load configuration
require_once APPPATH.'config/config.php';
require_once APPPATH.'config/routes.php';
require_once APPPATH.'config/database_helper.php';
require_once APPPATH.'helpers/language_helper.php';

// Start session for language support
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple autoloader for controllers and models
spl_autoload_register(function ($class) {
    // Try controllers first
    $file = APPPATH.'controllers/'.$class.'.php';
    if (file_exists($file)) {
        require_once $file;
        return;
    }
    
    // Try models
    $file = APPPATH.'models/'.$class.'.php';
    if (file_exists($file)) {
        require_once $file;
        return;
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

// Remove index.php/ if present
if (strpos($uri, $config['index_page'].'/') === 0) {
    $uri = substr($uri, strlen($config['index_page']) + 1);
}

// Match route
$controller = $route['default_controller'];
$method = 'index';
$params = [];
$matched = false;

if (!empty($uri)) {
    // Try to match custom routes with parameters
    foreach ($route as $pattern => $target) {
        if ($pattern !== 'default_controller' && $pattern !== '404_override' && $pattern !== 'translate_uri_dashes') {
            // Convert CodeIgniter route pattern to regex
            $regex_pattern = str_replace(['(:num)', ':num'], '([0-9]+)', $pattern);
            $regex_pattern = str_replace(['(:any)', ':any'], '(.+)', $regex_pattern);
            $regex_pattern = '#^' . $regex_pattern . '$#';
            
            if (preg_match($regex_pattern, $uri, $matches)) {
                // Remove the full match
                array_shift($matches);
                
                // Parse target
                if (strpos($target, '$') !== false) {
                    // Replace $1, $2 etc with captured groups
                    for ($i = 0; $i < count($matches); $i++) {
                        $target = str_replace('$' . ($i + 1), $matches[$i], $target);
                    }
                }
                
                $target_parts = explode('/', $target);
                $controller = $target_parts[0];
                $method = isset($target_parts[1]) ? $target_parts[1] : 'index';
                
                // Remaining parts are parameters
                if (count($target_parts) > 2) {
                    $params = array_slice($target_parts, 2);
                }
                
                $matched = true;
                break;
            }
        }
    }
    
    // If no custom route matched, try default controller/method/params
    if (!$matched && $controller === $route['default_controller'] && !empty($uri)) {
        $segments = explode('/', $uri);
        $controller = ucfirst($segments[0]);
        $method = isset($segments[1]) ? $segments[1] : 'index';
        
        // Remaining segments are parameters
        if (count($segments) > 2) {
            $params = array_slice($segments, 2);
        }
    }
}

// Load and execute controller
$controller = ucfirst($controller);

// Force load the controller file if it exists
$controller_file = APPPATH.'controllers/'.$controller.'.php';
if (file_exists($controller_file)) {
    require_once $controller_file;
}

if (class_exists($controller)) {
    $instance = new $controller();
    
    if (method_exists($instance, $method)) {
        // Call method with parameters
        call_user_func_array([$instance, $method], $params);
    } else {
        // Method not found
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['success' => false, 'message' => 'Method not found: ' . $method]);
    }
} else {
    // Controller not found
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['success' => false, 'message' => 'Controller not found: ' . $controller]);
}
