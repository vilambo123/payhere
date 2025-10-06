<?php
/**
 * Routing Test - Check if routes work
 */

echo "<h1>Routing Test</h1>";

// Define constants
define('BASEPATH', __DIR__ . '/');
define('APPPATH', __DIR__ . '/application/');
define('ENVIRONMENT', 'development');

// Load config and routes
require_once 'application/config/config.php';
require_once 'application/config/routes.php';

echo "<h3>1. Routes Configuration</h3>";
echo "<pre>";
print_r($route);
echo "</pre>";

echo "<h3>2. Current Request</h3>";
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];
echo "<p><strong>REQUEST_URI:</strong> $request_uri</p>";
echo "<p><strong>SCRIPT_NAME:</strong> $script_name</p>";

// Simulate routing
$base_path = str_replace(basename($script_name), '', $script_name);
$uri = str_replace($base_path, '', $request_uri);
$uri = trim(parse_url($uri, PHP_URL_PATH), '/');
$uri = str_replace('index.php/', '', $uri);

echo "<p><strong>Base Path:</strong> $base_path</p>";
echo "<p><strong>Parsed URI:</strong> $uri</p>";

echo "<h3>3. Route Matching Test</h3>";

// Test submit-inquiry route
$test_uri = 'submit-inquiry';
echo "<p>Testing URI: <code>$test_uri</code></p>";

$controller = $route['default_controller'];
$method = 'index';

foreach ($route as $pattern => $target) {
    if ($pattern !== 'default_controller' && $pattern !== '404_override' && $pattern !== 'translate_uri_dashes') {
        echo "<p>Checking pattern: <code>$pattern</code> against <code>$test_uri</code></p>";
        if ($test_uri === $pattern) {
            echo "<p style='color: green;'>✅ MATCH! Target: <code>$target</code></p>";
            list($controller, $method) = explode('/', $target);
            break;
        } else {
            echo "<p style='color: orange;'>No match</p>";
        }
    }
}

echo "<p><strong>Controller:</strong> $controller</p>";
echo "<p><strong>Method:</strong> $method</p>";

echo "<h3>4. Controller File Check</h3>";
$controller_file = 'application/controllers/' . ucfirst($controller) . '.php';
if (file_exists($controller_file)) {
    echo "<p style='color: green;'>✅ Controller file exists: <code>$controller_file</code></p>";
    
    // Load bootstrap FIRST
    require_once 'application/config/bootstrap.php';
    
    // Force load the controller
    require_once $controller_file;
    
    $class_name = ucfirst($controller);
    if (class_exists($class_name)) {
        echo "<p style='color: green;'>✅ Controller class exists: <code>$class_name</code></p>";
        
        try {
            $instance = new $class_name();
            echo "<p style='color: green;'>✅ Controller instantiated successfully</p>";
            
            if (method_exists($instance, $method)) {
                echo "<p style='color: green;'>✅ Method exists: <code>$method()</code></p>";
                
                echo "<h3>5. Testing Method Call</h3>";
                echo "<p>Calling method with test POST data...</p>";
                
                // Set test POST data
                $_POST = [
                    'name' => 'Test User',
                    'email' => 'test@test.com',
                    'phone' => '0123456789',
                    'loan_type' => 'personal',
                    'loan_amount' => '50000'
                ];
                
                echo "<pre>POST data: " . print_r($_POST, true) . "</pre>";
                echo "<hr>";
                echo "<p><strong>Method Output:</strong></p>";
                
                // Call the method
                $instance->$method();
                
                echo "<hr>";
                echo "<p style='color: green;'>✅ Method executed</p>";
                
            } else {
                echo "<p style='color: red;'>❌ Method does NOT exist: <code>$method()</code></p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error instantiating controller: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Controller class NOT found after loading file</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Controller file NOT found: <code>$controller_file</code></p>";
}

echo "<h3>6. Direct URL Test</h3>";
$submit_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php/submit-inquiry';
echo "<p>Try this URL directly:</p>";
echo "<p><a href='$submit_url' target='_blank'>$submit_url</a></p>";
echo "<p>Should show JSON error like: <code>{\"success\":false,\"message\":\"...\"}</code></p>";

?>

<style>
    body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    code { background: #e0e0e0; padding: 2px 6px; border-radius: 3px; }
</style>
