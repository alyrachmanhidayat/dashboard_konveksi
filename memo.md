# Dashboard Konveksi - AI Assistant Memory Bank

**Last Updated**: 2025-10-27
**Laravel Version**: 12.x
**PHP Version**: 8.2+
**Database**: MySQL 8.0

---

## 📋 PROJECT OVERVIEW

**Dashboard Konveksi** is a comprehensive web application for managing garment manufacturing operations. It handles the complete workflow from order creation (SPK - Surat Perintah Kerja) through production tracking, order completion, invoicing, payment management, and financial reporting.

### Core Business Flow
1. **Create SPK** → Work order with customer details, sizes, materials, delivery dates
2. **Track Progress** → Design, Print, Press, Delivery stages
3. **Close/Reject Order** → Finalize completed or rejected orders
4. **Set Pricing** → Assign price per meter and/or price per piece
5. **Generate Invoices** → Create 1 or 2 invoices based on pricing model
6. **Manage Payments** → Track partial/full payments, accounts receivable
7. **Generate Reports** → Revenue (Omzet) and Reject reports with charts

---

## 🗄️ DATABASE ARCHITECTURE

### Tables & Relationships

#### 1. **users**
```
- id (PK)
- name, username, email, password
- is_admin (boolean) - Role-based access control
- email_verified_at, remember_token
- timestamps
```

#### 2. **spks** (Work Orders)
```
- id (PK)
- spk_number (UNIQUE) - Format: SPK/MM/YYYY/XXXX
- customer_name, order_name
- entry_date, delivery_date, closed_date
- material, description
- total_qty (sum of all sizes)
- total_meter (fabric meters used)
- price_per_meter (decimal 10,2) - Fabric pricing
- harga_per_piece (decimal 10,2) - Per-piece pricing [ADDED: 2025-10-22]
- design_image_path
- status ENUM('In Progress', 'Closed', 'Rejected')
- Progress flags: is_design_done, is_print_done, is_press_done, is_delivery_done
- user_id (FK to users)
- timestamps
```

#### 3. **spk_sizes**
```
- id (PK)
- spk_id (FK → spks, CASCADE DELETE)
- size (S, M, L, XL, XXL, XXXL)
- quantity
- timestamps
```

#### 4. **invoices**
```
- id (PK)
- invoice_number (UNIQUE) - Format: INV/MM/YYYY/XXXX
- spk_id (FK → spks, CASCADE DELETE)
- customer_name, order_name
- total_qty, total_amount (decimal 10,2)
- paid_amount (decimal 10,2, default: 0)
- is_paid (boolean, default: false)
- timestamps
```

#### 5. **payments**
```
- id (PK)
- invoice_id (FK → invoices, CASCADE DELETE)
- amount (decimal 10,2)
- payment_date
- timestamps
```

### Entity Relationships
- **Spk** → hasMany(SpkSize), hasOne(Invoice), belongsTo(User)
- **SpkSize** → belongsTo(Spk)
- **Invoice** → belongsTo(Spk), hasMany(Payment)
- **Payment** → belongsTo(Invoice)
- **User** → hasMany(Spk)

---

## 🎯 MODELS & ELOQUENT

### Spk Model
**Location**: `app/Models/Spk.php`

**Relationships**:
- `spkSizes()` - hasMany(SpkSize)
- `invoice()` - hasOne(Invoice)
- `user()` - belongsTo(User)

**Accessors**:
- `progressPercentage` - Calculates completion percentage (0-100%) based on 4 stages
- `progressBarColor` - Returns Bootstrap class (bg-success, bg-info, bg-warning, bg-secondary)
- `bgColor` - Returns color based on delivery urgency:
  - ≤8 days: bg-danger (red - critical)
  - 9-10 days: bg-warning (yellow - moderate)
  - 11-12 days: bg-success (green - on track)
  - >12 days: bg-primary (blue - normal)
- `formattedDeliveryDate` - Returns formatted date (d M Y)

### Invoice Model
**Location**: `app/Models/Invoice.php`

**Relationships**:
- `spk()` - belongsTo(Spk)
- `payments()` - hasMany(Payment)

**Accessors & Methods**:
- `remainingAmount` - Accessor: calculates unpaid balance (total_amount - paid_amount)
- `getPaidAmount()` - Method: sums all related payments or returns paid_amount field

### SpkSize, Payment, User Models
Standard Eloquent models with basic relationships.

---

## 🔧 CONTROLLERS & BUSINESS LOGIC

### 1. DashboardController
**Location**: `app/Http/Controllers/DashboardController.php`

#### `index()`
Displays main dashboard with:
- **Total Orders**: Count of 'In Progress' SPKs
- **Deadline Cards**: Orders within H2, H8, H10, H12 deadlines
- **SPK Table**: All In Progress orders sorted by delivery_date ASC

### 2. SpkController
**Location**: `app/Http/Controllers/SpkController.php`

#### Key Methods:
- **`create()`** - Show form with auto-generated SPK number
- **`store(Request)`** - Create new SPK
  - Validates customer info, dates, materials, sizes
  - Uploads design image to `spk_designs/`
  - Uses DB transaction for atomicity
  - Sums sizes to calculate total_qty
  
- **`edit(Spk)`** - Show edit form for existing SPK
- **`update(Request, Spk)`** - Update SPK
  - Deletes old sizes, recreates from form data
  - Handles image replacement if new file uploaded
  
- **`updateStatus(Request, Spk)`** - Update progress/status
  - **Action: update_progress** → Updates checkboxes and total_meter
  - **Action: close_order** → Sets status to 'Closed', requires is_print_done and total_meter > 0
  - **Action: reject_order** → Sets status to 'Rejected', same requirements
  
- **`print(Spk)`** - Generate printable SPK (A4 format)
- **`getSpkData()`** - API endpoint returning JSON of active SPKs for dashboard
- **`generateSpkNumber()`** - Thread-safe generation using DB transaction with lockForUpdate()

### 3. InvoiceController
**Location**: `app/Http/Controllers/InvoiceController.php`

#### Key Methods:

**`spkClosedIndex()`**
- Lists all Closed/Rejected SPKs
- Used for admin to assign prices

**`savePrice(Request, Spk)`** [MODIFIED: 2025-10-22]
- Accepts `price_per_meter` AND/OR `harga_per_piece`
- Validates at least one price is provided
- Returns JSON response
- **Important**: Once saved, prices should not be editable (business rule)

**`invoiceIndex()`**
- Lists Closed SPKs that don't have invoices yet
- Shows SPKs ready for invoice generation

**`publishInvoice(Request)`** [MAJOR CHANGES: 2025-10-22]
Creates 1 or 2 invoices based on pricing strategy:

1. **Only `price_per_meter` exists**:
   - Creates 1 invoice (MTR)
   - order_name appended with " (MTR)"
   - total_amount = total_meter × price_per_meter
   
2. **Only `harga_per_piece` exists**:
   - Creates 1 invoice (QTY)
   - order_name appended with " (QTY)"
   - total_amount = total_qty × harga_per_piece
   
3. **Both prices exist**:
   - Creates 2 separate invoices:
     - Invoice 1: MTR-based (total_meter × price_per_meter)
     - Invoice 2: QTY-based (total_qty × harga_per_piece)
   - Both reference same spk_id
   - Sequential invoice numbers generated

**Returns**: Array of created invoice IDs for printing

**`piutangIndex()`**
- Lists unpaid invoices (is_paid = false)
- Supports date range filtering (start_date, end_date)
- Eager loads payments relationship

**`payPiutang(Request, Invoice)`**
- Records payment transaction
- Updates invoice paid_amount
- Marks is_paid = true when fully paid
- Preserves date filters on redirect

**`paidHistoryIndex()`**
- Shows all fully paid invoices
- Ordered by updated_at DESC

**`viewClosedRedirect()`** [MODIFIED: 2025-10-22]
- Shows Closed/Rejected SPKs needing price assignment
- Filters: `whereNull('price_per_meter') OR whereNull('harga_per_piece')`
- Used as redirect after closing/rejecting orders

**`printInvoice($invoiceIds)`**
- Accepts comma-separated invoice IDs
- Generates A5 format print view
- Can print single or multiple invoices

**`generateInvoiceNumber()`**
- Format: INV/MM/YYYY/XXXX
- Sequential per month/year
- Thread-safe with count + 1 logic

### 4. RekapController (Admin Only)
**Location**: `app/Http/Controllers/RekapController.php`

**Middleware**: Checks `is_admin` before allowing access

#### `omzet(Request)` - Revenue Report
**Cards** (filtered by date range):
- Completed orders count
- Total revenue sum
- Total quantity
- Total meters

**Table**: All paid invoices within date range

**Chart**: 12-month revenue trend (last year to current month)

#### `reject(Request)` - Reject Report
**Cards** (current month only):
- Rejected orders count
- Total nominal (total_meter × price_per_meter)
- Total quantity
- Total meters

**Table**: Rejected SPKs within date range

**Chart**: 12-month reject trend

---

## 🚀 RECENT CHANGES (2025-10-22)

### Migration Added
**File**: `database/migrations/2025_10_22_073327_add_harga_per_piece_to_spks_table.php`
- Adds `harga_per_piece` column (nullable, decimal 10,2) to spks table
- **Status**: File exists but may not be migrated yet (untracked in git)

### Controller Changes

#### InvoiceController.php
1. **`savePrice()` method**:
   - Changed from single price to dual pricing
   - Validates at least one price is filled
   - Accepts both `price_per_meter` and `harga_per_piece`

2. **`publishInvoice()` method**:
   - Complete rewrite for dual-pricing support
   - Logic to create 1 or 2 invoices based on available prices
   - Returns array of invoice IDs instead of single ID
   - Appends "(MTR)" or "(QTY)" to order names

3. **`viewClosedRedirect()` method**:
   - Query changed to check for missing prices using OR condition
   - Shows SPKs needing either price_per_meter OR harga_per_piece

### View Changes

#### spk-close.blade.php
**Purpose**: Admin page to assign prices to closed SPKs

**Changes**:
- Added second input field for `harga_per_piece`
- Added column "Harga @pieces" in table
- Modal displays both prices
- JavaScript validation updated:
  - Requires at least one price (not both)
  - Updates both modal fields with current values
  - FormData includes both prices if filled
- Filter logic: `empty($spk->price_per_meter) || empty($spk->harga_per_piece)`
- Success notification text changed from "kain" to generic "harga"

#### invoice.blade.php
**Purpose**: Invoice publishing page

**Changes**:
- Added "Harga @ QTY/Piece" column
- Displays both prices in separate columns
- "Nilai" column shows:
  - Both calculated values when both prices exist
  - Single value when only one price exists
- Changed from checkboxes to radio buttons (select only one SPK at a time)
- Enhanced AJAX handling for multiple invoice IDs
- Opens multiple print tabs when both invoices created
- Auto-refreshes table after successful publish

#### spkClose-view.blade.php
**Purpose**: View-only page for closed/rejected SPKs

**Changes**:
- Price columns commented out (no longer displayed)
- Simplified view without pricing details

---

## ⚠️ KNOWN ISSUES & OBSERVATIONS

### 1. Invoice-SPK Relationship Mismatch
**Problem**: Spk model defines `hasOne(Invoice)` relationship, but with dual-pricing, one SPK can now generate 2 invoices.

**Impact**: 
- `$spk->invoice` will only return first invoice
- Querying `$spk->invoice` may be unpredictable
- Reports may miss the second invoice

**Recommendation**: Change to `hasMany(Invoice)` relationship

### 2. Invoice Filtering Logic
**Current**: `invoiceIndex()` filters with `whereDoesntHave('invoice')`
**Problem**: After creating first invoice, SPK won't appear even if second invoice needs creation
**Potential Fix**: Check if invoice count matches expected invoice count based on pricing

### 3. Data Integrity Concerns
- Two invoices from same SPK can have different payment statuses
- One invoice might be paid, another unpaid
- Reporting needs careful handling to avoid double-counting SPKs

### 4. View Filter Inconsistency ✅ FIXED (Session 2)
~~`viewClosedRedirect()` query used OR condition~~
**Status**: Fixed in Session 2
**Solution**: Changed to AND-based nested query requiring BOTH prices to be empty

### 5. User Association Not Fully Implemented
- `spks` table has `user_id` column
- Migration exists to add it
- But SpkController doesn't set it during create/update

### 6. Migration Not Run
`2025_10_22_073327_add_harga_per_piece_to_spks_table.php` exists but may not be migrated.
**Action Required**: Run `php artisan migrate`

---

## 📂 PROJECT STRUCTURE

```
dashboard-konveksi/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── DashboardController.php
│   │       ├── SpkController.php
│   │       ├── InvoiceController.php [MODIFIED]
│   │       ├── RekapController.php
│   │       └── ProfileController.php
│   └── Models/
│       ├── Spk.php
│       ├── SpkSize.php
│       ├── Invoice.php
│       ├── Payment.php
│       └── User.php
├── database/
│   ├── migrations/ (12 total)
│   │   └── 2025_10_22_073327_add_harga_per_piece_to_spks_table.php [NEW]
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── AdminUserSeeder.php
│       └── DummyDataSeeder.php
├── resources/
│   └── views/
│       ├── layouts/ (app, dashboard)
│       ├── spk.blade.php
│       ├── spk_print.blade.php
│       ├── spk-close.blade.php [MODIFIED]
│       ├── spkClose-view.blade.php [MODIFIED]
│       ├── invoice.blade.php [MODIFIED]
│       ├── invoice-print.blade.php
│       ├── piutang.blade.php
│       ├── rekap-omzet.blade.php
│       └── rekap-reject.blade.php
├── routes/
│   └── web.php
├── .env [MODIFIED - APP_KEY changed]
├── docker-compose.yml
├── Dockerfile
└── README.md
```

---

## 🎨 TECHNOLOGY STACK

**Backend**:
- Laravel 12.x (PHP 8.2+)
- MySQL 8.0 (Docker)
- Redis 7-alpine

**Frontend**:
- Blade Templates
- Bootstrap 5.3.3
- Alpine.js
- DataTables 2.3.4 (for all list views)
- jQuery 3.7.1
- Chart.js (for reports)

**Build Tools**:
- Vite
- Tailwind CSS
- PostCSS

**Development**:
- Docker Compose (dev & production)
- phpMyAdmin 5.2.1

---

## 🛣️ ROUTES SUMMARY

### Public Routes
- `/` → Dashboard (home)
- `/dashboard` → Dashboard

### SPK Management
- GET `/spk` → Create form
- POST `/spk` → Store new SPK
- GET `/spk/{spk}/edit` → Edit form
- PUT `/spk/{spk}` → Update SPK
- POST `/spk/{spk}/status` → Update status/progress
- GET `/spk/{spk}/print` → Print SPK (A4)
- GET `/spkClose-view` → View closed SPKs
- GET `/api/spk-data` → JSON API for dashboard

### Invoice & Finance (Auth Required)
- GET `/spk-close` → Admin price assignment page
- POST `/spk-close/{spk}/save-price` → Save prices (JSON)
- GET `/invoice` → Invoice publishing page
- GET `/invoice/{invoice}` → Show single invoice
- POST `/invoice/publish` → Publish invoice(s)
- GET `/invoice/history` → Paid invoices
- GET `/invoice/print/{invoiceIds}` → Print invoice(s) (A5)
- GET `/piutang` → Accounts receivable
- POST `/piutang/{invoice}/pay` → Record payment

### Reports (Admin Only)
- GET `/rekap-omzet` → Revenue report
- GET `/rekap-reject` → Reject report

### Profile
- GET `/profile` → Profile edit
- PATCH `/profile` → Update profile
- DELETE `/profile` → Delete account
- POST `/user/change-password` → Change password

---

## 🔐 SECURITY FEATURES

1. **CSRF Protection** - All forms include `@csrf` token
2. **Role-Based Access Control** - `is_admin` check for reports
3. **Database Transactions** - Atomic operations for SPK/Invoice creation
4. **Database Locking** - `lockForUpdate()` for number generation (prevents race conditions)
5. **Input Validation** - All user inputs validated
6. **File Upload Validation** - Type (jpeg,png,jpg,gif), size (max 2MB)
7. **Authentication Required** - Sensitive routes protected by auth middleware
8. **SQL Injection Protection** - Eloquent ORM, parameterized queries
9. **XSS Protection** - Blade auto-escapes output

---

## 📝 QUICK REFERENCE

### Default Credentials
- **Admin**: `admin@example.com` / `admin2` (username: adminadmin)
- **Test User**: `test@example.com` / `password`

### Company Info (from .env)
```
COMPANY_NAME="CV. Dashboard Konveksi"
COMPANY_ADDRESS="JL. Mochammad Toha No. 123"
COMPANY_PHONE="Telp. 021-12345678"
COMPANY_EMAIL="admin@dashboard-konveksi.com"
COMPANY_NPWP="NPWP: 123.456.789.0.123.000"
```

### Key Commands
```bash
# Development
php artisan serve
npm run dev

# Database
php artisan migrate
php artisan db:seed

# Docker
./deploy-docker.sh --rebuild-dev
./deploy-docker.sh --rebuild-prod
docker compose down

# Testing
php artisan test

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Docker Services
- **App**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
  - Server: db
  - Username: laravel_user
  - Password: laravel_password

---

## 📊 GIT STATUS (2025-10-27)

### Modified Files (Unstaged)
1. `.env` - APP_KEY changed
2. `app/Http/Controllers/InvoiceController.php` - Dual pricing logic
3. `resources/views/invoice.blade.php` - Dual pricing display
4. `resources/views/spk-close.blade.php` - Dual pricing inputs
5. `resources/views/spkClose-view.blade.php` - Simplified view

### Untracked Files
1. `database/migrations/2025_10_22_073327_add_harga_per_piece_to_spks_table.php`

### Recent Commits
```
bec38b3 - datatables view fixed
fae6ed5 - update to datatables for table views
84027c5 - Update configuration files, deployment docs, and Docker setup
696be6d - Fix pagination info functions in multiple views
4501eda - Fix pagination count display in piutang and spkClose-view
```

---

## 🔄 SESSION HISTORY

### Session 1: 2025-10-27 (Initial Analysis)
**User Request**: "analyze my projects from start to finish. check the corresponding databases, controller, models and etc. dont change anything yet until i ask"

**Actions Taken**:
1. Comprehensive project analysis
2. Read all models, controllers, migrations, seeders
3. Examined routes and views
4. Checked git status and diffs
5. Identified recent dual-pricing feature implementation
6. Documented all findings in this memo.md

**Key Findings**:
- Dual-pricing feature recently implemented (harga_per_piece)
- Invoice generation logic supports creating 2 invoices from 1 SPK
- Several potential issues identified (relationship mismatch, filtering logic)
- Migration exists but may not be run yet

**Next Steps** (Awaiting User Instructions):
- Run migration if not executed
- Fix Invoice-SPK relationship (hasOne → hasMany)
- Adjust invoice filtering logic
- Test dual-invoice creation workflow
- Consider business rules for price editing

---

### Session 2: 2025-10-27 (SPK Filtering Logic Fix)
**User Request**: "Fix spk-close.blade.php so that SPK row disappears when ANY price is saved (not requiring both)"

**Issue 1: SPK Visibility Filter**

**Problem Identified**:
- **Blade View**: Used `||` (OR) condition - SPK shown if either price is empty
- **Controller**: Used `orWhereNull()` - SPK fetched if either price is NULL/0
- **Result**: SPK remained visible even after saving one price

**Root Cause**:
```php
// WRONG (Previous logic):
return empty($spk->price_per_meter) || empty($spk->harga_per_piece);
// Shows SPK if price_per_meter is empty OR harga_per_piece is empty

// CORRECT (Fixed logic):
return empty($spk->price_per_meter) && empty($spk->harga_per_piece);
// Shows SPK ONLY if BOTH prices are empty
```

**Files Modified**:
1. **resources/views/spk-close.blade.php** (Line 51)
   - Changed from: `return empty($spk->price_per_meter) || empty($spk->harga_per_piece);`
   - Changed to: `return empty($spk->price_per_meter) && empty($spk->harga_per_piece);`

2. **app/Http/Controllers/InvoiceController.php** (`viewClosedRedirect()` method, Lines 330-338)
   - Changed from OR-based query to AND-based nested query
   - Before: Shows if price_per_meter NULL OR harga_per_piece NULL
   - After: Shows ONLY if BOTH are NULL or 0
   
**SQL Logic Change**:
```sql
-- BEFORE (Wrong):
WHERE (price_per_meter IS NULL 
   OR harga_per_piece IS NULL 
   OR price_per_meter = 0 
   OR harga_per_piece = 0)

-- AFTER (Correct):
WHERE ((price_per_meter IS NULL OR price_per_meter = 0)
   AND (harga_per_piece IS NULL OR harga_per_piece = 0))
```

**Expected Behavior After Fix**:
- SPK appears in spk-close table ONLY when BOTH prices are empty/0
- Once user saves ANY price (meter OR piece), SPK disappears immediately
- SPK won't reappear unless both prices are cleared

**Status**: ✅ Fixed and documented

---

**Issue 2: Invoice Number Format**

**User Request**: "Change invoice number from `INV/MM/YYYY/XXXX` to `INV/MTR/MM/YYYY/XXXX` or `INV/QTY/MM/YYYY/XXXX` based on pricing type"

**Problem Identified**:
- Invoice numbers didn't distinguish between meter-based and piece-based invoices
- All invoices used generic `INV/MM/YYYY/XXXX` format
- Difficult to identify invoice type in piutang (accounts receivable) view

**Root Cause**:
```php
// BEFORE (Previous logic):
private function generateInvoiceNumber()
{
    $count = Invoice::whereYear('created_at', $year)
        ->whereMonth('created_at', $month)->count() + 1;
    return "INV/{$month}/{$year}/" . str_pad($count, 4, '0', STR_PAD_LEFT);
}
// All invoices got same format regardless of pricing type

// AFTER (Fixed logic):
private function generateInvoiceNumber($type = 'MTR')
{
    return DB::transaction(function () use ($type) {
        $pattern = "INV/{$type}/{$month}/{$year}/%";
        $count = Invoice::where('invoice_number', 'LIKE', $pattern)
            ->lockForUpdate()
            ->count() + 1;
        return "INV/{$type}/{$month}/{$year}/" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }, 5);
}
// Now generates type-specific invoice numbers
```

**Files Modified**:
1. **app/Http/Controllers/InvoiceController.php** (`generateInvoiceNumber()` method)
   - Added `$type` parameter (default: 'MTR')
   - Changed to use DB transaction with lockForUpdate() for thread safety
   - Uses LIKE pattern to count invoices of same type
   - Format: `INV/{type}/{month}/{year}/{sequential}`

2. **app/Http/Controllers/InvoiceController.php** (`publishInvoice()` method)
   - Updated all 4 calls to `generateInvoiceNumber()`
   - Meter-only pricing: `generateInvoiceNumber('MTR')`
   - Piece-only pricing: `generateInvoiceNumber('QTY')`
   - Both pricing: Separate calls for MTR and QTY

**Invoice Number Examples**:
- Meter-based: `INV/MTR/10/2025/0001`, `INV/MTR/10/2025/0002`
- Piece-based: `INV/QTY/10/2025/0001`, `INV/QTY/10/2025/0002`
- Mixed (both): Creates both `INV/MTR/...` and `INV/QTY/...`

**Sequential Numbering**:
- MTR and QTY invoices have **separate** sequential counters
- Both reset monthly
- Thread-safe with `lockForUpdate()`

**Expected Behavior**:
- **In piutang.blade.php**: Invoice numbers now display as `INV/MTR/...` or `INV/QTY/...`
- **Easy identification**: Can tell at a glance whether invoice is for material or finished product
- **Separate sequences**: MTR-0001, MTR-0002, QTY-0001, QTY-0002 (independent)

**Status**: ✅ Fixed and documented

---

**Issue 3: Invoice Table Row Removal**

**User Request**: "Make invoice table rows disappear after publishing, just like spk-close table does"

**Problem Identified**:
- In `spk-close.blade.php`, rows fade out and disappear immediately after saving price
- In `invoice.blade.php`, after publishing invoice, page reloads and row still visible momentarily
- Inconsistent UX between the two pages

**Solution Implemented**:
Added identical row removal logic with fade-out effect in `invoice.blade.php`.

**Files Modified**:
1. **resources/views/invoice.blade.php**
   - Added `id="spk-row-{{ $spk->id }}"` to table rows for targeting
   - Added `data-spk-id="{{ $spk->id }}"` to radio input for easy reference
   - Implemented fade-out and remove logic in all 3 success response handlers
   - Changed from page reload to smooth row removal
   - Added "no data" message when table becomes empty
   - Changed alert auto-dismiss from 3s to 5s for better UX

**Implementation Details**:
```javascript
// Get selected SPK ID
const selectedSpkId = selectedRadio.value;

// Fade out and remove row
const row = document.getElementById('spk-row-' + selectedSpkId);
if (row) {
    row.style.transition = 'opacity 0.5s';
    row.style.opacity = '0';
    setTimeout(() => {
        row.remove();
        // Check if table empty, show "no data" message
        if (tableBody.querySelectorAll('tr').length === 0) {
            // Insert "Belum ada SPK..." message
        }
    }, 500);
}
```

**Response Handlers Updated**:
1. `data.redirect` - Single invoice with redirect to print
2. `data.invoice_ids` - Multiple invoices (MTR + QTY)
3. `data.message || data.success` - Generic success response

**Expected Behavior**:
- ✅ User clicks "Publish & Print"
- ✅ Invoice(s) created and print tabs open
- ✅ Selected SPK row fades out (0.5s transition)
- ✅ Row removed from DOM after fade
- ✅ Success alert shows for 5 seconds then auto-dismisses
- ✅ No page reload needed
- ✅ If last row, shows "Belum ada SPK yang siap diterbitkan invoice."

**Status**: ✅ Fixed and documented

---

**Issue 4: Dynamic Invoice Print Column Headers**

**User Request**: "Make 'HARGA @ Meter(Rp)' column dynamic - show 'HARGA @ METER' for MTR invoices and 'HARGA @ PIECE' for QTY invoices"

**Problem Identified**:
- `invoice-print.blade.php` had static column header "HARGA @METER (Rp)"
- Showed incorrect header for piece-based (QTY) invoices
- Price value always showed `price_per_meter`, even for QTY invoices

**Solution Implemented**:
Dynamic column headers and values based on invoice type detection.

**Files Modified**:
1. **resources/views/invoice-print.blade.php**
   - Added PHP logic to detect invoice type from `invoice_number`
   - Dynamic column header based on invoice type
   - Dynamic price value display

**Implementation Details**:
```php
@php
    // Detect invoice type from invoice_number format
    $isMeterBased = str_contains($invoice->invoice_number, '/MTR/');
    $isPieceBased = str_contains($invoice->invoice_number, '/QTY/');
@endphp

// Dynamic column header
@if($isMeterBased)
    <th>HARGA @METER (Rp)</th>
@elseif($isPieceBased)
    <th>HARGA @PIECE (Rp)</th>
@else
    <th>HARGA SATUAN (Rp)</th>
@endif

// Dynamic price value
@if($isMeterBased)
    {{ $invoice->spk->price_per_meter }}
@elseif($isPieceBased)
    {{ $invoice->spk->harga_per_piece }}
@endif
```

**Invoice Type Detection**:
- Checks `invoice_number` format
- **MTR Invoice**: Contains `/MTR/` → Shows "HARGA @METER" + `price_per_meter`
- **QTY Invoice**: Contains `/QTY/` → Shows "HARGA @PIECE" + `harga_per_piece`
- **Fallback**: Shows "HARGA SATUAN" (for old invoices)

**Expected Behavior**:

| Invoice Type | Invoice Number | Column Header | Price Shown |
|--------------|---------------|---------------|-------------|
| Meter-based | INV/MTR/10/2025/0001 | HARGA @METER (Rp) | price_per_meter |
| Piece-based | INV/QTY/10/2025/0001 | HARGA @PIECE (Rp) | harga_per_piece |
| Old format | INV/10/2025/0001 | HARGA SATUAN (Rp) | Either price |

**Example Print Outputs**:

**MTR Invoice:**
```
| NO | NAMA BARANG | QTY | METER | HARGA @METER (Rp) | TOTAL (Rp) |
|----|-------------|-----|-------|-------------------|------------|
| 1  | Kaos (MTR)  | 100 | 45.5  | 50,000            | 2,275,000  |
```

**QTY Invoice:**
```
| NO | NAMA BARANG | QTY | METER | HARGA @PIECE (Rp) | TOTAL (Rp) |
|----|-------------|-----|-------|-------------------|------------|
| 1  | Kaos (QTY)  | 100 | 45.5  | 15,000            | 1,500,000  |
```

**Status**: ✅ Fixed and documented

---

## 📌 NOTES & REMINDERS

### Business Rules Observed
1. SPK status flow: In Progress → Closed/Rejected
2. Cannot close/reject SPK unless is_print_done = true and total_meter > 0
3. Prices should be immutable once saved (enforced in UI, not DB)
4. Invoice numbers are sequential per month
5. Payments can be partial or full
6. Only admin can view reports (Rekap Omzet, Rekap Reject)

### DataTables Configuration
All tables use consistent configuration:
- pageLength: 10
- Indonesian language translations
- Responsive design
- Search, sort, pagination enabled

### File Upload Storage
- Design images: `storage/app/public/spk_designs/`
- Accessible via: `/storage/spk_designs/{filename}`
- Requires: `php artisan storage:link`

### Printing Formats
- SPK: A4 format (landscape recommended)
- Invoice: A5 format (thermal printer compatible)

---

## 🎯 FUTURE ENHANCEMENTS (Potential)

Based on DOCUMENTATION.md and project structure:
1. Automated file organization by date
2. Database optimization with indexing
3. Enhanced logging for critical operations
4. Batch invoice generation
5. Email notifications for deadlines
6. Real-time progress updates (WebSockets)
7. Export to PDF/Excel for reports
8. Barcode/QR code for SPK tracking
9. Multi-user permission levels (beyond admin/user)
10. API for mobile app integration

---

## 🐛 DEBUGGING TIPS

### Common Issues
1. **SPK number duplicate**: Check if DB transaction is working, ensure lockForUpdate() is used
2. **Invoice not appearing**: Check if invoice relationship is loaded, verify is_paid status
3. **Payment not updating**: Check if paid_amount is recalculated after payment
4. **Chart not showing**: Verify data range, check if Chart.js is loaded
5. **Image not displaying**: Run `php artisan storage:link`

### Log Locations
- Laravel logs: `storage/logs/laravel.log`
- Docker logs: `docker compose logs -f app`

---

### How to push to this md
- git commit message to AI memory

**END OF MEMO**

*This memo serves as persistent knowledge base for AI assistant across sessions. Update this file whenever significant changes are made to the project.*
