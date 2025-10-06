<?php
/**
 * Direct Controller Test
 * Bypass routing and call controller directly
 */

// Define constants
define('BASEPATH', __DIR__ . '/');
define('APPPATH', __DIR__ . '/application/');
define('ENVIRONMENT', 'development');

// Set POST data
$_POST = [
    'name' => 'Test User',
    'email' => 'test@test.com',
    'phone' => '0123456789',
    'loan_type' => 'personal',
    'loan_amount' => '50000',
    'income' => '5000',
    'message' => 'Direct test'
];

echo "<h1>Direct Controller Test</h1>";
echo "<p>Calling Home::submit_inquiry() directly...</p>";

// Load bootstrap
require_once 'application/config/bootstrap.php';

// Create controller instance
$home = new Home();

echo "<p>Controller instantiated. Now calling submit_inquiry()...</p>";
echo "<hr>";

// Call the method
$home->submit_inquiry();

echo "<hr>";
echo "<p>Method completed.</p>";
?>
