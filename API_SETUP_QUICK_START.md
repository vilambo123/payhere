# 🚀 API Quick Start Guide

## ✅ **SETUP CHECKLIST**

### Step 1: Add Database Columns ⚡
Run this SQL in phpMyAdmin:

```sql
-- Option 1: Run the SQL file
-- Go to phpMyAdmin → Import → Choose file: add_export_flag.sql

-- Option 2: Copy-paste this SQL
ALTER TABLE `inquiries` 
ADD COLUMN `is_exported` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=Not exported, 1=Exported' AFTER `status`,
ADD COLUMN `exported_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When the inquiry was exported' AFTER `is_exported`,
ADD INDEX `idx_is_exported` (`is_exported`),
ADD INDEX `idx_exported_at` (`exported_at`);
```

### Step 2: Set Your API Key 🔑

Edit `application/controllers/Api.php` (Line 11):

```php
// CHANGE THIS! Generate a secure random key
$this->api_key = 'your_secure_api_key_here_change_in_production';
```

**Generate a secure key:**
```bash
# Linux/Mac
openssl rand -hex 32

# Output example: a7f3c9e2b1d4f8a6c3e9b2d5f7a4c1e8...
```

Or use: https://randomkeygen.com/

### Step 3: Test the API 🧪

Open in browser:
```
http://localhost/payhere/test_api.html
```

Or use cURL:
```bash
curl -H "Authorization: Bearer your_api_key" \
  "http://localhost/payhere/index.php/api/inquiries?only_unexported=1"
```

---

## 🎯 **MOST COMMON USAGE**

### Get All New (Unexported) Inquiries

This is the **recommended** workflow - one call does everything:

```bash
curl -H "Authorization: Bearer YOUR_API_KEY" \
  "http://localhost/payhere/index.php/api/inquiries?only_unexported=1&auto_mark=1"
```

**What it does:**
✅ Gets all inquiries that haven't been exported yet  
✅ Automatically marks them as exported  
✅ Next call won't return the same inquiries  

---

## 📍 **ALL API ENDPOINTS**

### Base URL
```
http://localhost/payhere/index.php/api
```

### 1. Get API Documentation
```bash
GET /api
```

### 2. Get Inquiries (with filters)
```bash
GET /api/inquiries?only_unexported=1&auto_mark=1
```

**Query Parameters:**
- `only_unexported=1` - Only get unexported inquiries
- `auto_mark=1` - Automatically mark as exported
- `status=pending` - Filter by status
- `loan_type=personal` - Filter by loan type
- `limit=10` - Limit results
- `offset=0` - Pagination offset

### 3. Mark as Exported
```bash
POST /api/mark_exported
Body: {"ids": [1, 2, 3]}
```

### 4. Reset Export Flag
```bash
POST /api/reset_export
Body: {"ids": [1, 2, 3]}
```

---

## 🔐 **AUTHENTICATION**

### Method 1: Authorization Header (Recommended)
```bash
curl -H "Authorization: Bearer YOUR_API_KEY" \
  "http://localhost/payhere/index.php/api/inquiries"
```

### Method 2: Query Parameter
```bash
curl "http://localhost/payhere/index.php/api/inquiries?api_key=YOUR_API_KEY"
```

---

## 💻 **CODE EXAMPLES**

### PHP Example
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

if ($data['success']) {
    foreach ($data['data'] as $inquiry) {
        echo "New inquiry: {$inquiry['name']} - {$inquiry['email']}\n";
    }
}
?>
```

### JavaScript Example
```javascript
const apiKey = 'your_api_key';
const url = 'http://localhost/payhere/index.php/api/inquiries?only_unexported=1&auto_mark=1';

fetch(url, {
    headers: {
        'Authorization': 'Bearer ' + apiKey
    }
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        data.data.forEach(inquiry => {
            console.log(`New inquiry: ${inquiry.name} - ${inquiry.email}`);
        });
    }
});
```

### Python Example
```python
import requests

api_key = 'your_api_key'
url = 'http://localhost/payhere/index.php/api/inquiries'
params = {
    'only_unexported': 1,
    'auto_mark': 1
}

response = requests.get(url, params=params, headers={
    'Authorization': f'Bearer {api_key}'
})

data = response.json()

if data['success']:
    for inquiry in data['data']:
        print(f"New inquiry: {inquiry['name']} - {inquiry['email']}")
```

---

## 📊 **RESPONSE FORMAT**

### Success Response (200)
```json
{
    "success": true,
    "count": 3,
    "filters": {
        "is_exported": 0
    },
    "auto_marked": true,
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
            "is_exported": 1,
            "exported_at": "2025-10-03 10:30:00",
            "created_at": "2025-10-03 10:00:00"
        }
    ]
}
```

### Error Response (401)
```json
{
    "success": false,
    "error": "Unauthorized - Invalid or missing API key"
}
```

---

## ❓ **TROUBLESHOOTING**

### ❌ "Column 'is_exported' not found"
**Solution:** Run `add_export_flag.sql` in phpMyAdmin

### ❌ "Unauthorized - Invalid API key"
**Solution:** 
1. Check API key in `Api.php` matches your request
2. Verify Authorization header: `Bearer YOUR_KEY`

### ❌ "Call to undefined function getallheaders()"
**Solution:** Use query parameter instead:
```bash
?api_key=YOUR_API_KEY
```

### ❌ Empty response or no data
**Solution:**
1. Check if there are inquiries in database
2. Try without filters first: `/api/inquiries`
3. Check if all inquiries are already marked as exported

---

## 🔄 **TYPICAL WORKFLOW**

### Scenario: Export to CRM System

```bash
# Step 1: Get new inquiries (auto-mark as exported)
curl -H "Authorization: Bearer YOUR_KEY" \
  "http://localhost/payhere/index.php/api/inquiries?only_unexported=1&auto_mark=1" \
  > new_inquiries.json

# Step 2: Process in your system
# (Your CRM processing code here)

# Step 3: Done! Next call will only return NEW inquiries
```

### Scenario: Re-export Data

```bash
# Reset specific inquiries
curl -X POST \
  -H "Authorization: Bearer YOUR_KEY" \
  -H "Content-Type: application/json" \
  -d '{"ids": [1, 2, 3]}' \
  http://localhost/payhere/index.php/api/reset_export

# Export again
curl -H "Authorization: Bearer YOUR_KEY" \
  "http://localhost/payhere/index.php/api/inquiries?only_unexported=1&auto_mark=1"
```

---

## 📚 **FULL DOCUMENTATION**

For complete API documentation, see:
- **API_DOCUMENTATION.md** - Complete reference
- **test_api.html** - Interactive testing page

---

## 🎉 **YOU'RE READY!**

Test your API now:
```bash
# Replace YOUR_API_KEY with your actual key
curl -H "Authorization: Bearer YOUR_API_KEY" \
  "http://localhost/payhere/index.php/api/inquiries?only_unexported=1"
```

Or open:
```
http://localhost/payhere/test_api.html
```

---

**Questions?** Check `API_DOCUMENTATION.md` for detailed examples and use cases.
