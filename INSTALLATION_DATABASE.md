# Database Installation Guide for XAMPP

## Step-by-Step Instructions

### 1. Start XAMPP Services
1. Open **XAMPP Control Panel**
2. Start **Apache** service
3. Start **MySQL** service

### 2. Access phpMyAdmin
1. Open your web browser
2. Go to: `http://localhost/phpmyadmin`
3. You should see the phpMyAdmin interface

### 3. Create Database Using SQL File

**Method 1: Import SQL File (Recommended)**
1. In phpMyAdmin, click on **"Import"** tab at the top
2. Click **"Choose File"** button
3. Navigate to your project folder and select `database_setup.sql`
4. Click **"Go"** button at the bottom
5. You should see a success message

**Method 2: Run SQL Manually**
1. In phpMyAdmin, click on **"SQL"** tab at the top
2. Open `database_setup.sql` file in a text editor
3. Copy all the SQL code
4. Paste it into the SQL query box
5. Click **"Go"** button

### 4. Verify Database Creation
After import, you should see:
- A new database named `loan_system` in the left sidebar
- Click on it to expand and verify these tables:
  - ✅ `inquiries` - Store loan applications
  - ✅ `loan_types` - Loan product configurations
  - ✅ `contacts` - General contact submissions
  - ✅ `site_settings` - Website settings
  - ✅ `inquiry_summary` - View for reports

### 5. Check Sample Data
1. Click on `inquiries` table
2. Click **"Browse"** tab
3. You should see 3 sample inquiry records

### 6. Database Configuration

The database is already configured in `application/config/database.php`:

```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',  // Default XAMPP password is empty
    'database' => 'loan_system',
    'dbdriver' => 'mysqli',
    'port' => 3306
);
```

**If you have a different MySQL password:**
1. Open `application/config/database.php`
2. Change the `'password'` value to your MySQL root password
3. Save the file

### 7. Test Database Connection

Create a test file `test-database.php` in your root folder:

```php
<?php
// Test database connection
$conn = new mysqli('localhost', 'root', '', 'loan_system');

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

echo "✅ Connected successfully to database!<br><br>";

// Test query
$result = $conn->query("SELECT COUNT(*) as total FROM inquiries");
$row = $result->fetch_assoc();

echo "📊 Total inquiries in database: " . $row['total'];

$conn->close();
?>
```

Then visit: `http://localhost/payhere/test-database.php`

### 8. Access Your Application
1. Go to: `http://localhost/payhere/`
2. Fill out the loan application form
3. Submit the form
4. Check phpMyAdmin to see the new record in `inquiries` table

## Database Tables Overview

### 📋 inquiries
Stores all loan application submissions
- `id` - Auto-increment primary key
- `name` - Applicant name
- `email` - Contact email
- `phone` - Phone number
- `loan_type` - Type of loan (personal/business/home/car)
- `loan_amount` - Requested amount
- `monthly_income` - Applicant's income
- `message` - Additional notes
- `status` - Application status (pending/contacted/approved/rejected)
- `created_at` - Submission timestamp

### 💼 loan_types
Reference table for loan products
- Predefined loan types with interest rates and limits
- Can be used to dynamically generate loan options

### 📞 contacts
For general contact form submissions

### ⚙️ site_settings
Store website configuration (email, phone, etc.)

## Common Issues & Solutions

### Issue 1: "Access Denied for user 'root'@'localhost'"
**Solution:** 
- Your MySQL root account has a password
- Edit `application/config/database.php`
- Update the `password` field with your MySQL password

### Issue 2: "Database 'loan_system' doesn't exist"
**Solution:**
- Re-import the `database_setup.sql` file
- Or manually create database: `CREATE DATABASE loan_system;`

### Issue 3: "Table 'loan_system.inquiries' doesn't exist"
**Solution:**
- Make sure you imported ALL the SQL from `database_setup.sql`
- Check the SQL tab in phpMyAdmin for any errors

### Issue 4: Can't access phpMyAdmin
**Solution:**
- Make sure MySQL is running in XAMPP Control Panel
- Try: `http://127.0.0.1/phpmyadmin`
- Check if port 3306 is not blocked by firewall

## Viewing Submitted Inquiries

### Via phpMyAdmin:
1. Go to `http://localhost/phpmyadmin`
2. Click on `loan_system` database
3. Click on `inquiries` table
4. Click **"Browse"** to see all records

### Via Custom Admin Page (Coming soon):
- Access: `http://localhost/payhere/admin`
- View, filter, and manage all inquiries

## Security Notes

⚠️ **Important for Production:**
1. Change MySQL root password
2. Create a dedicated database user (not root)
3. Enable CSRF protection in `application/config/config.php`
4. Use environment variables for database credentials
5. Add authentication for admin pages

## Next Steps

✅ Database is now configured!
✅ Forms will save to database
✅ Ready for development

Would you like me to create:
- Admin dashboard to view inquiries?
- Email notification system?
- Export functionality (Excel/PDF)?
- Advanced search and filtering?

---
**Need Help?** Check the error logs in:
- `C:\xampp\mysql\data\mysql_error.log`
- `C:\xampp\apache\logs\error.log`
