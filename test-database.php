<?php
/**
 * Database Connection Test
 * Test your MySQL/phpMyAdmin connection
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #2563eb; }
        .success { color: #10b981; font-size: 20px; font-weight: bold; }
        .error { color: #ef4444; font-size: 20px; font-weight: bold; }
        .info { background: #e0f2fe; padding: 15px; border-radius: 5px; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #2563eb; color: white; }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .btn:hover { background: #1e40af; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔌 Database Connection Test</h1>
        
        <?php
        // Database configuration
        $hostname = 'localhost';
        $username = 'root';
        $password = '';
        $database = 'loan_system';
        $port = 3306;
        
        // Try to connect
        $conn = new mysqli($hostname, $username, $password, $database, $port);
        
        if ($conn->connect_error) {
            echo '<p class="error">❌ Connection Failed!</p>';
            echo '<div class="info">';
            echo '<strong>Error:</strong> ' . $conn->connect_error . '<br>';
            echo '<strong>Error Code:</strong> ' . $conn->connect_errno . '<br><br>';
            echo '<strong>Troubleshooting:</strong><ul>';
            echo '<li>Make sure MySQL is running in XAMPP Control Panel</li>';
            echo '<li>Check if database "loan_system" exists in phpMyAdmin</li>';
            echo '<li>Verify MySQL username and password</li>';
            echo '<li>Check if port 3306 is not blocked</li>';
            echo '</ul></div>';
            
            echo '<a href="http://localhost/phpmyadmin" target="_blank" class="btn">Open phpMyAdmin</a>';
            echo '<a href="INSTALLATION_DATABASE.md" class="btn">View Installation Guide</a>';
        } else {
            echo '<p class="success">✅ Connected Successfully!</p>';
            
            echo '<div class="info">';
            echo '<strong>Database:</strong> ' . $database . '<br>';
            echo '<strong>Host:</strong> ' . $hostname . ':' . $port . '<br>';
            echo '<strong>User:</strong> ' . $username . '<br>';
            echo '<strong>MySQL Version:</strong> ' . $conn->server_info;
            echo '</div>';
            
            // Check tables
            echo '<h2>📊 Database Tables</h2>';
            $result = $conn->query("SHOW TABLES");
            
            if ($result && $result->num_rows > 0) {
                echo '<table>';
                echo '<tr><th>Table Name</th><th>Record Count</th><th>Status</th></tr>';
                
                while ($row = $result->fetch_array()) {
                    $table = $row[0];
                    $count_result = $conn->query("SELECT COUNT(*) as total FROM `{$table}`");
                    
                    if ($count_result) {
                        $count_row = $count_result->fetch_assoc();
                        $count = $count_row['total'];
                        echo "<tr>";
                        echo "<td><strong>{$table}</strong></td>";
                        echo "<td>{$count} records</td>";
                        echo "<td>✅ OK</td>";
                        echo "</tr>";
                    }
                }
                
                echo '</table>';
            } else {
                echo '<p class="error">⚠️ No tables found!</p>';
                echo '<p>Please import the database_setup.sql file.</p>';
            }
            
            // Test inquiries table
            echo '<h2>📝 Recent Inquiries</h2>';
            $result = $conn->query("SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 5");
            
            if ($result && $result->num_rows > 0) {
                echo '<table>';
                echo '<tr><th>ID</th><th>Name</th><th>Email</th><th>Loan Type</th><th>Amount</th><th>Status</th><th>Date</th></tr>';
                
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['id']}</td>";
                    echo "<td>{$row['name']}</td>";
                    echo "<td>{$row['email']}</td>";
                    echo "<td>{$row['loan_type']}</td>";
                    echo "<td>RM " . number_format($row['loan_amount'], 2) . "</td>";
                    echo "<td>{$row['status']}</td>";
                    echo "<td>{$row['created_at']}</td>";
                    echo "</tr>";
                }
                
                echo '</table>';
            } else {
                echo '<p>No inquiries found yet. Submit the form on the landing page!</p>';
            }
            
            echo '<h2>🎯 Quick Links</h2>';
            echo '<a href="index.php" class="btn">Go to Landing Page</a>';
            echo '<a href="http://localhost/phpmyadmin" target="_blank" class="btn">Open phpMyAdmin</a>';
            
            $conn->close();
        }
        ?>
    </div>
</body>
</html>
