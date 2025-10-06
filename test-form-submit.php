<?php
// Define BASEPATH to prevent "No direct script access allowed" error
define('BASEPATH', __DIR__ . '/');

// Define ENVIRONMENT constant
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Form Submission Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .result { padding: 20px; margin: 20px 0; border-radius: 5px; }
        .success { background: #d1fae5; color: #065f46; }
        .error { background: #fee2e2; color: #991b1b; }
        .info { background: #e0f2fe; color: #0c4a6e; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
        button { padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #1e40af; }
        .test-form { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        input, select, textarea { width: 100%; padding: 8px; margin: 5px 0 15px; border: 2px solid #e2e8f0; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🧪 Form Submission Test</h1>
    
    <div class="info result">
        <strong>This page tests:</strong>
        <ul>
            <li>Database connection</li>
            <li>Form submission endpoint</li>
            <li>Data validation</li>
            <li>Insert operation</li>
        </ul>
    </div>

    <!-- Test 1: Database Connection -->
    <h2>Test 1: Database Connection</h2>
    <div id="dbTest" class="result info">Testing...</div>
    
    <?php
    try {
        require_once __DIR__ . '/application/config/database.php';
        require_once __DIR__ . '/application/config/database_helper.php';
        
        $db = Database::get_instance();
        $conn = $db->get_connection();
        
        if ($conn->connect_error) {
            echo '<div class="result error">❌ Database connection failed: ' . $conn->connect_error . '</div>';
        } else {
            echo '<div class="result success">✅ Database connected successfully!</div>';
            
            // Test if tables exist
            $tables = ['inquiries', 'loan_types', 'site_settings'];
            echo '<div class="result info"><strong>Tables check:</strong><ul>';
            foreach ($tables as $table) {
                $result = $conn->query("SHOW TABLES LIKE '{$table}'");
                if ($result && $result->num_rows > 0) {
                    echo "<li>✅ Table '{$table}' exists</li>";
                } else {
                    echo "<li>❌ Table '{$table}' NOT FOUND</li>";
                }
            }
            echo '</ul></div>';
        }
    } catch (Exception $e) {
        echo '<div class="result error">❌ Error: ' . $e->getMessage() . '</div>';
    }
    ?>

    <!-- Test 2: Direct Insert -->
    <h2>Test 2: Direct Database Insert</h2>
    <?php
    if (isset($_POST['test_direct'])) {
        try {
            require_once __DIR__ . '/application/models/Inquiry_model.php';
            
            $test_data = [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone' => '+60123456789',
                'loan_type' => 'personal',
                'loan_amount' => 50000,
                'monthly_income' => 5000,
                'message' => 'Test inquiry',
                'status' => 'pending'
            ];
            
            $inquiry_model = new Inquiry_model();
            $insert_id = $inquiry_model->save($test_data);
            
            if ($insert_id) {
                echo '<div class="result success">✅ Direct insert successful! ID: ' . $insert_id . '</div>';
            } else {
                echo '<div class="result error">❌ Insert failed</div>';
            }
        } catch (Exception $e) {
            echo '<div class="result error">❌ Error: ' . $e->getMessage() . '</div>';
        }
    }
    ?>
    <form method="POST">
        <button type="submit" name="test_direct">Run Direct Insert Test</button>
    </form>

    <!-- Test 3: API Endpoint Test -->
    <h2>Test 3: API Endpoint Test</h2>
    <div class="test-form">
        <form id="testForm">
            <label>Name:</label>
            <input type="text" name="name" value="John Doe" required>
            
            <label>Email:</label>
            <input type="email" name="email" value="john@example.com" required>
            
            <label>Phone:</label>
            <input type="tel" name="phone" value="+60123456789" required>
            
            <label>Loan Type:</label>
            <select name="loan_type" required>
                <option value="personal">Personal Loan</option>
                <option value="business">Business Loan</option>
                <option value="home">Home Loan</option>
                <option value="car">Car Loan</option>
            </select>
            
            <label>Loan Amount (RM):</label>
            <input type="number" name="loan_amount" value="50000" required>
            
            <label>Monthly Income (RM):</label>
            <input type="number" name="income" value="5000">
            
            <label>Message:</label>
            <textarea name="message" rows="3">Test submission from test page</textarea>
            
            <button type="submit">Submit Test Form</button>
        </form>
    </div>
    
    <div id="formResult"></div>

    <script>
        document.getElementById('testForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const resultDiv = document.getElementById('formResult');
            resultDiv.innerHTML = '<div class="result info">Submitting...</div>';
            
            const formData = new FormData(this);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            // Build URL
            const baseUrl = window.location.origin + window.location.pathname.replace('test-form-submit.php', '');
            const submitUrl = baseUrl + 'index.php/submit-inquiry';
            
            console.log('Submit URL:', submitUrl);
            console.log('Form data:', data);
            
            fetch(submitUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);
                
                let html = '<div class="result">';
                html += '<h3>Response:</h3>';
                html += '<pre>' + text + '</pre>';
                
                try {
                    const result = JSON.parse(text);
                    if (result.success) {
                        html = '<div class="result success">';
                        html += '<h3>✅ Success!</h3>';
                        html += '<p>' + result.message + '</p>';
                        if (result.inquiry_id) {
                            html += '<p><strong>Inquiry ID:</strong> ' + result.inquiry_id + '</p>';
                        }
                        html += '<pre>' + JSON.stringify(result, null, 2) + '</pre>';
                    } else {
                        html = '<div class="result error">';
                        html += '<h3>❌ Failed</h3>';
                        html += '<p>' + result.message + '</p>';
                        html += '<pre>' + JSON.stringify(result, null, 2) + '</pre>';
                    }
                } catch (e) {
                    html = '<div class="result error">';
                    html += '<h3>❌ JSON Parse Error</h3>';
                    html += '<p>' + e.message + '</p>';
                    html += '<h4>Raw response:</h4>';
                    html += '<pre>' + text + '</pre>';
                }
                
                html += '</div>';
                resultDiv.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = '<div class="result error"><h3>❌ Request Failed</h3><p>' + error.message + '</p></div>';
            });
        });
    </script>

    <hr>
    <p><a href="index.php">← Back to Landing Page</a> | <a href="test-database.php">Database Test</a> | <a href="index.php/admin">Admin Dashboard</a></p>
</body>
</html>
