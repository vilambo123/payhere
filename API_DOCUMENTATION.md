# 📡 Loan System API Documentation

## Overview
The Loan System API allows you to retrieve and manage loan inquiries programmatically. All endpoints require API key authentication.

---

## 🔐 Authentication

### API Key
Before using the API, change the default API key in `application/controllers/Api.php`:

```php
// Line 11 - Change this to your secure key
$this->api_key = 'your_secure_api_key_here_change_in_production';
```

### How to Authenticate

**Method 1: Authorization Header (Recommended)**
```bash
Authorization: Bearer your_api_key_here
```

**Method 2: Query Parameter**
```bash
?api_key=your_api_key_here
```

---

## 📍 API Endpoints

### Base URL
```
http://localhost/payhere/index.php/api
```

---

## 1️⃣ Get API Documentation

**Endpoint:** `GET /api`

**Description:** Returns API documentation and available endpoints

**Example:**
```bash
curl http://localhost/payhere/index.php/api
```

**Response:**
```json
{
    "name": "Loan System API",
    "version": "1.0.0",
    "endpoints": [...]
}
```

---

## 2️⃣ Get All Inquiries

**Endpoint:** `GET /api/inquiries`

**Description:** Retrieve loan inquiries with optional filtering

### Query Parameters

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `api_key` | string | API key (if not using header) | `your_api_key` |
| `status` | string | Filter by status | `pending`, `contacted`, `approved`, `rejected` |
| `loan_type` | string | Filter by loan type | `personal`, `business`, `home`, `car` |
| `is_exported` | int | Filter by export status | `0` (not exported), `1` (exported) |
| `only_unexported` | int | Return only unexported | `1` (yes), `0` (no) |
| `limit` | int | Limit results | `10`, `50`, `100` |
| `offset` | int | Offset for pagination | `0`, `10`, `20` |
| `auto_mark` | int | Auto-mark as exported | `1` (yes), `0` (no) |

### Examples

**Get all unexported inquiries:**
```bash
curl -H "Authorization: Bearer your_api_key" \
  "http://localhost/payhere/index.php/api/inquiries?only_unexported=1"
```

**Get pending inquiries and auto-mark as exported:**
```bash
curl -H "Authorization: Bearer your_api_key" \
  "http://localhost/payhere/index.php/api/inquiries?status=pending&only_unexported=1&auto_mark=1"
```

**Get with pagination:**
```bash
curl -H "Authorization: Bearer your_api_key" \
  "http://localhost/payhere/index.php/api/inquiries?limit=20&offset=0"
```

**Using query parameter authentication:**
```bash
curl "http://localhost/payhere/index.php/api/inquiries?api_key=your_api_key&only_unexported=1"
```

### Success Response (200)
```json
{
    "success": true,
    "count": 5,
    "filters": {
        "is_exported": 0
    },
    "auto_marked": false,
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "+60 12-345-6789",
            "ic_number": "901234-12-3456",
            "loan_type": "personal",
            "loan_amount": "50000",
            "monthly_income": "5000",
            "message": "Need urgent loan",
            "status": "pending",
            "is_exported": 0,
            "exported_at": null,
            "ip_address": "192.168.1.1",
            "user_agent": "Mozilla/5.0...",
            "created_at": "2025-10-03 10:30:00"
        },
        ...
    ]
}
```

### Error Response (401)
```json
{
    "success": false,
    "error": "Unauthorized - Invalid or missing API key",
    "message": "Please provide a valid API key in the Authorization header or api_key query parameter"
}
```

---

## 3️⃣ Mark Inquiries as Exported

**Endpoint:** `POST /api/mark_exported`

**Description:** Manually mark specific inquiries as exported

### Request Body (JSON)
```json
{
    "ids": [1, 2, 3, 4, 5]
}
```

### Examples

**Using cURL:**
```bash
curl -X POST \
  -H "Authorization: Bearer your_api_key" \
  -H "Content-Type: application/json" \
  -d '{"ids": [1, 2, 3]}' \
  http://localhost/payhere/index.php/api/mark_exported
```

**Using JavaScript fetch:**
```javascript
fetch('http://localhost/payhere/index.php/api/mark_exported', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer your_api_key',
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        ids: [1, 2, 3, 4, 5]
    })
})
.then(response => response.json())
.then(data => console.log(data));
```

**Using Python:**
```python
import requests

url = 'http://localhost/payhere/index.php/api/mark_exported'
headers = {
    'Authorization': 'Bearer your_api_key',
    'Content-Type': 'application/json'
}
data = {
    'ids': [1, 2, 3, 4, 5]
}

response = requests.post(url, json=data, headers=headers)
print(response.json())
```

### Success Response (200)
```json
{
    "success": true,
    "message": "Inquiries marked as exported successfully",
    "marked_count": 5,
    "ids": [1, 2, 3, 4, 5]
}
```

### Error Response (400)
```json
{
    "success": false,
    "error": "Bad Request",
    "message": "Parameter \"ids\" is required and must be an array"
}
```

---

## 4️⃣ Reset Export Flag

**Endpoint:** `POST /api/reset_export`

**Description:** Reset export flag for specific inquiries or all inquiries

### Request Body (JSON)

**Reset specific inquiries:**
```json
{
    "ids": [1, 2, 3]
}
```

**Reset all inquiries:**
```json
{
    "ids": []
}
```
or send empty POST request

### Examples

**Reset specific inquiries:**
```bash
curl -X POST \
  -H "Authorization: Bearer your_api_key" \
  -H "Content-Type: application/json" \
  -d '{"ids": [1, 2, 3]}' \
  http://localhost/payhere/index.php/api/reset_export
```

**Reset all inquiries:**
```bash
curl -X POST \
  -H "Authorization: Bearer your_api_key" \
  -H "Content-Type: application/json" \
  -d '{}' \
  http://localhost/payhere/index.php/api/reset_export
```

### Success Response (200)
```json
{
    "success": true,
    "message": "Export flags reset successfully",
    "reset_count": 3,
    "ids": [1, 2, 3]
}
```

---

## 🔄 Common Workflows

### Workflow 1: Export New Inquiries (Recommended)

**Step 1: Get unexported inquiries and auto-mark**
```bash
curl -H "Authorization: Bearer your_api_key" \
  "http://localhost/payhere/index.php/api/inquiries?only_unexported=1&auto_mark=1"
```

This single request will:
- ✅ Get all unexported inquiries
- ✅ Automatically mark them as exported
- ✅ Prevent getting same inquiries next time

### Workflow 2: Export with Manual Marking

**Step 1: Get unexported inquiries**
```bash
curl -H "Authorization: Bearer your_api_key" \
  "http://localhost/payhere/index.php/api/inquiries?only_unexported=1"
```

**Step 2: Process the data in your system**

**Step 3: Mark as exported**
```bash
curl -X POST \
  -H "Authorization: Bearer your_api_key" \
  -H "Content-Type: application/json" \
  -d '{"ids": [1, 2, 3, 4, 5]}' \
  http://localhost/payhere/index.php/api/mark_exported
```

### Workflow 3: Get Specific Status with Export Filter

**Get all pending inquiries that haven't been exported:**
```bash
curl -H "Authorization: Bearer your_api_key" \
  "http://localhost/payhere/index.php/api/inquiries?status=pending&is_exported=0"
```

### Workflow 4: Re-export Data

**If you need to re-export data, reset the flag first:**
```bash
# Reset specific inquiries
curl -X POST \
  -H "Authorization: Bearer your_api_key" \
  -H "Content-Type: application/json" \
  -d '{"ids": [1, 2, 3]}' \
  http://localhost/payhere/index.php/api/reset_export

# Then export again
curl -H "Authorization: Bearer your_api_key" \
  "http://localhost/payhere/index.php/api/inquiries?only_unexported=1&auto_mark=1"
```

---

## 🗄️ Database Schema

### Export Flag Columns

The `inquiries` table includes these export-related columns:

| Column | Type | Description |
|--------|------|-------------|
| `is_exported` | TINYINT(1) | `0` = Not exported, `1` = Exported |
| `exported_at` | TIMESTAMP | When the inquiry was exported (NULL if not exported) |

### Setup Database

Run this SQL to add the export flag columns:

```sql
-- Run this in phpMyAdmin or MySQL command line
source add_export_flag.sql;
```

Or manually:
```sql
ALTER TABLE `inquiries` 
ADD COLUMN `is_exported` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=Not exported, 1=Exported' AFTER `status`,
ADD COLUMN `exported_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When the inquiry was exported' AFTER `is_exported`,
ADD INDEX `idx_is_exported` (`is_exported`),
ADD INDEX `idx_exported_at` (`exported_at`);
```

---

## 🔒 Security Best Practices

### 1. Change Default API Key
```php
// application/controllers/Api.php (Line 11)
$this->api_key = 'generate_a_long_random_secure_key_here_min_32_chars';
```

**Generate secure key:**
```bash
# Linux/Mac
openssl rand -hex 32

# Or use online generator
# https://randomkeygen.com/
```

### 2. Use HTTPS in Production
```bash
# Always use HTTPS for API calls in production
https://yourdomain.com/api/inquiries
```

### 3. Restrict API Access by IP (Optional)

Add this to `Api.php` constructor:
```php
$allowed_ips = ['192.168.1.100', '203.0.113.45'];
$client_ip = $_SERVER['REMOTE_ADDR'];

if (!in_array($client_ip, $allowed_ips)) {
    $this->json_response([
        'success' => false,
        'error' => 'Access denied - IP not whitelisted'
    ], 403);
}
```

### 4. Rate Limiting (Optional)

Consider implementing rate limiting to prevent API abuse.

---

## 📊 Response Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 400 | Bad Request (invalid parameters) |
| 401 | Unauthorized (invalid API key) |
| 403 | Forbidden (IP restriction) |
| 500 | Internal Server Error |

---

## 🧪 Testing the API

### Using Postman

1. **Create new request**
2. **Set method:** GET or POST
3. **Set URL:** `http://localhost/payhere/index.php/api/inquiries`
4. **Add Authorization:**
   - Go to "Headers" tab
   - Add: `Authorization: Bearer your_api_key`
5. **Send request**

### Using Browser (GET only)

```
http://localhost/payhere/index.php/api/inquiries?api_key=your_api_key&only_unexported=1
```

### Using PHP

```php
<?php
$api_key = 'your_api_key';
$url = 'http://localhost/payhere/index.php/api/inquiries?only_unexported=1&auto_mark=1';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $api_key
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
print_r($data);
?>
```

---

## ❓ Troubleshooting

### Issue: "Unauthorized - Invalid or missing API key"
**Solution:** 
- Check if API key matches the one in `Api.php`
- Verify Authorization header format: `Bearer YOUR_KEY`
- Try using query parameter: `?api_key=YOUR_KEY`

### Issue: "Call to undefined function getallheaders()"
**Solution:** 
- This happens on some PHP installations
- Use query parameter authentication instead: `?api_key=YOUR_KEY`

### Issue: Empty response
**Solution:**
- Check if database columns exist: `is_exported`, `exported_at`
- Run `add_export_flag.sql` to add columns
- Verify MySQL is running in XAMPP

### Issue: "Column 'is_exported' not found"
**Solution:**
```sql
-- Run this SQL in phpMyAdmin
ALTER TABLE `inquiries` 
ADD COLUMN `is_exported` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`,
ADD COLUMN `exported_at` TIMESTAMP NULL DEFAULT NULL AFTER `is_exported`;
```

---

## 📝 Example Integration

### Complete PHP Integration Example

```php
<?php
class LoanApiClient {
    private $base_url = 'http://localhost/payhere/index.php/api';
    private $api_key = 'your_api_key';
    
    private function request($endpoint, $method = 'GET', $data = null) {
        $url = $this->base_url . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json'
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'status' => $http_code,
            'data' => json_decode($response, true)
        ];
    }
    
    public function getUnexportedInquiries() {
        return $this->request('/inquiries?only_unexported=1&auto_mark=1');
    }
    
    public function markAsExported($ids) {
        return $this->request('/mark_exported', 'POST', ['ids' => $ids]);
    }
    
    public function resetExportFlag($ids = []) {
        return $this->request('/reset_export', 'POST', ['ids' => $ids]);
    }
}

// Usage
$api = new LoanApiClient();
$result = $api->getUnexportedInquiries();

if ($result['data']['success']) {
    foreach ($result['data']['data'] as $inquiry) {
        echo "Processing inquiry #{$inquiry['id']} - {$inquiry['name']}\n";
        // Your processing logic here
    }
}
?>
```

---

## 🎯 Summary

✅ **Setup:**
1. Run `add_export_flag.sql` to add database columns
2. Change API key in `Api.php`
3. Test with browser or Postman

✅ **Best Practice Workflow:**
```bash
# Single call to get and mark as exported
curl -H "Authorization: Bearer YOUR_KEY" \
  "http://localhost/payhere/index.php/api/inquiries?only_unexported=1&auto_mark=1"
```

✅ **Security:**
- Use HTTPS in production
- Generate strong API key (32+ characters)
- Consider IP whitelisting
- Implement rate limiting for production

---

**Need Help?** Check the inline API documentation:
```bash
curl http://localhost/payhere/index.php/api
```
