<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Controller Loading Test</h1>";

// Step 1: Define constants
echo "<h3>Step 1: Define Constants</h3>";
define('BASEPATH', __DIR__ . '/');
define('APPPATH', __DIR__ . '/application/');
define('ENVIRONMENT', 'development');
echo "<p>✅ Constants defined</p>";

// Step 2: Try to include the controller file directly
echo "<h3>Step 2: Include Controller File</h3>";
$controller_path = 'application/controllers/Home.php';
echo "<p>Path: <code>$controller_path</code></p>";

if (file_exists($controller_path)) {
    echo "<p>✅ File exists</p>";
    
    // Check if we can read it
    $content = file_get_contents($controller_path);
    echo "<p>✅ File readable, size: " . strlen($content) . " bytes</p>";
    
    // Check for PHP errors by trying to parse
    echo "<h3>Step 3: Load Bootstrap Classes</h3>";
    
    try {
        // Load config first
        require_once 'application/config/config.php';
        echo "<p>✅ Config loaded</p>";
        
        // Load database config
        require_once 'application/config/database.php';
        echo "<p>✅ Database config loaded</p>";
        
        // Load database helper
        require_once 'application/config/database_helper.php';
        echo "<p>✅ Database helper loaded</p>";
        
        // Load routes
        require_once 'application/config/routes.php';
        echo "<p>✅ Routes loaded</p>";
        
        // Now load the base controller from bootstrap
        echo "<h3>Step 4: Load Base Controller</h3>";
        
        // Base Controller class
        class CI_Controller {
            public $load;
            public $input;
            public $form_validation;
            
            public function __construct() {
                $this->load = new CI_Loader();
                $this->input = new CI_Input();
                $this->form_validation = new CI_Form_validation();
                self::$instance =& $this;
            }
            
            private static $instance;
            
            public static function &get_instance() {
                return self::$instance;
            }
        }
        
        class CI_Loader {
            public function helper($helper) { return $this; }
            public function library($library) { return $this; }
            public function view($view, $data = array()) {
                extract($data);
                $view_file = APPPATH.'views/'.str_replace('.', '/', $view).'.php';
                if (file_exists($view_file)) {
                    include $view_file;
                }
            }
            public function model($model) { return $this; }
        }
        
        class CI_Input {
            public function post($key = NULL, $xss_clean = FALSE) {
                if ($key === NULL) return $_POST;
                return isset($_POST[$key]) ? $_POST[$key] : NULL;
            }
        }
        
        class CI_Form_validation {
            private $rules = array();
            private $errors = array();
            
            public function set_rules($field, $label, $rules) {
                $this->rules[$field] = array('label' => $label, 'rules' => $rules);
                return $this;
            }
            
            public function run() {
                $this->errors = array();
                foreach ($this->rules as $field => $rule) {
                    $value = isset($_POST[$field]) ? trim($_POST[$field]) : '';
                    $rules = explode('|', $rule['rules']);
                    foreach ($rules as $r) {
                        $r = trim($r);
                        if ($r === 'required' && empty($value)) {
                            $this->errors[] = $rule['label'].' is required.';
                        }
                    }
                }
                return empty($this->errors);
            }
            
            public function error_array() {
                return $this->errors;
            }
        }
        
        function validation_errors() {
            return '';
        }
        
        function base_url($uri = '') {
            global $config;
            return rtrim($config['base_url'], '/') . '/' . ltrim($uri, '/');
        }
        
        function &get_instance() {
            return CI_Controller::get_instance();
        }
        
        echo "<p>✅ Base controller class defined</p>";
        
        // Load helper
        echo "<h3>Step 5: Load Helpers</h3>";
        require_once 'application/helpers/malaysian_validation_helper.php';
        echo "<p>✅ Malaysian validation helper loaded</p>";
        
        // Load models
        echo "<h3>Step 6: Load Models</h3>";
        require_once 'application/models/Settings_model.php';
        echo "<p>✅ Settings model loaded</p>";
        require_once 'application/models/Loan_type_model.php';
        echo "<p>✅ Loan type model loaded</p>";
        require_once 'application/models/Inquiry_model.php';
        echo "<p>✅ Inquiry model loaded</p>";
        
        // Now load the controller
        echo "<h3>Step 7: Load Home Controller</h3>";
        require_once $controller_path;
        echo "<p>✅ Home controller file included</p>";
        
        // Check if class exists
        if (class_exists('Home')) {
            echo "<p style='color: green; font-size: 1.2em;'>✅✅✅ Home class EXISTS!</p>";
            
            // Try to instantiate
            echo "<h3>Step 8: Instantiate Controller</h3>";
            $home = new Home();
            echo "<p style='color: green;'>✅ Controller instantiated!</p>";
            
            // Check if method exists
            if (method_exists($home, 'submit_inquiry')) {
                echo "<p style='color: green;'>✅ submit_inquiry method exists!</p>";
                
                // Try to call it
                echo "<h3>Step 9: Call submit_inquiry()</h3>";
                $_POST = [
                    'name' => 'Test User',
                    'email' => 'test@test.com',
                    'phone' => '0123456789',
                    'loan_type' => 'personal',
                    'loan_amount' => '50000'
                ];
                echo "<p>Setting POST data and calling method...</p>";
                echo "<hr>";
                
                $home->submit_inquiry();
                
                echo "<hr>";
                echo "<p style='color: green;'>✅ Method completed!</p>";
                
            } else {
                echo "<p style='color: red;'>❌ submit_inquiry method NOT found</p>";
            }
            
        } else {
            echo "<p style='color: red;'>❌ Home class NOT found after including file!</p>";
            echo "<p>Declared classes:</p>";
            echo "<pre>";
            print_r(get_declared_classes());
            echo "</pre>";
        }
        
    } catch (Error $e) {
        echo "<p style='color: red;'>❌ PHP Error: " . $e->getMessage() . "</p>";
        echo "<p>File: " . $e->getFile() . "</p>";
        echo "<p>Line: " . $e->getLine() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
        echo "<p>File: " . $e->getFile() . "</p>";
        echo "<p>Line: " . $e->getLine() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Controller file NOT found!</p>";
}
?>

<style>
    body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
    pre { background: #fff; padding: 10px; border: 1px solid #ddd; overflow-x: auto; max-height: 300px; }
    code { background: #e0e0e0; padding: 2px 6px; border-radius: 3px; }
    hr { margin: 20px 0; border: none; border-top: 2px solid #667eea; }
</style>
