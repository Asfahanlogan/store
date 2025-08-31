# HackVault Pro - Complete Cybersecurity Tools Website

## Website Features

- Modern dark theme with Bootstrap 5
- Responsive design for desktop and mobile
- JSON-based database (no SQL required)
- Complete e-commerce functionality
- Admin panel with authentication
- Cryptocurrency payment system
- Product filtering and search
- Contact form with validation
- Animated UI elements and transitions

## Project Structure

```
├── admin/
│   ├── includes/
│   │   ├── footer.php
│   │   └── header.php
│   ├── dashboard.php
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── payments.php
│   └── tools.php
├── assets/
│   └── css/
│       └── animations.css
├── data/
│   ├── admin.json
│   ├── payments.json
│   └── tools.json
├── includes/
│   ├── footer.php
│   └── header.php
├── checkout.php
├── config.php
├── contact.php
├── index.php
├── payment-status.php
├── products.php
└── test.php
```

## Admin Credentials

- Username: `admin`
- Password: `SecureAdmin123!`

## How to Run the Website

1. Place all files in your web server directory (e.g., Laragon's www folder)
2. Ensure PHP 7.0 or higher is installed and configured
3. Access `index.php` through your web browser
4. Navigate to `/admin/` for admin panel access

## Key Functionality

### Frontend
- Homepage with hero section and featured products
- Products page with filtering and search
- Checkout process with cryptocurrency options
- Payment status tracking
- Contact form with validation

### Admin Panel
- Dashboard with sales statistics
- Tool management (CRUD operations)
- Payment records with status updates
- Secure authentication system

### Database
All data is stored in JSON files:
- `data/tools.json` - Product information
- `data/payments.json` - Payment records
- `data/admin.json` - Admin credentials

## Technology Stack

- PHP 7.0+
- Bootstrap 5 (Dark Theme)
- FontAwesome Icons
- Chart.js (for admin dashboard)
- Vanilla JavaScript
- JSON for data storage

## Security Features

- Password hashing for admin authentication
- Session-based login system
- Form validation and sanitization
- CSRF protection in forms
- Secure logout functionality

## Customization

You can easily customize:
- Product listings in `data/tools.json`
- Admin credentials in `data/admin.json`
- Cryptocurrency wallet addresses in `data/admin.json`
- Styling in `assets/css/animations.css`
- Content in individual page files

## Support

For any issues or questions, please check:
1. The test.php file for diagnostics
2. Ensure all files are in the correct locations
3. Verify PHP is properly configured
4. Check file permissions if using Linux/macOS