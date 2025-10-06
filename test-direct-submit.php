<?php
/**
 * Direct Form Submission Test
 * Tests the submit-inquiry endpoint directly
 */

// Test data
$testData = [
    'name' => 'Ahmad bin Abdullah',
    'email' => 'ahmad@example.com',
    'phone' => '0123456789',
    'loan_type' => 'personal',
    'loan_amount' => '50000',
    'income' => '5000',
    'message' => 'Test submission',
    'terms' => 'on'
];

echo "<h1>Direct Submission Test</h1>";
echo "<p>Testing: <code>index.php/submit-inquiry</code></p>";
echo "<hr>";

// Build the full URL
$baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/';
$submitUrl = $baseUrl . 'index.php/submit-inquiry';

echo "<h3>1. Testing URL</h3>";
echo "<p><strong>Submit URL:</strong> <code>$submitUrl</code></p>";

// Test with cURL
echo "<h3>2. Making Request...</h3>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $submitUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($testData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

curl_close($ch);

// Split headers and body
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

echo "<h3>3. Response Details</h3>";
echo "<p><strong>HTTP Status Code:</strong> <code>$httpCode</code></p>";

echo "<h4>Headers:</h4>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
echo htmlspecialchars($headers);
echo "</pre>";

echo "<h4>Response Body:</h4>";
echo "<p><strong>Length:</strong> " . strlen($body) . " bytes</p>";
echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
echo htmlspecialchars($body);
echo "</pre>";

// Try to parse as JSON
echo "<h3>4. JSON Parse Test</h3>";
if (empty(trim($body))) {
    echo "<p style='color: red;'><strong>❌ ERROR:</strong> Response body is EMPTY!</p>";
    echo "<p><strong>Possible causes:</strong></p>";
    echo "<ul>";
    echo "<li>MySQL is not running in XAMPP</li>";
    echo "<li>Database 'loan_system' doesn't exist</li>";
    echo "<li>PHP error occurred (check error logs)</li>";
    echo "<li>Route not configured correctly</li>";
    echo "</ul>";
} else {
    $json = json_decode($body, true);
    if ($json === null) {
        echo "<p style='color: red;'><strong>❌ JSON Parse Error:</strong> " . json_last_error_msg() . "</p>";
        echo "<p>Response is not valid JSON. It might contain HTML or PHP errors.</p>";
        
        // Check if it contains HTML
        if (strpos($body, '<!DOCTYPE') !== false || strpos($body, '<html') !== false) {
            echo "<p style='color: orange;'><strong>⚠️ Response contains HTML</strong> - This usually means:</p>";
            echo "<ul>";
            echo "<li>A PHP error occurred</li>";
            echo "<li>The page redirected</li>";
            echo "<li>Wrong URL or routing issue</li>";
            echo "</ul>";
        }
        
        // Check for PHP errors
        if (strpos($body, 'Fatal error') !== false || strpos($body, 'Warning') !== false) {
            echo "<p style='color: red;'><strong>⚠️ PHP Error Detected</strong></p>";
            echo "<p>First 500 characters of error:</p>";
            echo "<pre style='background: #fee; padding: 10px;'>";
            echo htmlspecialchars(substr($body, 0, 500));
            echo "</pre>";
        }
    } else {
        echo "<p style='color: green;'><strong>✅ Valid JSON!</strong></p>";
        echo "<pre style='background: #e0ffe0; padding: 10px; border-radius: 5px;'>";
        echo json_encode($json, JSON_PRETTY_PRINT);
        echo "</pre>";
        
        if (isset($json['success'])) {
            if ($json['success']) {
                echo "<p style='color: green; font-size: 1.2em;'><strong>✅ SUCCESS!</strong> Inquiry saved with ID: " . ($json['inquiry_id'] ?? 'N/A') . "</p>";
            } else {
                echo "<p style='color: orange; font-size: 1.2em;'><strong>⚠️ FAILED:</strong> " . $json['message'] . "</p>";
            }
        }
    }
}

echo "<hr>";
echo "<h3>5. Quick Diagnostics</h3>";

// Check MySQL
echo "<h4>MySQL Status:</h4>";
try {
    $conn = new mysqli('localhost', 'root', '', 'loan_system');
    if ($conn->connect_error) {
        echo "<p style='color: red;'>❌ MySQL: <strong>NOT CONNECTED</strong> - " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✅ MySQL: <strong>CONNECTED</strong></p>";
        
        // Check table
        $result = $conn->query("SHOW TABLES LIKE 'inquiries'");
        if ($result && $result->num_rows > 0) {
            echo "<p style='color: green;'>✅ Table 'inquiries': <strong>EXISTS</strong></p>";
        } else {
            echo "<p style='color: red;'>❌ Table 'inquiries': <strong>NOT FOUND</strong></p>";
        }
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ MySQL Error: " . $e->getMessage() . "</p>";
}

// Check files
echo "<h4>File Status:</h4>";
$requiredFiles = [
    'index.php' => 'Entry point',
    'application/controllers/Home.php' => 'Home controller',
    'application/models/Inquiry_model.php' => 'Inquiry model',
    'application/config/database.php' => 'Database config',
    'application/config/database_helper.php' => 'Database helper',
    'application/helpers/malaysian_validation_helper.php' => 'Validation helper'
];

foreach ($requiredFiles as $file => $desc) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $desc: <code>$file</code></p>";
    } else {
        echo "<p style='color: red;'>❌ $desc: <code>$file</code> - <strong>NOT FOUND</strong></p>";
    }
}

echo "<hr>";
echo "<h3>Actions</h3>";
echo "<p><a href='test-form-submit.php'>Try Form Submission Test</a></p>";
echo "<p><a href='test-database.php'>Try Database Connection Test</a></p>";
echo "<p><a href='index.php'>Back to Landing Page</a></p>";
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h1, h2, h3, h4 {
        color: #333;
    }
    code {
        background: #e0e0e0;
        padding: 2px 6px;
        border-radius: 3px;
        font-family: 'Courier New', monospace;
    }
    pre {
        overflow-x: auto;
        max-height: 400px;
    }
    a {
        color: #2563eb;
        text-decoration: none;
        font-weight: bold;
    }
    a:hover {
        text-decoration: underline;
    }
</style>
