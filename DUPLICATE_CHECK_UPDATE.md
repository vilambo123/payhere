# 🔍 Duplicate Application Check - Updated

## What Changed

### Previous Behavior ❌
Only checked for duplicates using:
- ✅ Email
- ✅ IC Number

### New Behavior ✅
Now checks for duplicates using:
- ✅ Email
- ✅ IC Number
- ✅ Phone Number

## How It Works

### Duplicate Detection Logic

When a user submits a loan application, the system checks if there's already a **pending** application with:

```
Same Email  OR  Same IC Number  OR  Same Phone Number
```

If **any** match is found → Application is rejected with a friendly message.

---

## Example Scenarios

### Scenario 1: Email Match
```
Existing Application (Pending):
- Email: john@example.com
- IC: 901234-12-3456
- Phone: +60 12-345-6789

New Submission:
- Email: john@example.com  ← MATCH!
- IC: 991234-12-9999
- Phone: +60 19-888-7777

Result: ❌ Rejected
Message: "We have received your application (matching email address) 
         submitted on 01 Oct 2025..."
```

### Scenario 2: IC Number Match
```
Existing Application (Pending):
- Email: john@example.com
- IC: 901234-12-3456
- Phone: +60 12-345-6789

New Submission:
- Email: different@example.com
- IC: 901234-12-3456  ← MATCH!
- Phone: +60 19-888-7777

Result: ❌ Rejected
Message: "We have received your application (matching IC number) 
         submitted on 01 Oct 2025..."
```

### Scenario 3: Phone Number Match
```
Existing Application (Pending):
- Email: john@example.com
- IC: 901234-12-3456
- Phone: +60 12-345-6789

New Submission:
- Email: different@example.com
- IC: 991234-12-9999
- Phone: +60 12-345-6789  ← MATCH!

Result: ❌ Rejected
Message: "We have received your application (matching phone number) 
         submitted on 01 Oct 2025..."
```

### Scenario 4: No Match - All Different
```
Existing Application (Pending):
- Email: john@example.com
- IC: 901234-12-3456
- Phone: +60 12-345-6789

New Submission:
- Email: different@example.com
- IC: 991234-12-9999
- Phone: +60 19-888-7777

Result: ✅ Accepted
Message: "Thank you! We have received your application..."
```

---

## Important Notes

### 1. Only Checks PENDING Applications
- ✅ Status = "pending" → Blocks duplicate
- ✅ Status = "contacted" → Allows new application
- ✅ Status = "approved" → Allows new application
- ✅ Status = "rejected" → Allows new application

**Why?** Users can apply again after their previous application is processed.

### 2. Phone Number Formatting
The system automatically formats phone numbers for consistent matching:

```
User Input:        System Formats To:
012-345-6789   →   +60 12-345-6789
0123456789     →   +60 12-345-6789
+60123456789   →   +60 12-345-6789
+6012-345-6789 →   +60 12-345-6789
```

This ensures matches even if users enter phone numbers differently.

### 3. IC Number Formatting
IC numbers are also formatted:

```
User Input:        System Formats To:
901234123456   →   901234-12-3456
901234-123456  →   901234-12-3456
901234-12-3456 →   901234-12-3456
```

### 4. Email Comparison
Emails are compared in lowercase:

```
john@example.com  =  JOHN@EXAMPLE.COM  =  John@Example.Com
```

---

## User Experience

### When Duplicate is Found

**Error Message Displayed:**
```
⚠️ We have received your application (matching email address) 
   submitted on 03 Oct 2025. Our team is currently reviewing 
   your request and will contact you within 2-3 business days. 
   We appreciate your patience. 
   
   If you have any urgent queries, please call us at 
   +60 3-1234 5678.
```

**What User Sees:**
- Clear explanation
- Which field matched (email/IC/phone)
- Original submission date
- Expected response time
- Contact number for urgent queries

---

## API Response

### Success Response (No Duplicate)
```json
{
    "success": true,
    "message": "Thank you! We have received your application...",
    "inquiry_id": 123
}
```

### Error Response (Duplicate Found)
```json
{
    "success": false,
    "message": "We have received your application (matching phone number) submitted on 03 Oct 2025...",
    "pending_application_id": 45,
    "submission_date": "03 Oct 2025",
    "matched_field": "phone number"
}
```

---

## Code Changes

### File 1: `application/models/Inquiry_model.php`

**Method:** `check_pending_application()`

**Before:**
```php
public function check_pending_application($email, $ic_number = null)
```

**After:**
```php
public function check_pending_application($email, $ic_number = null, $phone = null)
```

**SQL Query:**
```sql
SELECT * FROM inquiries 
WHERE status = 'pending' 
AND (
    email = 'john@example.com' 
    OR ic_number = '901234-12-3456' 
    OR phone = '+60 12-345-6789'
)
ORDER BY created_at DESC 
LIMIT 1
```

### File 2: `application/controllers/Home.php`

**Method:** `submit_inquiry()`

**Updated Call:**
```php
// Before
$pending = $inquiry_model->check_pending_application($email, $ic_number);

// After
$formatted_phone = format_malaysian_phone($phone);
$pending = $inquiry_model->check_pending_application($email, $ic_number, $formatted_phone);
```

**Added Field Detection:**
```php
// Determine which field matched
$matched_field = '';
if ($pending['email'] == $email) {
    $matched_field = 'email address';
} elseif ($pending['ic_number'] == format_malaysian_ic($ic_number)) {
    $matched_field = 'IC number';
} elseif ($pending['phone'] == $formatted_phone) {
    $matched_field = 'phone number';
}
```

---

## Testing Guide

### Test 1: Same Email
```
1. Submit application with:
   - Email: test@example.com
   - IC: 901234-12-3456
   - Phone: 012-345-6789

2. Submit another application with:
   - Email: test@example.com (same)
   - IC: 991234-12-9999 (different)
   - Phone: 019-888-7777 (different)

Expected: ❌ Rejected (matching email address)
```

### Test 2: Same IC Number
```
1. Submit application with:
   - Email: first@example.com
   - IC: 901234-12-3456
   - Phone: 012-345-6789

2. Submit another application with:
   - Email: second@example.com (different)
   - IC: 901234-12-3456 (same)
   - Phone: 019-888-7777 (different)

Expected: ❌ Rejected (matching IC number)
```

### Test 3: Same Phone Number
```
1. Submit application with:
   - Email: first@example.com
   - IC: 901234-12-3456
   - Phone: 012-345-6789

2. Submit another application with:
   - Email: second@example.com (different)
   - IC: 991234-12-9999 (different)
   - Phone: 012-345-6789 (same)

Expected: ❌ Rejected (matching phone number)
```

### Test 4: Different Phone Format (Should Still Match)
```
1. Submit application with:
   - Phone: 012-345-6789

2. Submit another with:
   - Phone: 0123456789 (no dashes)

Expected: ❌ Rejected (system formats both to +60 12-345-6789)
```

### Test 5: All Different
```
Submit application with completely different:
- Email
- IC Number
- Phone

Expected: ✅ Accepted
```

### Test 6: Approved Status (Should Allow New)
```
1. Submit application (gets approved)
2. Admin changes status to "approved"
3. Submit new application with same email/IC/phone

Expected: ✅ Accepted (previous is no longer pending)
```

---

## Database Query Example

**Checking for duplicates:**
```sql
SELECT * FROM inquiries 
WHERE status = 'pending' 
AND (
    email = 'test@example.com' 
    OR ic_number = '901234-12-3456' 
    OR phone = '+60 12-345-6789'
)
ORDER BY created_at DESC 
LIMIT 1;
```

**If this returns a row** → Duplicate found → Reject  
**If this returns nothing** → No duplicate → Accept  

---

## Summary

### ✅ What's Protected

| Field | Checked | Format | Case Sensitive |
|-------|---------|--------|----------------|
| Email | ✅ Yes | Lowercase | No |
| IC Number | ✅ Yes | 901234-12-3456 | No |
| Phone | ✅ Yes | +60 12-345-6789 | No |

### ✅ Benefits

1. **Prevents Duplicate Submissions**
   - Users can't spam applications
   - Reduces admin workload

2. **Better User Experience**
   - Clear message about existing application
   - Shows submission date
   - Provides contact info

3. **Flexible Matching**
   - Catches duplicates even with format variations
   - Smart formatting ensures consistency

4. **Status-Aware**
   - Only blocks PENDING applications
   - Allows reapplication after processing

---

## Quick Test

```bash
# Test in browser
1. Go to: http://localhost/payhere/
2. Submit an application
3. Try submitting again with:
   - Same email (different IC/phone)
   - Same IC (different email/phone)
   - Same phone (different email/IC)

All should be rejected with appropriate messages!
```

---

**Duplicate checking now covers Email, IC Number, AND Phone Number! 🛡️**
