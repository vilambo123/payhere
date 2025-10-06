# 🔧 Fix: JSON Parse Error

## Error Message
```
JSON parse error: SyntaxError: Unexpected end of JSON input
```

This error means the server returned **empty response** or **invalid JSON**.

---

## 🎯 Quick Fix Steps

### Step 1: Run Diagnostic Test

**Visit:** `http://localhost/payhere/test-direct-submit.php`

This will show you:
- ✅ Exact server response
- ✅ HTTP status code
- ✅ Response headers
- ✅ Response body
- ✅ MySQL connection status
- ✅ File existence check

**Look for:**
- Empty response body
- HTML instead of JSON
- PHP errors
- MySQL connection errors

---

### Step 2: Check Most Common Causes

#### Cause 1: MySQL Not Running (80% of cases)

**Fix:**
```
1. Open XAMPP Control Panel
2. Check if MySQL shows "Running" in green
3. If not, click "Start" next to MySQL
4. Wait for green "Running" status
5. Try form again
```

#### Cause 2: Database Not Imported

**Fix:**
```
1. Go to http://localhost/phpmyadmin
2. Look for "loan_system" database in left sidebar
3. If missing:
   - Click "Import" tab
   - Choose "database_setup.sql"
   - Click "Go"
4. Verify tables exist:
   - inquiries
   - loan_types
   - site_settings
   - contacts
```

#### Cause 3: PHP Error

**Check error logs:**
```
Windows: C:\xampp\apache\logs\error.log
Linux: /var/log/apache2/error.log
```

**Look for:**
- Fatal errors
- Class not found
- Database connection errors
- File not found errors

---

## 🔍 Detailed Diagnosis

### What Does "Empty JSON" Mean?

The server is returning **nothing** or **wrong content type**:

**Empty Response (0 bytes):**
```
Response text: 
Response length: 0
```

**HTML Instead of JSON:**
```
Response text: <!DOCTYPE html><html>...
```

**PHP Error:**
```
Response text: Fatal error: Class 'Database' not found...
```

---

## 💻 Using Test Pages

### Test Page 1: test-direct-submit.php

**What it does:**
- Makes actual POST request to submit-inquiry
- Shows exact server response
- Parses JSON if valid
- Shows detailed error if invalid

**How to use:**
1. Visit: `http://localhost/payhere/test-direct-submit.php`
2. Look at "Response Body" section
3. Check if response is empty or contains errors
4. Read the diagnostic results

### Test Page 2: test-form-submit.php

**What it does:**
- Tests database connection
- Tests direct insert
- Tests API endpoint
- Interactive form submission

**How to use:**
1. Visit: `http://localhost/payhere/test-form-submit.php`
2. Run all 3 tests
3. Check which test fails
4. Read specific error message

---

## ✅ Checklist

Go through this checklist:

- [ ] **MySQL is running** in XAMPP Control Panel (green "Running")
- [ ] **Apache is running** in XAMPP Control Panel (green "Running")
- [ ] **Database "loan_system" exists** in phpMyAdmin
- [ ] **Table "inquiries" exists** in loan_system database
- [ ] **test-direct-submit.php** shows valid JSON response
- [ ] **test-database.php** shows "Connected successfully"
- [ ] **Browser console** (F12) shows the response text
- [ ] **No PHP errors** in Apache error.log

---

## 🔧 Step-by-Step Fix

### Fix Procedure:

**1. Check Browser Console (F12)**
```javascript
// You should see:
Response text: {"success":true,"message":"..."}

// If you see:
Response text: 
// OR
Response text: <!DOCTYPE html>
// Then server has a problem
```

**2. Visit test-direct-submit.php**
```
Go to: http://localhost/payhere/test-direct-submit.php

Look at "Response Body" section:
- If empty → MySQL not running
- If HTML → PHP error or wrong route
- If JSON → Check the JSON content
```

**3. Check MySQL**
```
Open XAMPP Control Panel
Check MySQL status:
✅ Green "Running" = Good
❌ Red "Stopped" = Start it
```

**4. Verify Database**
```
Open phpMyAdmin: http://localhost/phpmyadmin
Check left sidebar for "loan_system"
If missing → Import database_setup.sql
If exists → Click it, check for "inquiries" table
```

**5. Test Database Connection**
```
Visit: http://localhost/payhere/test-database.php
Should show: ✅ Connected successfully
If shows error → Fix MySQL connection
```

**6. Check PHP Errors**
```
Look at: C:\xampp\apache\logs\error.log
Search for recent errors (today's date)
Fix any Fatal errors or Warnings
```

---

## 🎯 Common Scenarios

### Scenario 1: Empty Response

**Console shows:**
```
Response text: 
Response length: 0
```

**Cause:** MySQL not running or database error

**Fix:**
1. Start MySQL in XAMPP
2. Import database_setup.sql
3. Try again

---

### Scenario 2: HTML Response

**Console shows:**
```
Response text: <!DOCTYPE html><html>...
```

**Cause:** PHP error or page redirect

**Fix:**
1. Check Apache error.log for PHP errors
2. Verify route exists: `index.php/submit-inquiry`
3. Check .htaccess exists

---

### Scenario 3: PHP Error

**Console shows:**
```
Response text: Fatal error: Class 'Database' not found...
```

**Cause:** Missing file or class

**Fix:**
1. Check file exists: `application/config/database_helper.php`
2. Check file exists: `application/models/Inquiry_model.php`
3. Clear PHP cache (restart Apache)

---

### Scenario 4: Database Connection Error

**Console shows:**
```
Response text: {"success":false,"message":"Database error: Access denied"}
```

**Cause:** Wrong database credentials

**Fix:**
1. Edit `application/config/database.php`
2. Set password to '' (empty for XAMPP default)
3. Check database name is 'loan_system'

---

## 💡 Quick Tests

### Test 1: Can PHP run?
```
Create file: test.php
<?php echo "PHP works!"; ?>

Visit: http://localhost/payhere/test.php
Should show: PHP works!
```

### Test 2: Can connect to MySQL?
```
Visit: http://localhost/payhere/test-database.php
Should show: ✅ Connected successfully
```

### Test 3: Does route work?
```
Visit: http://localhost/payhere/index.php/submit-inquiry
Should show: JSON error (not 404)
{"success":false,"message":"Name is required"}
```

### Test 4: Is database imported?
```
Visit: http://localhost/phpmyadmin
Click: loan_system → inquiries
Should show: Table structure
```

---

## 🆘 Still Not Working?

### Collect This Information:

1. **Visit test-direct-submit.php** and copy:
   - HTTP Status Code
   - Response Body (full text)
   - MySQL Status result

2. **Check Browser Console** (F12) and copy:
   - "Response text:" line
   - "Response length:" line
   - Any red error messages

3. **Check Error Log:**
   - Open: `C:\xampp\apache\logs\error.log`
   - Copy last 20 lines

4. **Check XAMPP Status:**
   - Apache: Running / Stopped?
   - MySQL: Running / Stopped?
   - Port conflicts?

---

## ✅ Success Indicators

**Form submission is working when:**

1. **Browser Console shows:**
```javascript
Response text: {"success":true,"message":"Thank you!..."}
Response length: 123 (or any number > 0)
```

2. **test-direct-submit.php shows:**
```
✅ Valid JSON!
✅ SUCCESS! Inquiry saved with ID: 123
✅ MySQL: CONNECTED
✅ Table 'inquiries': EXISTS
```

3. **phpMyAdmin shows:**
```
New row in inquiries table with your data
```

4. **Admin Dashboard shows:**
```
Your new inquiry appears in the list
```

---

## 📊 Error Message Guide

| Error | Meaning | Fix |
|-------|---------|-----|
| "Empty response" | Server returned nothing | Start MySQL |
| "Unexpected end of JSON" | Incomplete/empty JSON | Check MySQL running |
| "HTML instead of JSON" | Wrong content type | Check PHP errors |
| "Network error" | Can't reach server | Start Apache |
| "404 Not Found" | Wrong URL | Check route config |
| "500 Internal Error" | PHP error | Check error.log |

---

## 🔄 Reset Everything

**If nothing works, reset:**

```bash
# 1. Stop XAMPP services
Stop Apache and MySQL

# 2. Delete database (backup first!)
Drop database loan_system in phpMyAdmin

# 3. Re-import
Import database_setup.sql

# 4. Restart XAMPP
Start Apache and MySQL

# 5. Clear browser cache
Ctrl + Shift + Delete

# 6. Test
Visit test-direct-submit.php
```

---

**Test URLs:**
- Direct Submit Test: `http://localhost/payhere/test-direct-submit.php`
- Form Test: `http://localhost/payhere/test-form-submit.php`
- Database Test: `http://localhost/payhere/test-database.php`
- Landing Page: `http://localhost/payhere/`

**Good luck! 🚀**
