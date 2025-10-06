# 🇲🇾 Malaysian Validation Guide

## Overview

Your loan application form now includes **comprehensive Malaysian-specific validation** for phone numbers, names, and loan amounts. This ensures data quality and compliance with Malaysian standards.

---

## ✅ Validation Features

### 1. **Malaysian Phone Number Validation**

**Accepts:**
- ✅ Mobile numbers: `012-345-6789`, `012-3456789`, `0123456789`
- ✅ With country code: `+6012-345-6789`, `+60123456789`, `60123456789`
- ✅ Landline: `03-1234-5678`, `03-12345678`
- ✅ All Malaysian mobile prefixes: 010, 011, 012, 013, 014, 015, 016, 017, 018, 019

**Rejects:**
- ❌ Invalid prefixes
- ❌ Wrong length
- ❌ Non-Malaysian numbers
- ❌ Random text

**Auto-formatting:**
- Input: `0123456789` → Output: `012-345-6789`
- Input: `60123456789` → Output: `+60 12-345-6789`

---

### 2. **Malaysian IC (MyKad) Validation**

**Format:** `YYMMDD-PB-###G`
- YY = Year (2 digits)
- MM = Month (01-12)
- DD = Day (01-31)
- PB = Place of Birth (state code)
- ### = Sequential number
- G = Gender indicator

**Valid State Codes:**
```
01 = Johor          09 = Perlis
02 = Kedah          10 = Selangor
03 = Kelantan       11 = Terengganu
04 = Melaka         12 = Sabah
05 = N. Sembilan    13 = Sarawak
06 = Pahang         14 = WP Kuala Lumpur
07 = Penang         15 = WP Labuan
08 = Perak          16 = WP Putrajaya
21-24 = Born outside Malaysia
```

**Example Valid ICs:**
- ✅ `901231-01-5678` (Born Dec 31, 1990 in Johor)
- ✅ `850615-14-1234` (Born Jun 15, 1985 in KL)
- ✅ `750920125678` (without dashes)

---

### 3. **Malaysian Name Validation**

**Accepts:**
- ✅ Common Malaysian names with `bin`, `binti`, `a/l`, `a/p`
- ✅ Arabic characters: `Ahmad bin Abdullah`
- ✅ Chinese names: `Tan Ah Kow`
- ✅ Indian names: `Rajesh a/l Subramaniam`
- ✅ Spaces, dots, slashes, hyphens

**Examples:**
- ✅ `Ahmad bin Abdullah`
- ✅ `Siti Nurhaliza binti Tarudin`
- ✅ `Lee Wei Ming`
- ✅ `Raj a/l Kumar`
- ✅ `Mary a/p Joseph`
- ✅ `Dr. Ahmad Hafiz bin Ismail`

**Rejects:**
- ❌ Special characters: `@#$%`
- ❌ Numbers in name
- ❌ Too short (< 3 characters)

---

### 4. **Malaysian Postcode Validation**

**Format:** 5 digits

**Examples:**
- ✅ `50450` (Kuala Lumpur)
- ✅ `10250` (Penang)
- ✅ `80100` (Johor Bahru)
- ✅ `88000` (Kota Kinabalu)

**Rejects:**
- ❌ Less than 5 digits
- ❌ More than 5 digits
- ❌ Non-numeric

---

### 5. **Loan Amount Validation by Type**

Each loan type has specific ranges:

| Loan Type | Minimum | Maximum |
|-----------|---------|---------|
| **Personal Loan** | RM 1,000 | RM 200,000 |
| **Business Loan** | RM 10,000 | RM 1,000,000 |
| **Home Loan** | RM 50,000 | RM 2,000,000 |
| **Car Loan** | RM 20,000 | RM 500,000 |

**Examples:**
- ✅ Personal Loan: RM 50,000 (valid)
- ❌ Personal Loan: RM 500,000 (exceeds maximum)
- ✅ Business Loan: RM 500,000 (valid)
- ❌ Car Loan: RM 5,000 (below minimum)

---

### 6. **Income vs Loan Amount Check**

**Debt-to-Income Ratio:**
- Loan should not exceed 10 years of monthly income
- Formula: `Loan Amount ≤ Monthly Income × 120 months`

**Examples:**
- ✅ Monthly Income: RM 5,000, Loan: RM 50,000 (valid)
- ⚠️ Monthly Income: RM 3,000, Loan: RM 500,000 (warning shown)

---

## 🎯 How It Works

### Client-Side Validation (JavaScript)

**Real-time validation as you type:**

1. **Phone Number Field:**
   - Type: `0123456789`
   - On blur: Validates format
   - Auto-formats to: `012-345-6789`
   - Shows error if invalid

2. **IC Number Field** (if added)
   - Type: `901231015678`
   - On blur: Validates IC format
   - Shows error if invalid

3. **Form Submit:**
   - Validates all fields before submission
   - Shows inline errors for each field
   - Prevents submission if validation fails

### Server-Side Validation (PHP)

**Double-check on server:**

1. **Basic Validation:**
   - Required fields check
   - Email format
   - Numeric values
   - Min/max lengths

2. **Malaysian-Specific:**
   - Phone number format
   - Name characters
   - Loan amount ranges

3. **Data Formatting:**
   - Auto-format phone: `012-345-6789`
   - Proper case name: `Ahmad Bin Abdullah`
   - Lowercase email: `user@email.com`

---

## 📝 Validation Messages

### Phone Number Messages

**Invalid Format:**
```
"Please enter a valid Malaysian phone number 
(e.g., 012-345-6789 or +6012-345-6789)"
```

**Accepted Formats:**
```
Format: 012-345-6789 or +6012-345-6789
```

### IC Number Messages

**Invalid IC:**
```
"Please enter a valid Malaysian IC number 
(e.g., 901231-01-5678)"
```

### Loan Amount Messages

**Below Minimum:**
```
"Minimum loan amount for this type is RM 10,000"
```

**Above Maximum:**
```
"Maximum loan amount for this type is RM 200,000"
```

**Income Warning:**
```
"Warning: The loan amount seems high compared to your income. 
Our team will review your application."
```

---

## 🧪 Testing Examples

### Test Case 1: Valid Phone Numbers

**Test these formats:**
```javascript
012-345-6789     ✅ Valid
0123456789       ✅ Valid (auto-formats)
+6012-345-6789   ✅ Valid
60123456789      ✅ Valid
012-3456-789     ✅ Valid
011-1234-5678    ✅ Valid
03-1234-5678     ✅ Valid (landline)
```

### Test Case 2: Invalid Phone Numbers

**These should fail:**
```javascript
123456789        ❌ No leading 0
021-234-5678     ❌ Invalid prefix (021)
0123            ❌ Too short
+6523456789      ❌ Wrong country code
abcd1234         ❌ Contains letters
```

### Test Case 3: Valid IC Numbers

**Test these:**
```javascript
901231-01-5678   ✅ Valid
901231015678     ✅ Valid (auto-formats)
850615-14-1234   ✅ Valid
750920-12-5678   ✅ Valid
```

### Test Case 4: Invalid IC Numbers

**These should fail:**
```javascript
991350-01-5678   ❌ Invalid month (13)
901232-99-5678   ❌ Invalid state code (99)
90123101567      ❌ Only 11 digits
abcd12-01-5678   ❌ Contains letters
```

### Test Case 5: Loan Amounts

**Personal Loan Tests:**
```javascript
RM 500           ❌ Below minimum (RM 1,000)
RM 50,000        ✅ Valid
RM 200,000       ✅ Valid (at maximum)
RM 300,000       ❌ Above maximum
```

**Business Loan Tests:**
```javascript
RM 5,000         ❌ Below minimum (RM 10,000)
RM 500,000       ✅ Valid
RM 1,000,000     ✅ Valid (at maximum)
RM 2,000,000     ❌ Above maximum
```

---

## 🎨 Visual Feedback

### Error States

**Invalid Field:**
- 🔴 Red border around input
- ❌ Error message below field
- Auto-focus on field

**Valid Field:**
- ⚪ Normal border
- ✅ No error message
- Auto-format applied

### Example Error Display

```
┌─────────────────────────────────┐
│ Phone Number (Malaysian) *      │
│ ┌─────────────────────────────┐ │
│ │ 123456789                 │◄├─ Red border
│ └─────────────────────────────┘ │
│ ❌ Please enter a valid          │◄── Error message
│    Malaysian phone number        │
└─────────────────────────────────┘
```

---

## 💻 Code Examples

### Using Validation in JavaScript

```javascript
// Validate phone number
if (!validateMalaysianPhone('0123456789')) {
    showFieldError('phone', 'Invalid phone number');
}

// Validate IC
if (!validateMalaysianIC('901231-01-5678')) {
    showFieldError('ic_number', 'Invalid IC number');
}

// Format phone
const formatted = formatMalaysianPhone('0123456789');
// Result: "012-345-6789"
```

### Using Validation in PHP

```php
// Validate phone
if (!validate_malaysian_phone('0123456789')) {
    return 'Invalid phone number';
}

// Validate IC
if (!validate_malaysian_ic('901231-01-5678')) {
    return 'Invalid IC number';
}

// Format phone
$formatted = format_malaysian_phone('0123456789');
// Result: "012-345-6789"

// Validate loan amount
$validation = validate_loan_amount_range(50000, 'personal');
if (!$validation['valid']) {
    echo $validation['message'];
}
```

---

## 📋 Validation Checklist

**Before form submission, check:**

- [ ] Phone number is Malaysian format
- [ ] IC number is valid (if field exists)
- [ ] Name contains only valid characters
- [ ] Email is valid format
- [ ] Loan amount is within range for loan type
- [ ] Monthly income is reasonable (if provided)
- [ ] All required fields are filled
- [ ] Terms checkbox is checked

---

## 🔧 Customization

### Add New Validation

**1. Add to JavaScript** (`public/js/script.js`):
```javascript
function validateCustomField(value) {
    // Your validation logic
    return true/false;
}
```

**2. Add to PHP Helper** (`application/helpers/malaysian_validation_helper.php`):
```php
function validate_custom_field($value) {
    // Your validation logic
    return true/false;
}
```

**3. Add to Controller** (`application/controllers/Home.php`):
```php
if (!validate_custom_field($value)) {
    echo json_encode([
        'success' => false,
        'message' => 'Validation error message'
    ]);
    return;
}
```

### Modify Loan Ranges

**Edit** `application/helpers/malaysian_validation_helper.php`:

```php
$ranges = [
    'personal' => ['min' => 5000, 'max' => 300000],  // New ranges
    // ... other types
];
```

---

## 📊 Supported Formats Summary

| Field | Format | Example |
|-------|--------|---------|
| **Phone** | 01X-XXXX-XXXX | 012-345-6789 |
| **Phone (Alt)** | +60X-XXXX-XXXX | +6012-345-6789 |
| **IC** | YYMMDD-PB-###G | 901231-01-5678 |
| **Postcode** | XXXXX | 50450 |
| **Name** | Letters, spaces | Ahmad bin Abdullah |
| **Loan Amount** | RM XXX,XXX | RM 50,000 |

---

## ✅ Benefits

**For Users:**
- ✨ Instant feedback on errors
- 🔄 Auto-formatting of phone numbers
- 📝 Clear error messages
- 🎯 Prevents invalid submissions

**For Admins:**
- 📊 Clean, consistent data
- 📞 Properly formatted phone numbers
- ✅ Validated Malaysian ICs
- 💾 Database integrity

**For Business:**
- 🚫 Reduces invalid applications
- 📈 Better data quality
- ⚡ Faster processing
- 💯 Malaysian compliance

---

## 🆘 Troubleshooting

**Validation not working?**
1. Check browser console (F12) for errors
2. Clear browser cache (Ctrl+F5)
3. Verify JavaScript file is loaded
4. Test with known valid values

**Phone number keeps showing error?**
1. Make sure it starts with 0 or +60
2. Use mobile prefixes: 010-019
3. Remove extra spaces/characters
4. Try format: 012-345-6789

**IC validation failing?**
1. Check date is valid (YYMMDD)
2. Verify state code (01-16, 21-24)
3. Must be exactly 12 digits
4. Try format: 901231-01-5678

---

**All Malaysian validations are now active! 🇲🇾**

**Files Created:**
- `application/helpers/malaysian_validation_helper.php`
- Updated: `public/js/script.js`
- Updated: `application/controllers/Home.php`
- Updated: `application/views/home/index.php`
