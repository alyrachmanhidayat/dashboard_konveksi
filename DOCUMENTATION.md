# Dashboard Konveksi - Documentation

## Overview
Dashboard Konveksi is a web-based application built with Laravel framework for managing garment manufacturing orders. It's designed to handle the entire workflow from order creation through completion and billing, with features for tracking production progress, managing invoices, and reporting.

## Project Structure
```
dashboard-konveksi/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Auth/
│   │       ├── DashboardController.php
│   │       ├── InvoiceController.php
│   │       ├── ProfileController.php
│   │       ├── RekapController.php
│   │       └── SpkController.php
│   ├── Models/
│   │   ├── Invoice.php
│   │   ├── Payment.php
│   │   ├── Spk.php
│   │   ├── SpkSize.php
│   │   └── User.php
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
│   ├── web.php
│   └── auth.php
├── storage/
├── tests/
├── vendor/
├── .env
├── .gitignore
├── composer.json
├── package.json
├── README.md
├── projectNote.md
├── spkprint.md
└── tailwind.config.js
```

## Features

### 1. Dashboard
- Displays overall order statistics and progress
- Shows cards for Total Orders, Orders within 2, 8, and 12 day deadlines
- Shows a table of all current orders with delivery dates, customer names, and progress
- Visual indicators for urgent deadlines (color-coded based on delivery proximity)

### 2. SPK (Surat Perintah Kerja / Work Order)
- Create new orders with customer information
- Upload design images
- Specify fabric materials and sizes (S to 3XL)
- Set entry and delivery dates
- Track progress through cutting, printing, pressing, stitching, and delivery stages
- Ability to close or reject orders when completed
- Print SPKs in A4 format

### 3. SPK Closed
- View orders that have been closed or rejected
- Assign price per meter for closed orders
- Manage rejected orders separately

### 4. Invoice Management
- Generate invoices from closed orders
- Combine multiple orders into a single invoice
- Print invoices in A5 format
- Publish invoices to accounts receivable

### 5. Accounts Receivable (Piutang)
- Track unpaid invoices
- Record partial and full payments
- Monitor payment history
- Filter by date ranges

### 6. Reporting (Rekap)
- **Omzet Report**: Total revenue from completed orders
  - Monthly cards showing completed orders, total revenue, quantity, and meters
  - Yearly chart showing revenue trends
  - Detailed table of completed orders
- **Reject Report**: Track rejected orders
  - Monthly cards showing rejected orders, nominal value, quantity, and meters
  - Yearly chart showing reject trends
  - Detailed table of rejected orders

## Architecture

### Models
- **Spk**: Main model for work orders containing customer, order, and progress information
- **SpkSize**: Tracks quantity for each clothing size (S, M, L, XL, 2XL, 3XL)
- **Invoice**: Manages invoice data and payment tracking
- **Payment**: Tracks individual payment transactions
- **User**: Standard Laravel user authentication model

### Controllers
- **DashboardController**: Handles dashboard statistics and main page
- **SpkController**: Manages work orders creation, editing, and progress tracking
- **InvoiceController**: Handles invoicing, accounts receivable, and payment processing
- **RekapController**: Generates reports and analytics (admin only)
- **ProfileController**: User profile management

### Key Technical Features
- Database-level locking to prevent race conditions when generating SPK/invoice numbers
- File organization with automatic storage in year/month directories
- Responsive UI using Tailwind CSS
- Progress tracking with visual indicators
- Date-based filtering for reports
- Role-based access control (admin privileges required for reports)

## Database Schema

### Key Tables
- `spk`: Work orders with status (In Progress, Closed, Rejected), dates, customer info, progress tracking
- `spk_sizes`: Quantity per size for each work order
- `invoices`: Invoice records linked to SPKs
- `payments`: Payment transactions linked to invoices
- `users`: User authentication and roles

## Installation and Setup

1. Clone or download the project
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Install Node.js dependencies:
   ```bash
   npm install
   ```
4. Copy and configure environment file:
   ```bash
   cp .env.example .env
   ```
5. Configure your database in `.env`
6. Generate application key:
   ```bash
   php artisan key:generate
   ```
7. Run database migrations:
   ```bash
   php artisan migrate
   ```
8. Start the development server:
   ```bash
   php artisan serve
   ```

## Development Commands

- Run development server: `php artisan serve`
- Run tests: `php artisan test`
- Generate application key: `php artisan key:generate`
- Run database migrations: `php artisan migrate`
- Seed database: `php artisan db:seed`

## Key Business Logic

### SPK Number Generation
- Format: `SPK/MM/YYYY/XXXX`
- Uses database locking to prevent duplicate numbers
- Sequential numbering per month

### Invoice Number Generation
- Format: `INV/MM/YYYY/XXXX`
- Sequential numbering per month

### Status Flow
1. **In Progress**: New orders can be modified and progress tracked
2. **Closed**: Completed orders ready for invoicing
3. **Rejected**: Orders that were not completed successfully

### Payment Processing
- Handles partial payments for invoices
- Calculates remaining balance automatically
- Moves fully paid invoices to history

## Security Features
- Role-based access control
- Admin-only access to financial reports
- Validation for all user inputs
- Secure file uploads with type and size restrictions

## Future Enhancements
Based on the project notes, planned enhancements include:
- Automated file organization by date
- Database optimization with indexing
- Additional logging for operations
- Improved UI/UX for better user experience

## Project Notes Reference
This project originated from the Laravel framework skeleton and has been customized for garment manufacturing needs. The `projectNote.md` file contains detailed development tasks, questions, and implementation recommendations that were used during development.

## Technology Stack
- **Backend**: Laravel 12.x (PHP)
- **Frontend**: Blade templates, Tailwind CSS, Alpine.js
- **Database**: MySQL (configurable via Laravel)
- **Build Tools**: Vite, PostCSS, Tailwind CSS
- **Testing**: PHPUnit