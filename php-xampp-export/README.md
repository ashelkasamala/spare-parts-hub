# Ashel's Autospare - PHP/XAMPP Setup Guide

## Requirements
- XAMPP (Apache + MySQL + PHP)
- Web browser

## Installation Steps

### 1. Install XAMPP
Download and install XAMPP from https://www.apachefriends.org/

### 2. Create Database
1. Start Apache and MySQL from XAMPP Control Panel
2. Open phpMyAdmin: http://localhost/phpmyadmin
3. Create a new database called `ashels_autospare`
4. Import the `database/schema.sql` file

### 3. Copy Project Files
Copy all files from this folder to: `C:\xampp\htdocs\ashels-autospare\`

### 4. Configure Database Connection
Edit `config/database.php` with your MySQL credentials (default: root with no password)

### 5. Access the Application
Open your browser and go to: http://localhost/ashels-autospare/

## Project Structure

```
ashels-autospare/
├── config/
│   └── database.php          # Database connection
├── database/
│   └── schema.sql            # Complete SQL schema
├── includes/
│   ├── header.php            # Header template
│   ├── footer.php            # Footer template
│   └── functions.php         # Helper functions
├── assets/
│   ├── css/
│   │   └── style.css         # Main stylesheet
│   └── js/
│       └── main.js           # JavaScript functions
├── auth/
│   ├── login.php             # Login page
│   ├── register.php          # Registration page
│   ├── logout.php            # Logout handler
│   └── google-auth.php       # Google OAuth (requires setup)
├── admin/
│   ├── dashboard.php         # Admin dashboard
│   ├── products.php          # Manage products
│   ├── categories.php        # Manage categories
│   ├── orders.php            # Manage orders
│   ├── users.php             # Manage users
│   └── suppliers.php         # Manage suppliers
├── customer/
│   ├── dashboard.php         # Customer dashboard
│   ├── orders.php            # Order history
│   └── profile.php           # Profile settings
├── index.php                 # Landing page
├── products.php              # Products listing
├── product-detail.php        # Single product view
├── contact.php               # Contact page
├── about.php                 # About page
└── services.php              # Services page
```

## Default Admin Account
After importing the database, use these credentials:
- Email: admin@ashelsautospare.com
- Password: Admin123!

## Features Included
- ✅ User authentication (login/register)
- ✅ Role-based access (Admin, Staff, Customer)
- ✅ Product management (CRUD)
- ✅ Category management
- ✅ Order management
- ✅ Customer management
- ✅ Supplier management
- ✅ Contact form
- ✅ Responsive design
- ✅ Dark/Light mode toggle

## Google OAuth Setup (Optional)
1. Go to Google Cloud Console
2. Create OAuth 2.0 credentials
3. Update `config/google-config.php` with your Client ID and Secret
