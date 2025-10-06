# 🎯 Dynamic Database Settings Guide

## Overview

Your loan landing page now **dynamically loads ALL data from the database**! This means you can change your company name, contact info, social media links, and loan products **directly in the database or through the Settings page** - no code changes needed!

---

## ✅ What's Dynamic Now

### 1. **Site Settings** (from `site_settings` table)
- ✅ Company Name (shown in header, footer, page title)
- ✅ Email Address (shown in footer)
- ✅ Phone Number (shown in footer)
- ✅ Physical Address (shown in footer)
- ✅ Social Media Links (Facebook, Twitter, Instagram, LinkedIn)

### 2. **Loan Types** (from `loan_types` table)
- ✅ Service cards on landing page (auto-generated)
- ✅ Loan dropdown in application form (auto-populated)
- ✅ Interest rates and amounts (pulled from database)
- ✅ Loan descriptions and features (from database)
- ✅ Active/inactive status (control visibility)

---

## 🎨 How To Update Settings

### Method 1: Settings Management Page (EASIEST)

**Access:** `http://localhost/payhere/index.php/settings`

**Features:**
- 📝 Edit company information (name, email, phone, address)
- 🔗 Update social media links
- 💰 Modify loan types (amounts, rates, descriptions)
- ✅ Enable/disable loan products
- 💾 Save changes with one click

**Steps:**
1. Go to `http://localhost/payhere/index.php/settings`
2. Click on the tab you want to edit:
   - **Site Settings** - Company info & social media
   - **Loan Types** - Loan products configuration
3. Make your changes
4. Click "Save" button
5. Refresh landing page to see changes!

---

### Method 2: phpMyAdmin (Direct Database)

**Access:** `http://localhost/phpmyadmin`

#### Edit Site Settings:
1. Open `loan_system` database
2. Click on `site_settings` table
3. Click "Browse" to see all settings
4. Click "Edit" (pencil icon) next to the setting you want to change
5. Update the `setting_value` field
6. Click "Go" to save

**Example: Change Company Name**
```sql
UPDATE site_settings 
SET setting_value = 'MyLoanCompany' 
WHERE setting_key = 'site_name';
```

#### Edit Loan Types:
1. Open `loan_system` database
2. Click on `loan_types` table
3. Click "Edit" next to the loan type
4. Modify values (amounts, rates, description)
5. Click "Go" to save

**Example: Update Personal Loan Max Amount**
```sql
UPDATE loan_types 
SET max_amount = 300000 
WHERE type_name = 'personal';
```

---

## 📊 Database Tables Reference

### `site_settings` Table

| setting_key | setting_value | Description |
|-------------|---------------|-------------|
| `site_name` | QuickLoan | Company name (header, footer) |
| `site_email` | info@quickloan.com | Contact email |
| `site_phone` | +60 3-1234 5678 | Contact phone |
| `site_address` | Kuala Lumpur, Malaysia | Business address |
| `social_facebook` | https://facebook.com/... | Facebook URL |
| `social_twitter` | https://twitter.com/... | Twitter URL |
| `social_instagram` | https://instagram.com/... | Instagram URL |
| `social_linkedin` | https://linkedin.com/... | LinkedIn URL |

### `loan_types` Table

| Column | Description | Example |
|--------|-------------|---------|
| `type_name` | Internal name | personal, business, home, car |
| `display_name` | Show on website | "Personal Loan" |
| `min_amount` | Minimum loan amount | 5000 |
| `max_amount` | Maximum loan amount | 200000 |
| `min_interest_rate` | Interest rate % | 3.5 |
| `max_tenure_years` | Max years to repay | 10 |
| `description` | Loan description | "Quick cash for..." |
| `is_active` | Show on website? | 1 = Yes, 0 = No |

---

## 🎬 Examples

### Example 1: Change Company Name

**Before:** QuickLoan  
**After:** FastCash Solutions

**Method A - Settings Page:**
1. Go to `http://localhost/payhere/index.php/settings`
2. Change "Company Name" field to "FastCash Solutions"
3. Click "Save All Settings"
4. ✅ Done! Name updated everywhere

**Method B - phpMyAdmin:**
```sql
UPDATE site_settings 
SET setting_value = 'FastCash Solutions' 
WHERE setting_key = 'site_name';
```

---

### Example 2: Add Social Media Links

**Settings Page:**
1. Go to Settings → Site Settings tab
2. Fill in social media URLs:
   - Facebook: https://facebook.com/yourcompany
   - Twitter: https://twitter.com/yourcompany
   - Instagram: https://instagram.com/yourcompany
   - LinkedIn: https://linkedin.com/company/yourcompany
3. Click "Save All Settings"
4. ✅ Links now work in footer!

---

### Example 3: Update Loan Amounts

**Increase Personal Loan to RM 500,000:**

**Settings Page:**
1. Go to Settings → Loan Types tab
2. Find "Personal Loan" section
3. Change "Max Amount" to 500000
4. Click "Update personal Loan"
5. ✅ Updated on landing page!

**phpMyAdmin:**
```sql
UPDATE loan_types 
SET max_amount = 500000 
WHERE type_name = 'personal';
```

---

### Example 4: Disable a Loan Type

**Hide Car Loan temporarily:**

**Settings Page:**
1. Go to Settings → Loan Types tab
2. Find "Car Loan" section
3. Uncheck "Active (Show on website)"
4. Click "Update car Loan"
5. ✅ Car Loan no longer visible!

**phpMyAdmin:**
```sql
UPDATE loan_types 
SET is_active = 0 
WHERE type_name = 'car';
```

---

### Example 5: Add New Setting

**Add business hours to settings:**

**phpMyAdmin:**
```sql
INSERT INTO site_settings (setting_key, setting_value, setting_type, description) 
VALUES ('business_hours', 'Mon-Fri: 9AM - 6PM', 'text', 'Business operating hours');
```

Then edit your view files to display it!

---

## 🔄 Where Settings Appear

### Site Name (`site_name`)
- ✅ Browser tab title
- ✅ Navigation bar logo
- ✅ Footer company name
- ✅ Page title (meta)

### Contact Info (`site_email`, `site_phone`, `site_address`)
- ✅ Footer contact section
- ✅ Can be used in forms (future)

### Social Media Links
- ✅ Footer social icons (clickable links)
- ✅ Opens in new tab

### Loan Types
- ✅ Services section cards (4 cards)
- ✅ Application form dropdown
- ✅ Interest rates shown
- ✅ Max amounts displayed
- ✅ Tenure years listed

---

## 🚀 Access Points

| Page | URL | Purpose |
|------|-----|---------|
| **Landing Page** | `http://localhost/payhere/` | See your changes live |
| **Settings Manager** | `http://localhost/payhere/index.php/settings` | Edit all settings |
| **Admin Dashboard** | `http://localhost/payhere/index.php/admin` | View inquiries |
| **phpMyAdmin** | `http://localhost/phpmyadmin` | Direct database access |

---

## 💡 Tips & Best Practices

### ✅ DO:
- Test changes on landing page after saving
- Keep social media URLs complete (include https://)
- Use realistic loan amounts and rates
- Update descriptions to match your brand
- Keep phone numbers in consistent format

### ❌ DON'T:
- Don't use special characters in `type_name` (use letters only)
- Don't delete default settings (edit them instead)
- Don't set unrealistic interest rates
- Don't leave social URLs as "#" (put real URLs or remove)
- Don't forget to set `is_active = 1` for visible loans

---

## 🔧 Troubleshooting

### Changes not showing?
**Solution:** 
1. Clear browser cache (Ctrl+F5)
2. Check if you saved the changes
3. Verify in phpMyAdmin that value updated

### Settings page not loading?
**Solution:**
1. Check MySQL is running in XAMPP
2. Visit `test-database.php` to check connection
3. Check if database tables exist

### Loan not showing on landing page?
**Solution:**
1. Check `is_active = 1` in `loan_types` table
2. Verify loan has all required fields filled
3. Clear browser cache

### Social links not working?
**Solution:**
1. Make sure URLs start with `http://` or `https://`
2. Test the URL in browser directly
3. Check `site_settings` table has the values

---

## 📝 Adding New Settings

Want to add more settings? Easy!

**1. Add to Database:**
```sql
INSERT INTO site_settings (setting_key, setting_value, setting_type, description) 
VALUES ('your_setting_key', 'Your Value', 'text', 'Description here');
```

**2. Add to Settings Page:**
Edit `application/views/admin/settings.php` and add input field

**3. Use in Views:**
```php
<?php echo $all_settings['your_setting_key']; ?>
```

---

## 🎨 Customization Examples

### Change Entire Brand:
```sql
-- Update company name
UPDATE site_settings SET setting_value = 'MoneyMate' WHERE setting_key = 'site_name';

-- Update contact email
UPDATE site_settings SET setting_value = 'help@moneymate.com' WHERE setting_key = 'site_email';

-- Update phone
UPDATE site_settings SET setting_value = '+60 12-345 6789' WHERE setting_key = 'site_phone';

-- Update all social links
UPDATE site_settings SET setting_value = 'https://facebook.com/moneymate' WHERE setting_key = 'social_facebook';
UPDATE site_settings SET setting_value = 'https://twitter.com/moneymate' WHERE setting_key = 'social_twitter';
```

### Adjust All Loan Rates:
```sql
-- Increase all interest rates by 0.5%
UPDATE loan_types SET min_interest_rate = min_interest_rate + 0.5;

-- Double all max amounts
UPDATE loan_types SET max_amount = max_amount * 2;
```

---

## ✨ Summary

**Your landing page is now fully dynamic!**

- ✅ No code changes needed for content updates
- ✅ Easy Settings page for non-technical users
- ✅ phpMyAdmin access for advanced users
- ✅ All changes reflect immediately
- ✅ Professional and maintainable

**Just update the database and see the magic happen! 🎉**

---

**Quick Links:**
- Settings Page: `http://localhost/payhere/index.php/settings`
- phpMyAdmin: `http://localhost/phpmyadmin`
- Landing Page: `http://localhost/payhere/`

---

**Version:** 1.0.0  
**Last Updated:** 2025-10-03
