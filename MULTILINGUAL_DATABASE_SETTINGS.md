# 🌍 Multilingual Database Settings Guide

## Overview

The PayHere Loan application uses a **hybrid approach** for multilingual support:

- **Static Content**: Translated via language files (`application/language/*.php`)
- **Database Settings**: Contact info (phone, email, address) remains the same across languages
- **Display Names**: Site name and descriptions are translated in language files

---

## 🎯 How It Works

### **What Translates:**

✅ **From Language Files:**
- Site name display
- Site description/tagline
- All UI text (navigation, buttons, labels)
- Form fields and validation messages
- Content sections (hero, features, testimonials, etc.)

✅ **From Database (No Translation Needed):**
- Phone number: `+60 3-1234 5678` (same in all languages)
- Email: `info@quickloan.com` (same in all languages)
- Address: `Kuala Lumpur, Malaysia` (same in all languages)
- Social media URLs (same in all languages)

---

## 📊 Current Implementation

### **Site Name:**

**Database Value:** `QuickLoan` (in `site_settings` table)

**Displayed As:**
- 🇬🇧 English: `QuickLoan`
- 🇲🇾 Malay: `QuickLoan`
- 🇨🇳 Chinese: `QuickLoan`

*Note: Brand names typically stay the same across languages*

### **Site Description:**

**Displayed As:**
- 🇬🇧 English: *"We are a leading financial services provider in Malaysia..."*
- 🇲🇾 Malay: *"Kami adalah penyedia perkhidmatan kewangan terkemuka di Malaysia..."*
- 🇨🇳 Chinese: *"我们是马来西亚领先的金融服务提供商..."*

### **Contact Information:**

**Same in All Languages:**
```
Phone: +60 3-1234 5678
Email: info@quickloan.com
Address: Kuala Lumpur, Malaysia
```

---

## 🔧 Architecture

### **Language Files:**
```
application/language/
├── en.php   # English translations
├── my.php   # Malay translations
└── zh.php   # Chinese translations
```

### **Database Settings:**
```
site_settings table:
├── site_name → QuickLoan
├── site_email → info@quickloan.com
├── site_phone → +60 3-1234 5678
└── site_address → Kuala Lumpur, Malaysia
```

### **Display Logic:**
```php
// Site name - uses translation
<?php _e('site_name'); ?>

// Contact info - uses database
<?php echo $site['phone']; ?>
<?php echo $site['email']; ?>
<?php echo $site['address']; ?>
```

---

## 📝 Example: Footer Section

**Code:**
```php
<div class="footer-section">
    <h3><?php _e('site_name'); ?></h3>
    <p><?php _e('site_description'); ?></p>
</div>

<div class="footer-section">
    <h4><?php _e('footer_contact'); ?></h4>
    <ul>
        <li><?php echo $site['phone']; ?></li>
        <li><?php echo $site['email']; ?></li>
        <li><?php echo $site['address']; ?></li>
    </ul>
</div>
```

**Output in English:**
```
QuickLoan
We are a leading financial services provider...

Contact Us
+60 3-1234 5678
info@quickloan.com
Kuala Lumpur, Malaysia
```

**Output in Malay:**
```
QuickLoan
Kami adalah penyedia perkhidmatan kewangan terkemuka...

Hubungi Kami
+60 3-1234 5678
info@quickloan.com
Kuala Lumpur, Malaysia
```

**Output in Chinese:**
```
QuickLoan
我们是马来西亚领先的金融服务提供商...

联系我们
+60 3-1234 5678
info@quickloan.com
Kuala Lumpur, Malaysia
```

---

## 🚀 Advanced: Full Database Translation (Optional)

If you want **contact info to also translate**, here's how:

### **Option 1: Separate Language Columns**

Modify `site_settings` table:
```sql
ALTER TABLE site_settings
ADD COLUMN setting_value_my VARCHAR(500),
ADD COLUMN setting_value_zh VARCHAR(500);

-- Update with translations
UPDATE site_settings 
SET setting_value_my = 'Kuala Lumpur, Malaysia',
    setting_value_zh = '吉隆坡，马来西亚'
WHERE setting_key = 'site_address';
```

### **Option 2: JSON Translation**

Store translations in JSON:
```sql
ALTER TABLE site_settings
ADD COLUMN setting_translations JSON;

-- Update with translations
UPDATE site_settings
SET setting_translations = JSON_OBJECT(
    'en', 'Kuala Lumpur, Malaysia',
    'my', 'Kuala Lumpur, Malaysia',
    'zh', '吉隆坡，马来西亚'
)
WHERE setting_key = 'site_address';
```

### **Option 3: Separate Translation Table**

Create a new table:
```sql
CREATE TABLE site_settings_i18n (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100),
    language_code VARCHAR(5),
    translated_value VARCHAR(500),
    FOREIGN KEY (setting_key) REFERENCES site_settings(setting_key)
);

INSERT INTO site_settings_i18n VALUES
(NULL, 'site_address', 'en', 'Kuala Lumpur, Malaysia'),
(NULL, 'site_address', 'my', 'Kuala Lumpur, Malaysia'),
(NULL, 'site_address', 'zh', '吉隆坡，马来西亚');
```

---

## 💡 Best Practices

### **What Should Be Translated:**

✅ **Always Translate:**
- UI text (buttons, labels, messages)
- Content descriptions
- Marketing copy
- Error/success messages

✅ **Usually Keep Same:**
- Brand/company names
- Phone numbers
- Email addresses
- Product SKUs/codes

✅ **Sometimes Translate:**
- Street addresses (if localized)
- Business hours (if format differs)
- Currency symbols

### **Current Setup Recommendations:**

For **QuickLoan**, the current implementation is optimal:

1. **Site Name**: ✅ Keep "QuickLoan" (it's a brand)
2. **Phone**: ✅ Keep same (it's a number)
3. **Email**: ✅ Keep same (it's an address)
4. **Physical Address**: ✅ Keep same (or translate if you want)
5. **Descriptions**: ✅ Translate (marketing content)

---

## 🔄 Updating Settings

### **To Change Contact Info:**

**Via phpMyAdmin:**
```sql
-- Update phone number (affects all languages)
UPDATE site_settings
SET setting_value = '+60 3-9999 8888'
WHERE setting_key = 'site_phone';

-- Update email (affects all languages)
UPDATE site_settings
SET setting_value = 'support@quickloan.com'
WHERE setting_key = 'site_email';
```

**Via Admin Panel:** (Future enhancement)
- Go to `/settings`
- Update contact fields
- Saves to database

### **To Change Translated Content:**

**Edit Language Files:**

```php
// application/language/en.php
'site_description' => 'Your new description in English',

// application/language/my.php
'site_description' => 'Penerangan baharu anda dalam Bahasa Melayu',

// application/language/zh.php
'site_description' => '您的新中文描述',
```

---

## 🎯 Summary

### **Hybrid Translation Model:**

| Content Type | Source | Translates? | Example |
|--------------|--------|-------------|---------|
| Brand Name | Translation File | No (brand) | QuickLoan |
| Site Description | Translation File | Yes | "We are leading..." |
| Phone Number | Database | No (number) | +60 3-1234 5678 |
| Email | Database | No (address) | info@quickloan.com |
| Address | Database | Optional | Kuala Lumpur |
| UI Text | Translation File | Yes | "Apply Now" |
| Form Labels | Translation File | Yes | "Full Name" |
| Messages | Translation File | Yes | "Thank you!" |

---

## 🔍 Debugging

**Check What's Being Used:**

```php
// In any view file
echo "Site Name: " . lang('site_name') . "<br>";
echo "DB Phone: " . $site['phone'] . "<br>";
echo "Current Lang: " . get_current_language() . "<br>";
```

**Test Language Switching:**

1. Switch to Malay
2. Check footer - description should be in Malay
3. Contact info (phone/email) stays same
4. Switch to Chinese - same pattern

---

## ✅ Current Status

**Fully Implemented:**
- ✅ Site name translation (brand)
- ✅ Site description translation
- ✅ All UI text translation
- ✅ Database contact info (non-translated)
- ✅ Hybrid model working correctly

**Works As Designed:**
- Site name, phone, email, address from database
- Display names and descriptions from translations
- Best of both worlds! 🎉

---

**Your multilingual site is ready! 🌍**

Contact information stays consistent across languages (as it should), while all user-facing content translates perfectly!
