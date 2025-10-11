# Financial Loan Landing Page - CodeIgniter

A modern, responsive landing page for financial loan services built with PHP using CodeIgniter file structure. Inspired by professional financial service websites with a clean, user-friendly design.

## Features

- 🎨 **Modern UI/UX Design** - Beautiful gradient backgrounds and smooth animations
- 📱 **Fully Responsive** - Works perfectly on desktop, tablet, and mobile devices
- 💰 **Loan Calculator** - Interactive calculator with real-time monthly payment estimates
- 📋 **Application Form** - Complete loan application form with validation
- ⚡ **Fast Loading** - Optimized assets and clean code
- 🔒 **Secure** - Form validation and security best practices
- 🎯 **SEO Optimized** - Proper meta tags and semantic HTML

## Services Offered

- Personal Loans (up to RM 200,000)
- Business Loans (up to RM 1,000,000)
- Home Loans (up to RM 2,000,000)
- Car Loans (up to RM 500,000)

## Technology Stack

- **Backend**: PHP (CodeIgniter structure)
- **Frontend**: HTML5, CSS3, JavaScript
- **Icons**: Font Awesome 6.4.0
- **Fonts**: Google Fonts (Inter)

## Directory Structure

```
/
├── application/
│   ├── config/
│   │   ├── autoload.php
│   │   ├── bootstrap.php
│   │   ├── config.php
│   │   └── routes.php
│   ├── controllers/
│   │   └── Home.php
│   ├── models/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── header.php
│   │   │   └── footer.php
│   │   └── home/
│   │       └── index.php
│   └── helpers/
├── public/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── images/
├── system/
├── .htaccess
├── index.php
└── README.md
```

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB
- Web server (Apache/Nginx)
- XAMPP (for Windows) or LAMP/MAMP (for Linux/Mac)
- mod_rewrite enabled (for Apache)

### Setup Instructions

#### 1. **Setup Files**
   - Clone or download this repository to your XAMPP `htdocs` folder
   - For XAMPP: `C:\xampp\htdocs\payhere\`

#### 2. **Database Setup**
   
   **Start XAMPP Services:**
   - Open XAMPP Control Panel
   - Start Apache and MySQL
   
   **Import Database:**
   1. Open phpMyAdmin: `http://localhost/phpmyadmin`
   2. Click on "Import" tab
   3. Choose file: `database_setup.sql`
   4. Click "Go" to import
   
   **Or manually create:**
   1. Create database: `loan_system`
   2. Import the SQL file
   
   📖 **Detailed guide:** See [INSTALLATION_DATABASE.md](INSTALLATION_DATABASE.md)

#### 3. **Configure Database**
   
   Set your database credentials using environment variables:
   - `DB_HOST` - Database hostname (default: localhost)
   - `DB_USERNAME` - Database username
   - `DB_PASSWORD` - Database password
   - `DB_NAME` - Database name (default: loan_system)
   - `DB_PORT` - Database port (default: 3306)
   
   The database configuration in `application/config/database.php` uses environment variables for security.

#### 4. **Test Database Connection**
   
   Visit: `http://localhost/payhere/test-database.php`
   
   You should see:
   - ✅ Connection successful
   - Database tables listed
   - Sample inquiries

#### 5. **Configure Base URL** (Auto-configured for XAMPP)
   
   The base URL is automatically detected. If needed, manually edit:
   `application/config/config.php`

#### 6. **Set Permissions** (Linux/Mac only)
   ```bash
   chmod -R 755 application/
   chmod -R 755 public/
   ```

#### 7. **Access the Website**
   
   Open your browser:
   ```
   http://localhost/payhere/
   ```
   
   Or for other servers:
   ```
   http://your-domain.com/
   ```

## Development Server

You can use PHP's built-in development server for quick testing:

```bash
php -S localhost:8080
```

Then visit `http://localhost:8080` in your browser.

## Configuration

### Base URL Configuration

Edit `application/config/config.php`:
```php
$config['base_url'] = 'http://localhost:8080/';
```

### Routes Configuration

Edit `application/config/routes.php`:
```php
$route['default_controller'] = 'home';
$route['submit-inquiry'] = 'home/submit_inquiry';
```

## Features Breakdown

### 1. Hero Section
- Eye-catching gradient background
- Key statistics and benefits
- Clear call-to-action buttons
- Animated security badge

### 2. Services Section
- Four loan types with detailed information
- Interest rates and maximum amounts
- Featured "Most Popular" card

### 3. How It Works
- Simple 3-step process visualization
- Clear icons and descriptions
- Responsive step cards

### 4. Benefits Section
- Six key benefits highlighted
- Icon-based grid layout
- Quick scan format

### 5. Loan Calculator
- Interactive range sliders
- Real-time payment calculation
- Visual feedback on values
- Formula: M = P[r(1+r)^n]/[(1+r)^n-1]

### 6. Application Form
- Comprehensive input fields
- Client-side validation
- AJAX form submission
- Success/error messaging
- Terms & conditions checkbox

### 7. Testimonials
- Customer reviews with ratings
- Social proof section
- Avatar placeholders

### 8. Footer
- Company information
- Quick links
- Contact details
- Social media icons

## Customization

### Changing Colors

Edit `public/css/style.css` and modify the CSS variables:

```css
:root {
    --primary-color: #2563eb;
    --secondary-color: #1e40af;
    --accent-color: #3b82f6;
    /* ... more colors */
}
```

### Updating Content

Edit `application/views/home/index.php` to modify:
- Service offerings
- Loan amounts and rates
- Testimonials
- Statistics
- Benefits

### Adding More Pages

1. Create a new controller in `application/controllers/`
2. Add corresponding views in `application/views/`
3. Update routes in `application/config/routes.php`

## Form Submission

✅ **Fully Implemented!** The application form now:

1. **Validates user input** (required fields, email format, numeric amounts)
2. **Saves to MySQL database** via the Inquiry_model
3. **Returns JSON response** for AJAX handling
4. **Stores metadata** (IP address, user agent, timestamp)

### Database Tables

The system uses these tables:

- **inquiries** - Loan applications with full details
- **loan_types** - Loan products configuration
- **contacts** - General contact form submissions
- **site_settings** - Website settings

### View Submitted Data

**Via phpMyAdmin:**
1. Go to `http://localhost/phpmyadmin`
2. Select `loan_system` database
3. Browse the `inquiries` table

**Via Test Page:**
- Visit: `http://localhost/payhere/test-database.php`

### API Endpoint

**POST** `/submit-inquiry`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+60123456789",
    "loan_type": "personal",
    "loan_amount": 50000,
    "income": 5000,
    "message": "Need loan for business"
}
```

**Success Response:**
```json
{
    "success": true,
    "message": "Thank you! We have received your application...",
    "inquiry_id": 123
}
```

**Error Response:**
```json
{
    "success": false,
    "message": "Email is required. Phone is required."
}
```

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance

- Optimized CSS with minimal framework overhead
- Vanilla JavaScript (no jQuery dependency)
- CDN-hosted fonts and icons
- Smooth scroll animations
- Lazy loading ready

## Security Features

- CSRF protection ready
- XSS filtering
- Form validation
- SQL injection prevention (when database is connected)
- Input sanitization

## Future Enhancements

- [ ] Database integration for storing inquiries
- [ ] Email notification system
- [ ] Admin dashboard
- [ ] Document upload functionality
- [ ] Multi-language support
- [ ] Live chat integration
- [ ] Payment gateway integration
- [ ] User authentication
- [ ] Loan application tracking

## License

This project is open source and available for modification and distribution.

## Credits

- Font Awesome for icons
- Google Fonts for typography
- Inspired by modern financial service websites

## Support

For issues or questions, please create an issue in the repository or contact the development team.

---

**Version**: 1.0.0  
**Last Updated**: 2025-10-03  
**Author**: Development Team
