<?php
/**
 * Simple Endpoint Test
 * Tests if the submit-inquiry endpoint is accessible
 */

echo "<h1>Endpoint Test</h1>";

// Test 1: Can we access the endpoint URL?
$baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/';
$endpoint = $baseUrl . 'index.php/submit-inquiry';

echo "<h3>1. Endpoint URL</h3>";
echo "<p><code>$endpoint</code></p>";

// Test 2: Make a simple GET request
echo "<h3>2. GET Request Test</h3>";
$response = @file_get_contents($endpoint);
if ($response === false) {
    echo "<p style='color: red;'>❌ Cannot reach endpoint</p>";
} else {
    echo "<p style='color: green;'>✅ Endpoint is reachable</p>";
    echo "<p><strong>Response:</strong></p>";
    echo "<pre style='background: #f5f5f5; padding: 10px;'>" . htmlspecialchars($response) . "</pre>";
}

// Test 3: Check if index.php exists
echo "<h3>3. File Check</h3>";
if (file_exists('index.php')) {
    echo "<p style='color: green;'>✅ index.php exists</p>";
} else {
    echo "<p style='color: red;'>❌ index.php NOT FOUND</p>";
}

// Test 4: Check if .htaccess exists
if (file_exists('.htaccess')) {
    echo "<p style='color: green;'>✅ .htaccess exists</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px;'>";
    echo htmlspecialchars(file_get_contents('.htaccess'));
    echo "</pre>";
} else {
    echo "<p style='color: orange;'>⚠️ .htaccess NOT FOUND (not required for XAMPP)</p>";
}

// Test 5: Direct PHP test
echo "<h3>4. Direct Controller Test</h3>";
echo "<p>Testing if we can load the controller directly...</p>";

try {
    define('BASEPATH', __DIR__ . '/');
    define('APPPATH', __DIR__ . '/application/');
    define('ENVIRONMENT', 'development');
    
    require_once 'application/config/config.php';
    require_once 'application/config/database.php';
    require_once 'application/config/database_helper.php';
    require_once 'application/models/Inquiry_model.php';
    
    echo "<p style='color: green;'>✅ All files loaded successfully!</p>";
    
    // Test database connection
    $db = Database::get_instance();
    echo "<p style='color: green;'>✅ Database connected!</p>";
    
    // Test model
    $model = new Inquiry_model();
    echo "<p style='color: green;'>✅ Model instantiated!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

?>
<hr>
<p><strong>What to do next:</strong></p>
<ul>
    <li>If endpoint is NOT reachable → Apache mod_rewrite issue</li>
    <li>If endpoint returns empty → Controller has error</li>
    <li>If endpoint returns HTML → Wrong route</li>
    <li>If files load OK → Issue is with routing</li>
</ul>

<p><a href="index.php">← Back to Landing Page</a></p>
