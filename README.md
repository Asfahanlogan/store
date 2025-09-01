## HackVault Pro — Digital Marketplace

Modern PHP-based digital marketplace with a dark, responsive UI, JSON-backed storage (no SQL), admin panel, and cryptocurrency checkout.

### Features

- Modern dark UI with Bootstrap 5
- Responsive for desktop and mobile
- JSON data storage (no database server needed)
- Full e‑commerce flow (browse, cart/checkout, payment status)
- Admin panel with authentication
- Cryptocurrency payment flow (BTC/ETH/LTC)
- Product filtering and search
- Contact form with validation
- Smooth animations and transitions

### Tech Stack

- PHP 7.4+ (works with 7.0+, recommended 7.4+)
- Bootstrap 5, Font Awesome
- Chart.js (admin dashboard)
- Vanilla JavaScript
- JSON for persistence

## Requirements

- PHP 7.4+ with built-in web server or Apache/Nginx + PHP-FPM
- Write permissions for the `data/` directory (for JSON writes)

## Quick Start

1. Clone or copy the repository into your web root, or work locally.
2. Ensure PHP is installed: `php -v`.
3. Start a local server from the project root:

```bash
php -S localhost:8000 -t .
```

4. Visit `http://localhost:8000` in your browser.
5. Open the admin panel at `http://localhost:8000/admin/`.

If using Apache/Nginx, point the virtual host to the project root and ensure PHP is enabled. Adjust `SITE_URL` in `config.php` if needed (e.g., `http://localhost:8000`).

## Configuration

All core settings live in `config.php`:

- `SITE_NAME`: Display name for the site
- `SITE_URL`: Base URL used by the app
- `DATA_PATH`: Filesystem path for JSON data files

Utility helpers include JSON read/write helpers, price formatting, ID generation, admin session checks, redirect helper, and crypto wallet address retrieval.

### Crypto Wallet Addresses

Update wallet addresses in `data/admin.json` under `wallet_addresses`. If not set, safe defaults are shown by `getCryptoAddresses()`.

## Admin Panel

- URL: `/admin/`
- Default credentials:
  - Username: `admin`
  - Password: `SecureAdmin123!`

After logging in you can:

- Manage tools/products (CRUD)
- View and update payment records
- See dashboard charts and basic stats

## Data Storage

All data is stored as JSON files in `data/`:

- `data/tools.json`: Product catalog
- `data/payments.json`: Payment records
- `data/admin.json`: Admin credentials and wallet addresses

Back up these files regularly if you run in production.

## Application Pages

- `index.php`: Home with featured products and categories
- `products.php`: Catalog with filtering and search
- `checkout.php`: Single‑item checkout flow
- `cart.php` / `checkout-cart.php`: Cart and cart checkout flow
- `payment-status.php`: Payment status tracking
- `contact.php`: Contact form with validation
- `test.php`: Environment diagnostics

Admin:

- `admin/login.php`, `admin/logout.php`
- `admin/dashboard.php`, `admin/tools.php`, `admin/payments.php`

## Directory Structure

```text
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
├── cart.php
├── checkout-cart.php
├── checkout.php
├── config.php
├── contact.php
├── index.php
├── payment-status.php
├── products.php
├── solution.php
├── SOLUTION.md
└── test.php
```

## Payment Flow (Crypto)

- Customer selects a product and proceeds to checkout.
- A unique order ID is generated and shown with wallet addresses.
- Customer pays the amount to the provided address.
- Payment status is tracked via `payment-status.php` and admin updates.

Note: This implementation demonstrates a crypto payment flow and does not integrate with a gateway by default. In production, integrate a payment confirmation service or manually reconcile payments.

## Security Checklist

- Change the default admin password immediately.
- Serve over HTTPS in production.
- Keep `data/` writable by the web server, but not publicly browsable if you deploy behind a web server (use proper deny rules).
- Validate and sanitize user input (already present for forms).
- Use strong session settings and regenerate session IDs after login.

## Troubleshooting

- Visit `test.php` to run quick diagnostics.
- Ensure PHP is installed and meets the version requirement.
- Verify permissions so the server can write to `data/` (e.g., on Linux: `chmod -R 775 data` and ensure correct user/group).
- If JSON files get corrupted, validate JSON syntax and restore from backup.

## Customization

- Products: edit `data/tools.json`
- Admin credentials and wallet addresses: `data/admin.json`
- Styling/animations: `assets/css/animations.css`
- Content and copy: update individual page files

## Deployment Notes

- Configure a virtual host to the project root.
- Set proper file permissions for `data/`.
- Consider moving JSON files outside the web root if your server serves files directly, and update `DATA_PATH` accordingly.
- Update `SITE_URL` in `config.php` for your domain.

## Support

If you run into issues:

1. Check `test.php` diagnostics
2. Confirm file paths and permissions
3. Verify PHP configuration and logs
4. Review web server error logs