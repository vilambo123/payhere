# 🚀 Quick Start Guide - Loan Landing Page

## ✅ Complete Setup in 5 Minutes!

### Step 1: Start XAMPP (1 minute)
1. Open **XAMPP Control Panel**
2. Click **Start** for **Apache**
3. Click **Start** for **MySQL**
4. Both should show green "Running" status

### Step 2: Import Database (2 minutes)
1. Open browser: `http://localhost/phpmyadmin`
2. Click **"Import"** tab at the top
3. Click **"Choose File"**
4. Select `database_setup.sql` from your project folder
5. Click **"Go"** button at bottom
6. ✅ Success! Database created with sample data

### Step 3: Test Everything (2 minutes)

**Test Database:**
- Visit: `http://localhost/payhere/test-database.php`
- Should show: ✅ Connected successfully

**Test Landing Page:**
- Visit: `http://localhost/payhere/`
- Should show: Beautiful loan landing page

**Test Admin Panel:**
- Visit: `http://localhost/payhere/index.php/admin`
- Should show: Dashboard with sample inquiries

---

## 🎯 Quick Links

| Page | URL | Purpose |
|------|-----|---------|
| **Landing Page** | `http://localhost/payhere/` | Public loan application page |
| **Admin Dashboard** | `http://localhost/payhere/index.php/admin` | View all inquiries |
| **Database Test** | `http://localhost/payhere/test-database.php` | Check DB connection |
| **phpMyAdmin** | `http://localhost/phpmyadmin` | Manage database |

---

## 📊 What's Included

### ✅ Frontend Features
- ✨ Modern, responsive design
- 💰 Loan calculator with real-time calculations
- 📋 Application form with validation
- 🎨 Beautiful gradient hero section
- 📱 Mobile-friendly navigation
- ⭐ Customer testimonials
- 🔄 Smooth animations

### ✅ Backend Features
- 💾 MySQL database integration
- 📝 Form validation
- 🔒 Security (XSS protection, SQL injection prevention)
- 📊 Admin dashboard
- 📈 Statistics and analytics
- 🔍 Filter and search inquiries

### ✅ Database Tables
- **inquiries** - Loan applications (8 columns)
- **loan_types** - Loan products (4 pre-configured)
- **contacts** - Contact form submissions
- **site_settings** - Website configuration

---

## 🎬 Try It Out!

### Submit a Test Application

1. Go to: `http://localhost/payhere/`
2. Scroll to **"Apply for a Loan"** section
3. Fill in the form:
   - Name: Test User
   - Email: test@example.com
   - Phone: +60123456789
   - Loan Type: Personal Loan
   - Amount: 50000
4. Click **"Submit Application"**
5. ✅ Success message appears!

### View in Admin

1. Go to: `http://localhost/payhere/index.php/admin`
2. See your new inquiry in the table
3. View statistics updated
4. Filter by status or loan type

### Check Database

1. Go to: `http://localhost/phpmyadmin`
2. Click `loan_system` database
3. Click `inquiries` table
4. Click **"Browse"**
5. See your submission with full details!

---

## 🛠️ Troubleshooting

### Issue: Page shows "Access Denied"
**Solution:** Database credentials not configured. Set environment variables `DB_USERNAME` and `DB_PASSWORD` with your database credentials.

### Issue: CSS/JS not loading
**Solution:** Check that you're accessing via `http://localhost/payhere/` (not file://)

### Issue: "Database doesn't exist"
**Solution:** Re-import `database_setup.sql` in phpMyAdmin

### Issue: Form submission fails
**Solution:** 
1. Visit `test-database.php` to check connection
2. Check MySQL is running in XAMPP
3. Check browser console for errors

---

## 📁 Project Structure

```
payhere/
├── application/
│   ├── controllers/
│   │   ├── Home.php          (Landing page)
│   │   └── Admin.php         (Admin dashboard)
│   ├── models/
│   │   └── Inquiry_model.php (Database operations)
│   ├── views/
│   │   ├── layouts/          (Header/Footer)
│   │   ├── home/             (Landing page)
│   │   └── admin/            (Dashboard)
│   └── config/
│       ├── config.php        (Base URL - auto-configured)
│       ├── database.php      (MySQL settings)
│       └── routes.php        (URL routing)
├── public/
│   ├── css/style.css         (Main styles)
│   └── js/script.js          (Calculator, forms)
├── database_setup.sql        (Database import file)
├── test-database.php         (Connection test)
└── index.php                 (Entry point)
```

---

## 🎨 Customization

### Change Colors
Edit `public/css/style.css`:
```css
:root {
    --primary-color: #2563eb;  /* Change this */
    --secondary-color: #1e40af;
}
```

### Change Company Name
Edit `application/views/layouts/header.php`:
```php
<span>QuickLoan</span>  <!-- Change this -->
```

### Change Database Name
1. Edit `database_setup.sql` - Change `loan_system` to your name
2. Set environment variable `DB_NAME` to your database name
3. Re-import SQL file

### Add More Loan Types
1. Open phpMyAdmin
2. Go to `loan_types` table
3. Insert new row with details

---

## 📊 Database Schema Quick Reference

### inquiries table
```sql
id              INT (auto)
name            VARCHAR(100)
email           VARCHAR(100)
phone           VARCHAR(20)
loan_type       VARCHAR(50)
loan_amount     DECIMAL(15,2)
monthly_income  DECIMAL(15,2)
message         TEXT
status          ENUM (pending/contacted/approved/rejected)
ip_address      VARCHAR(45)
created_at      TIMESTAMP
```

---

## 🎯 Next Steps

### For Development
- [ ] Add user authentication for admin
- [ ] Email notifications on new inquiries
- [ ] Export inquiries to Excel/PDF
- [ ] Advanced analytics and charts
- [ ] Document upload functionality

### For Production
- [ ] Configure database credentials via environment variables
- [ ] Use strong database passwords
- [ ] Enable CSRF protection
- [ ] Add SSL certificate (HTTPS)
- [ ] Configure email settings
- [ ] Set up backups
- [ ] Add rate limiting

---

## 📚 Documentation Files

| File | Description |
|------|-------------|
| `README.md` | Complete project documentation |
| `INSTALLATION_DATABASE.md` | Detailed database setup guide |
| `QUICK_START_GUIDE.md` | This file - quick reference |
| `database_setup.sql` | Database creation script |

---

## 💡 Tips

✅ **Bookmark these URLs** for quick access
✅ **Keep XAMPP running** while developing
✅ **Check test-database.php** if something breaks
✅ **Use phpMyAdmin** to view/edit data directly
✅ **Check browser console** for JavaScript errors

---

## 🆘 Need Help?

1. **Check test-database.php** - Shows connection status
2. **Check browser console** - Shows JavaScript errors  
3. **Check XAMPP logs** - Located in `C:\xampp\apache\logs\`
4. **Check phpMyAdmin** - Verify database structure
5. **Re-import SQL** - Fix corrupted tables

---

## ✨ Features at a Glance

| Feature | Status | Location |
|---------|--------|----------|
| Landing Page | ✅ Ready | `http://localhost/payhere/` |
| Loan Calculator | ✅ Working | Home page |
| Application Form | ✅ Saves to DB | Home page |
| Admin Dashboard | ✅ Ready | `/index.php/admin` |
| Database | ✅ Configured | phpMyAdmin |
| Form Validation | ✅ Active | Frontend + Backend |
| Responsive Design | ✅ Mobile-ready | All pages |
| Sample Data | ✅ Included | 3 sample inquiries |

---

**🎉 You're all set! Start customizing your loan landing page!**

**Version:** 1.0.0  
**Last Updated:** 2025-10-03  
**XAMPP Compatible:** ✅ Yes
