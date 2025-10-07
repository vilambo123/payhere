# 🌍 Multi-Language Support Guide

## Overview

The PayHere Loan application now supports **3 languages**:
- **EN** **English** (EN)
- 🇲🇾 **Bahasa Melayu** (MY)
- 🇨🇳 **简体中文** (ZH)

Users can switch languages seamlessly, and their preference is saved in sessions and cookies.

---

## 🎯 Features

### ✅ What's Implemented:

1. **Language Selector** in navigation bar
2. **Automatic Language Detection** from session/cookie
3. **Persistent Language Preference** (saved for 30 days)
4. **Dynamic Content Translation** via PHP
5. **JavaScript Translation Support** for dynamic messages
6. **Clean URL-based Language Switching**
7. **Smooth Page Reload** on language change

---

## 📁 File Structure

```
application/
├── language/
│   ├── en.php          # English translations
│   ├── my.php          # Malay translations
│   └── zh.php          # Chinese translations
├── helpers/
│   └── language_helper.php  # Language utility functions
└── controllers/
    └── Language.php    # Language switching controller
```

---

## 🎨 How It Works

### 1. Language Selection Flow

```
User clicks language → AJAX call to /language/switch 
→ Server updates session & cookie → Page reloads 
→ Content displayed in new language
```

### 2. Language Detection Priority

1. **Session** (`$_SESSION['language']`)
2. **Cookie** (`$_COOKIE['language']`)
3. **Default** (English - `en`)

### 3. Translation System

**PHP Side:**
```php
<?php _e('hero_title'); ?>  // Outputs translated text
<?php echo lang('hero_subtitle'); ?>  // Returns translated text
```

**JavaScript Side:**
```javascript
window.lang('success_submit')  // Returns translated text
```

---

## 🔧 Using Translations

### In PHP Views

**Method 1: Echo directly**
```php
<?php _e('nav_home'); ?>
```

**Method 2: Get translation**
```php
<?php echo lang('form_title'); ?>
```

**Method 3: With parameters**
```php
<?php echo lang('pending_application', [
    'date' => '05 Oct 2025',
    'phone' => '+60 3-1234 5678'
]); ?>
```

### In JavaScript

**Basic usage:**
```javascript
const message = window.lang('success_submit');
alert(message);
```

**With parameters:**
```javascript
const msg = window.lang('pending_application')
    .replace('{date}', '05 Oct 2025')
    .replace('{phone}', '+60 3-1234 5678');
```

---

## 📝 Adding New Translations

### Step 1: Add to Language Files

**application/language/en.php**
```php
return [
    // ... existing translations
    'new_key' => 'New English Text',
];
```

**application/language/my.php**
```php
return [
    // ... existing translations
    'new_key' => 'Teks Baharu Melayu',
];
```

**application/language/zh.php**
```php
return [
    // ... existing translations
    'new_key' => '新中文文本',
];
```

### Step 2: Use in Views

```php
<?php _e('new_key'); ?>
```

---

## 🎨 Language Selector Styling

The language selector is styled with:
- Dropdown menu with flags
- Smooth animations
- Hover effects
- Active state indicators
- Mobile responsive design

**CSS Classes:**
- `.language-selector` - Container
- `.lang-btn` - Trigger button
- `.lang-dropdown` - Dropdown menu
- `.lang-option` - Individual language option

---

## 🚀 Testing Multi-Language

### Test 1: Manual Language Switch
1. Go to: `http://localhost/payhere/`
2. Click the language selector (globe icon)
3. Select **Bahasa Melayu** 🇲🇾
4. Page should reload in Malay
5. Navigate around - all text should be in Malay

### Test 2: Language Persistence
1. Switch to Chinese 🇨🇳
2. Close the browser
3. Open browser again and visit the site
4. Should still be in Chinese ✅

### Test 3: Cookie Expiry
1. Switch to Malay
2. Check browser cookies:
   - Name: `language`
   - Value: `my`
   - Expires: 30 days from now

---

## 🔍 Available Helper Functions

### `get_current_language()`
Returns current language code (`en`, `my`, or `zh`)

```php
$lang = get_current_language(); // 'en'
```

### `set_language($lang)`
Sets the language (session + cookie)

```php
set_language('my'); // Switch to Malay
```

### `load_language($lang)`
Loads translations array

```php
$translations = load_language('zh');
```

### `lang($key, $params)`
Get translated text

```php
echo lang('hero_title');
echo lang('pending_application', ['date' => '05 Oct 2025']);
```

### `_e($key, $params)`
Echo translated text

```php
_e('form_submit');
```

### `get_available_languages()`
Get all supported languages

```php
$languages = get_available_languages();
// Returns array with language details
```

### `get_language_name($code, $native)`
Get language name

```php
echo get_language_name('my');  // 'Malay'
echo get_language_name('my', true);  // 'Bahasa Melayu'
```

### `get_language_json($keys)`
Get translations as JSON for JavaScript

```php
<script>
window.translations = <?php echo get_language_json(); ?>;
</script>
```

---

## 🌐 Language Files Content

### Key Translation Categories:

1. **Site Info** - Site name, tagline
2. **Navigation** - Menu items
3. **Hero Section** - Main banner text
4. **Features** - Why choose us section
5. **Services** - Loan services
6. **Calculator** - Loan calculator
7. **Form** - Application form fields
8. **Footer** - Footer content
9. **Validation** - Error messages
10. **Messages** - Success/error messages
11. **Loan Types** - Product names

---

## 🛠️ Troubleshooting

### Language not switching?

**Check 1: Session Started?**
```php
// In bootstrap.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
```

**Check 2: Language files exist?**
```bash
ls application/language/
# Should show: en.php  my.php  zh.php
```

**Check 3: Helper loaded?**
```php
// In controller
require_once APPPATH . 'helpers/language_helper.php';
```

### Translations not showing?

**Check 1: Key exists in language file?**
```php
// application/language/en.php
'your_key' => 'Your Translation'
```

**Check 2: Using correct function?**
```php
<?php _e('your_key'); ?>  // ✅ Correct
<?php echo 'your_key'; ?>  // ❌ Wrong
```

### JavaScript translations not working?

**Check 1: Translations passed to view?**
```php
// In controller
$data['translations_json'] = get_language_json();
```

**Check 2: Script tag in header?**
```html
<script>
window.translations = <?php echo $translations_json; ?>;
</script>
```

---

## 📱 Mobile Responsive

The language selector is **fully responsive**:

- **Desktop**: Dropdown appears below button
- **Mobile**: Fits within hamburger menu
- **Touch Devices**: Optimized for touch interaction

---

## 🎯 Best Practices

### 1. Always Use Translation Keys
❌ **Don't:**
```php
<h1>Get Your Dream Loan</h1>
```

✅ **Do:**
```php
<h1><?php _e('hero_title'); ?></h1>
```

### 2. Keep Keys Organized
Group related translations:
```php
// Navigation
'nav_home' => 'Home',
'nav_about' => 'About',

// Form
'form_name' => 'Name',
'form_email' => 'Email',
```

### 3. Use Meaningful Key Names
❌ **Bad:** `'text1'`, `'msg2'`  
✅ **Good:** `'hero_title'`, `'form_submit'`

### 4. Include Context in Translations
```php
'form_phone_hint' => 'Format: 012-345-6789 or +6012-345-6789'
```

### 5. Test All Languages
After adding new content, test:
- English 🇬🇧
- Bahasa Melayu 🇲🇾
- 简体中文 🇨🇳

---

## 🚀 Future Enhancements

Possible additions:

1. **More Languages** - Tamil, Hindi, etc.
2. **RTL Support** - For Arabic/Hebrew
3. **Language Auto-Detection** - Based on browser settings
4. **Admin Panel** - Edit translations via UI
5. **Translation Export** - CSV/JSON export
6. **Missing Translation Warnings** - Developer mode

---

## 📞 Support

If you encounter issues:

1. Check this guide first
2. Verify all files are in place
3. Check browser console for errors
4. Test in different browsers
5. Clear browser cache and cookies

---

**Language support is now live! 🌍✨**

Choose your language from the navigation bar and enjoy PayHere in your preferred language!
