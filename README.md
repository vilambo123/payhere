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
- Web server (Apache/Nginx)
- mod_rewrite enabled (for Apache)

### Setup Instructions

1. **Clone or download** this repository to your web server directory

2. **Configure base URL**
   
   Open `application/config/config.php` and update the base URL:
   ```php
   $config['base_url'] = 'http://your-domain.com/';
   ```

3. **Set permissions** (if on Linux/Unix)
   ```bash
   chmod -R 755 application/
   chmod -R 755 public/
   ```

4. **Configure web server**

   **For Apache:**
   - Ensure `.htaccess` is present in the root directory
   - Make sure `mod_rewrite` is enabled
   
   **For Nginx:**
   Add this to your server block:
   ```nginx
   location / {
       try_files $uri $uri/ /index.php?$query_string;
   }
   ```

5. **Access the website**
   
   Open your browser and navigate to:
   ```
   http://localhost/
   ```
   or
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

The application form submits to `/submit-inquiry` endpoint. Currently, it validates and returns a JSON response. To save data:

1. **Create a database table:**
   ```sql
   CREATE TABLE inquiries (
       id INT AUTO_INCREMENT PRIMARY KEY,
       name VARCHAR(100),
       email VARCHAR(100),
       phone VARCHAR(20),
       loan_type VARCHAR(50),
       loan_amount DECIMAL(10,2),
       message TEXT,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

2. **Create a model** in `application/models/Inquiry_model.php`

3. **Uncomment database code** in `application/controllers/Home.php`

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
