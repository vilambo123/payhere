<!DOCTYPE html>
<html>
<head>
    <title>Simple POST Test</title>
</head>
<body>
    <h1>Simple POST Test</h1>
    
    <p>This tests if POST data reaches the endpoint.</p>
    
    <button onclick="testPost()">Test POST Request</button>
    
    <div id="result"></div>
    
    <script>
        function testPost() {
            const resultDiv = document.getElementById('result');
            resultDiv.innerHTML = '<p>Sending...</p>';
            
            const data = {
                name: 'Test Name',
                email: 'test@test.com',
                phone: '0123456789',
                loan_type: 'personal',
                loan_amount: '50000'
            };
            
            const url = window.location.origin + window.location.pathname.replace('test-simple-post.php', '') + 'index.php/submit-inquiry';
            
            console.log('URL:', url);
            console.log('Data:', data);
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            })
            .then(response => {
                console.log('Status:', response.status);
                console.log('Headers:', response.headers);
                return response.text();
            })
            .then(text => {
                console.log('Response:', text);
                console.log('Length:', text.length);
                
                resultDiv.innerHTML = '<h3>Response:</h3>' +
                    '<p><strong>Length:</strong> ' + text.length + ' bytes</p>' +
                    '<p><strong>Text:</strong></p>' +
                    '<pre style="background: #f5f5f5; padding: 10px;">' + (text || '(empty)') + '</pre>';
                
                if (text.length === 0) {
                    resultDiv.innerHTML += '<p style="color: red;"><strong>❌ EMPTY RESPONSE</strong></p>' +
                        '<p>This means:</p>' +
                        '<ul>' +
                        '<li>Controller method is NOT being called</li>' +
                        '<li>OR controller is called but exits silently</li>' +
                        '<li>OR output buffering issue</li>' +
                        '</ul>' +
                        '<p><strong>Check:</strong> C:\\xampp\\apache\\logs\\error.log for "Submit inquiry START"</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultDiv.innerHTML = '<p style="color: red;">Error: ' + error.message + '</p>';
            });
        }
    </script>
    
    <hr>
    <h3>Manual Check:</h3>
    <ol>
        <li>Click the "Test POST Request" button above</li>
        <li>Open browser console (F12) and check the logs</li>
        <li>Check if response length is 0 or > 0</li>
        <li>Check Apache error.log: <code>C:\xampp\apache\logs\error.log</code></li>
        <li>Look for line: <code>=== Submit inquiry START ===</code></li>
    </ol>
    
    <p><strong>If error.log shows "Submit inquiry START":</strong> Controller is being called but not outputting</p>
    <p><strong>If error.log does NOT show it:</strong> Controller method is NOT being reached</p>
    
    <p><a href="index.php">← Back to Landing Page</a></p>
</body>
</html>
