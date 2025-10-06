# 🔧 Form Submission Troubleshooting Guide

## "An error occurred. Please try again later."

If you're getting this error when submitting the loan application form, follow these steps:

---

## 🎯 Quick Fix Steps

### Step 1: Test Database Connection

**Visit:** `http://localhost/payhere/test-database.php`

**Expected:** ✅ Connected successfully

**If Failed:**
- Make sure MySQL is running in XAMPP Control Panel
- Check database name is `loan_system`
- Re-import `database_setup.sql` if needed

---

### Step 2: Run Form Submission Test

**Visit:** `http://localhost/payhere/test-form-submit.php`

This page will show you:
- ✅ Database connection status
- ✅ Tables exist check
- ✅ Direct insert test
- ✅ API endpoint test

**Run all tests and see where it fails!**

---

### Step 3: Check Browser Console

1. Open the landing page
2. Press **F12** to open Developer Tools
3. Click **Console** tab
4. Fill and submit the form
5. Look for errors in red

**Common errors you might see:**
- "Network error" → Database connection issue
- "404 Not Found" → URL routing issue
- "500 Internal Server Error" → PHP/Database error
- JSON parse error → Response is not valid JSON

---

## 🔍 Detailed Troubleshooting

### Issue 1: Database Not Connected

**Symptoms:**
- "Connection error" message
- "Database helper not loaded"

**Solutions:**

**A. Check XAMPP MySQL:**
```
1. Open XAMPP Control Panel
2. Make sure MySQL shows "Running" in green
3. If not, click "Start" next to MySQL
```

**B. Verify Database Exists:**
```
1. Go to http://localhost/phpmyadmin
2. Look for "loan_system" in left sidebar
3. If missing, import database_setup.sql:
   - Click "Import" tab
   - Choose database_setup.sql file
   - Click "Go"
```

**C. Check Database Config:**
```php
// application/config/database.php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',  // Empty for XAMPP default
    'database' => 'loan_system',
    'port' => 3306
);
```

---

### Issue 2: URL Routing Error

**Symptoms:**
- "404 Not Found" in console
- Form submits but nothing happens

**Solutions:**

**A. Check .htaccess exists:**
```bash
# Should be in root directory
/workspace/.htaccess
```

**B. Verify Apache mod_rewrite:**
```
1. In XAMPP, mod_rewrite should be enabled by default
2. Check httpd.conf if needed
```

**C. Test direct URL:**
```
Visit: http://localhost/payhere/index.php/submit-inquiry
Should show: {"success":false,"message":"..."}
Not: 404 error
```

---

### Issue 3: Form Validation Errors

**Symptoms:**
- "Name is required"
- "Email must be valid"
- Other validation messages

**Solutions:**

**Check form fields are filled:**
- Name (required)
- Email (required, must be valid email)
- Phone (required)
- Loan Type (required, must select one)
- Loan Amount (required, must be number)
- Terms checkbox (required, must check)

**Test with these values:**
```
Name: John Doe
Email: john@example.com
Phone: +60123456789
Loan Type: Personal Loan
Loan Amount: 50000
Monthly Income: 5000 (optional)
Message: Test (optional)
✅ Check the terms box
```

---

### Issue 4: Database Tables Missing

**Symptoms:**
- "Table 'loan_system.inquiries' doesn't exist"

**Solution:**

**Re-import Database:**
```
1. Go to http://localhost/phpmyadmin
2. Click "loan_system" database (or create it if missing)
3. Click "Import" tab
4. Choose database_setup.sql
5. Check "Drop tables if exists" (optional)
6. Click "Go"
```

**Verify tables exist:**
```sql
SHOW TABLES FROM loan_system;

Should show:
- inquiries
- loan_types
- contacts
- site_settings
```

---

### Issue 5: PHP Errors

**Symptoms:**
- White screen
- "Fatal error" messages
- "Class not found"

**Solutions:**

**A. Check error logs:**
```
XAMPP error log location:
C:\xampp\apache\logs\error.log
C:\xampp\php\logs\php_error_log.txt
```

**B. Enable error display (temporarily):**
```php
// Add to index.php (top of file)
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

**C. Check class files exist:**
```
application/models/Inquiry_model.php
application/config/database_helper.php
application/controllers/Home.php
```

---

### Issue 6: AJAX/Fetch Error

**Symptoms:**
- Console shows: "Failed to fetch"
- Network tab shows CORS error

**Solutions:**

**Check URL is correct:**
```javascript
// Open browser console on landing page
// Type this and press Enter:
console.log(window.location.origin + window.location.pathname);

// Should show something like:
// http://localhost/payhere/
```

**Clear browser cache:**
```
1. Press Ctrl+Shift+Delete
2. Clear cache and cookies
3. Refresh page (Ctrl+F5)
```

---

## 🧪 Using the Test Page

### test-form-submit.php Features

**1. Database Connection Test**
- Shows if MySQL is connected
- Lists all tables
- Checks if required tables exist

**2. Direct Insert Test**
- Bypasses the API
- Tests database insert directly
- Shows if database operations work

**3. API Endpoint Test**
- Pre-filled form
- Tests the actual submit endpoint
- Shows detailed response

**How to use:**
1. Visit `http://localhost/payhere/test-form-submit.php`
2. Click "Run Direct Insert Test"
   - ✅ If successful → Database works!
   - ❌ If failed → Database issue
3. Click "Submit Test Form"
   - ✅ If successful → Everything works!
   - ❌ If failed → Check the error message

---

## 📋 Checklist

Use this checklist to diagnose your issue:

- [ ] MySQL running in XAMPP Control Panel
- [ ] Apache running in XAMPP Control Panel
- [ ] Database "loan_system" exists in phpMyAdmin
- [ ] All 4 tables exist (inquiries, loan_types, contacts, site_settings)
- [ ] test-database.php shows "Connected successfully"
- [ ] test-form-submit.php "Database Connection" test passes
- [ ] test-form-submit.php "Direct Insert" test passes
- [ ] test-form-submit.php "API Endpoint" test passes
- [ ] Browser console (F12) shows no errors
- [ ] All form fields are filled correctly
- [ ] Terms checkbox is checked

---

## 🎯 Most Common Causes

**90% of form errors are caused by:**

1. **MySQL not running** (50%)
   - Solution: Start MySQL in XAMPP

2. **Database not imported** (30%)
   - Solution: Import database_setup.sql

3. **Wrong URL path** (10%)
   - Solution: Check you're accessing via http://localhost/payhere/

4. **Terms checkbox not checked** (10%)
   - Solution: Check the terms checkbox before submit

---

## 🔧 Advanced Debugging

### Enable Debug Mode

**Edit:** `application/controllers/Home.php`

Already added! Check browser console for debug output:
```javascript
console.log('Submitting to:', submitUrl);
console.log('Form data:', data);
console.log('Response status:', response.status);
console.log('Response text:', text);
```

### Check Apache Logs

**Windows:**
```
C:\xampp\apache\logs\error.log
```

**Look for:**
- PHP Fatal errors
- MySQL connection errors
- Permission errors

### Test Direct PHP Execution

Create `test-inquiry.php` in root:
```php
<?php
require_once 'application/config/database.php';
require_once 'application/config/database_helper.php';
require_once 'application/models/Inquiry_model.php';

$data = [
    'name' => 'Test',
    'email' => 'test@test.com',
    'phone' => '123',
    'loan_type' => 'personal',
    'loan_amount' => 50000,
    'status' => 'pending'
];

$model = new Inquiry_model();
$id = $model->save($data);

echo "Insert ID: " . $id;
?>
```

Visit: `http://localhost/payhere/test-inquiry.php`

---

## ✅ Success Criteria

**Form submission is working when:**
- ✅ Form submits without "An error occurred" message
- ✅ Shows "Thank you! We have received your application..."
- ✅ Form fields clear after submission
- ✅ New record appears in phpMyAdmin inquiries table
- ✅ Admin dashboard shows the new inquiry
- ✅ No errors in browser console

---

## 🆘 Still Not Working?

**Provide these details for help:**

1. **What test-form-submit.php shows:**
   - Test 1 result (Database Connection)
   - Test 2 result (Direct Insert)
   - Test 3 result (API Endpoint)

2. **Browser Console output:**
   - Press F12 → Console tab
   - Copy any red errors

3. **Error log contents:**
   - Check `C:\xampp\apache\logs\error.log`
   - Copy last 20 lines

4. **phpMyAdmin check:**
   - Does loan_system database exist?
   - Does inquiries table exist?
   - How many rows in inquiries table?

---

## 📞 Quick Fixes Summary

| Error Message | Quick Fix |
|--------------|-----------|
| "Connection error" | Start MySQL in XAMPP |
| "Database doesn't exist" | Import database_setup.sql |
| "Table doesn't exist" | Re-import database_setup.sql |
| "Network error" | Check URL is correct |
| "Validation errors" | Fill all required fields |
| Nothing happens | Check browser console (F12) |
| White screen | Check PHP error logs |

---

**Test Page:** `http://localhost/payhere/test-form-submit.php`  
**Database Test:** `http://localhost/payhere/test-database.php`  
**Landing Page:** `http://localhost/payhere/`

**Good luck! 🚀**
