<?php
// Quick path test for debugging
echo "<h2>Path Configuration Test</h2>";

$base = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$base .= "://" . $_SERVER['HTTP_HOST'];
$base .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);

echo "<p><strong>Base URL:</strong> " . $base . "</p>";
echo "<p><strong>CSS Path:</strong> " . $base . "public/css/style.css</p>";
echo "<p><strong>JS Path:</strong> " . $base . "public/js/script.js</p>";

echo "<hr>";
echo "<h3>Check if files exist:</h3>";
echo "<p>CSS exists: " . (file_exists(__DIR__ . '/public/css/style.css') ? 'YES' : 'NO') . "</p>";
echo "<p>JS exists: " . (file_exists(__DIR__ . '/public/js/script.js') ? 'YES' : 'NO') . "</p>";

echo "<hr>";
echo "<h3>Test Links:</h3>";
echo "<p><a href='" . $base . "public/css/style.css'>Open CSS File</a></p>";
echo "<p><a href='" . $base . "public/js/script.js'>Open JS File</a></p>";
echo "<p><a href='" . $base . "'>Back to Home</a></p>";
?>
