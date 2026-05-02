# 📦 Aplikasi Pinjaman Barang - Dokumentasi Lengkap

## 🎯 Overview

Aplikasi web pinjaman barang dengan Laravel yang menyediakan sistem manajemen peminjaman barang yang komprehensif dengan fitur-fitur advanced seperti:

- ✅ Sistem Autentikasi & Autorisasi
- ✅ Manajemen Barang dengan Kategori
- ✅ Tracking Peminjaman Lengkap
- ✅ Laporan & Export Data (PDF/Excel)
- ✅ Dashboard Admin dengan Statistik
- ✅ Sistem Notifikasi Email & In-App
- ✅ Background Jobs untuk Automasi

---

## 🏗️ Struktur Database

### Users Table (dari Laravel Breeze)
```
- id (Primary Key)
- name
- email (Unique)
- email_verified_at
- password
- remember_token
- created_at, updated_at
```

### Categories Table
```
- id
- nama_kategori
- deskripsi (nullable)
- created_at, updated_at
```

### Items Table
```
- id
- nama_barang
- kode_barang (Unique)
- deskripsi (nullable)
- category_id (Foreign Key)
- lokasi (nullable)
- status: ['tersedia', 'dipinjam', 'perbaikan', 'hilang']
- gambar (nullable)
- created_by (Foreign Key - User)
- created_at, updated_at
- Indexes: kode_barang, status
```

### Loans Table
```
- id
- kode_peminjaman (Unique)
- user_id (Foreign Key)
- item_id (Foreign Key)
- tanggal_pinjam
- tanggal_kembali_rencana
- tanggal_kembali_aktual (nullable)
- status: ['dipinjam', 'dikembalikan', 'terlambat', 'hilang']
- catatan (nullable)
- dikonfirmasi_oleh (Foreign Key - User, nullable)
- created_at, updated_at
- Indexes: kode_peminjaman, status, user_id, item_id
```

### Notifications Table
```
- id
- user_id (Foreign Key)
- title
- message
- type: ['loan', 'reminder', 'late', 'general']
- related_id (nullable)
- is_read (boolean)
- created_at, updated_at
- Indexes: user_id, is_read, type
```

---

## 📊 Model Relationships

```
User
  ├─ hasMany: loans (sebagai peminjam)
  ├─ hasMany: items (jika admin)
  └─ hasMany: confirmedLoans (jika admin - sebagai penegas)

Category
  └─ hasMany: items

Item
  ├─ belongsTo: category
  ├─ hasMany: loans
  └─ belongsTo: creator (User)

Loan
  ├─ belongsTo: user (peminjam)
  ├─ belongsTo: item
  ├─ belongsTo: admin (penegas, nullable)
  └─ Scopes: active(), overdue(), returned(), betweenDates()

Notification
  ├─ belongsTo: user
  └─ belongsTo: loan (optional)
```

---

## 🎮 Fitur Utama

### 1. **Autentikasi & Autorisasi**
- Menggunakan Spatie Laravel Permission
- User roles: `admin`, `user` (default sebagai user)
- Policies untuk mengontrol akses ke resources

**File Kunci:**
- `app/Policies/ItemPolicy.php`
- `app/Policies/LoanPolicy.php`

### 2. **Manajemen Barang (Items)**
- Create: Admin only
- Read: Public
- Update: Admin atau creator
- Delete: Admin atau creator
- Status tracking: tersedia, dipinjam, perbaikan, hilang
- Gambar support dengan file upload

**File Kunci:**
- `app/Http/Controllers/ItemController.php`
- `app/Models/Item.php`
- Traits: `GeneratesCode`, `HandlesStatus`

### 3. **Sistem Peminjaman (Loans)**
- User dapat meminjam barang yang tersedia
- Validasi otomatis: barang harus tersedia
- Tracking timeline peminjaman
- Status management: dipinjam → dikembalikan atau terlambat
- Generate unique kode peminjaman otomatis

**File Kunci:**
- `app/Http/Controllers/LoanController.php`
- `app/Models/Loan.php`
- `app/Services/LoanService.php`

### 4. **Laporan & Export**
- Export ke PDF dengan DomPDF
- Export ke Excel dengan Laravel-Excel
- Laporan: Daily, Monthly, Yearly
- Filter by date range
- Statistik lengkap

**File Kunci:**
- `app/Http/Controllers/ReportController.php`
- `app/Services/ReportService.php`
- `app/Exports/LoansExport.php`, `UsersExport.php`, `ItemsExport.php`

### 5. **Dashboard**
- **Admin:** Statistik, charts, daftar peminjaman aktif, daftar yang terlambat
- **User:** Peminjaman aktif saya, pengingat pengembalian, histor

i

**File Kunci:**
- `app/Http/Controllers/DashboardController.php`
- `resources/views/dashboard/index.blade.php`

### 6. **Notifikasi**
- Email notifications: Confirmation, Reminder, Late Return
- In-app notifications dengan database
- Scheduled jobs untuk automasi

**File Kunci:**
- `app/Mail/LoanConfirmationMail.php`
- `app/Mail/LoanReminderMail.php`
- `app/Mail/LateReturnMail.php`
- `app/Jobs/CheckLateReturnsJob.php`
- `app/Jobs/SendLoanReminderJob.php`
- `app/Services/NotificationService.php`

---

## 🚀 Instalasi & Setup

### 1. **Persiapan Database**

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pinjam_barang
DB_USERNAME=root
DB_PASSWORD=
```

### 2. **Install Dependencies**

```bash
# Install composer packages
composer install

# Install npm packages
npm install

# Generate app key
php artisan key:generate
```

### 3. **Database Migration & Seeding**

```bash
# Run migrations
php artisan migrate

# (Optional) Seed data dummy
php artisan db:seed
```

### 4. **Konfigurasi Mail**

Edit `config/mail.php` untuk SMTP atau gunakan Mailtrap untuk development.

Atau set di `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. **Jalankan Aplikasi**

```bash
# Development
php artisan serve

# Watch asset files (terminal terpisah)
npm run dev

# Listen to queue jobs (terminal terpisah)
php artisan queue:listen
```

---

## 📚 API Endpoints

### Items
```
GET    /items              - Daftar barang dengan filter & search
POST   /items              - Buat barang baru (Admin)
GET    /items/{id}         - Detail barang
PUT    /items/{id}         - Update barang (Admin/Creator)
DELETE /items/{id}         - Hapus barang (Admin/Creator)
```

### Loans
```
GET    /loans              - Daftar semua peminjaman
POST   /loans              - Buat peminjaman baru
GET    /loans/{id}         - Detail peminjaman
PUT    /loans/{id}/return  - Kembalikan barang
GET    /my-loans           - Peminjaman aktif user saat ini
GET    /loans-history      - Riwayat peminjaman user
```

### Reports
```
GET    /reports            - Dashboard laporan (Admin)
POST   /reports/generate-loans   - Generate loan report
POST   /reports/generate-users   - Generate user report
POST   /reports/generate-items   - Generate item report
GET    /reports/overdue    - Daftar peminjaman terlambat
GET    /reports/monthly    - Statistik bulanan
```

---

## 🔧 Service Classes

### LoanService
**Methods:**
- `createLoan()` - Buat peminjaman baru dengan validasi
- `returnLoan()` - Kembalikan barang
- `getActiveLoans()` - Dapatkan peminjaman aktif
- `getOverdueLoans()` - Dapatkan peminjaman terlambat
- `getLoansNeedingReminder()` - Dapatkan peminjaman yang perlu reminder
- `calculateLoanDays()` - Hitung durasi peminjaman
- `calculateOverdueDays()` - Hitung keterlambatan

### NotificationService
**Methods:**
- `sendLoanConfirmation()` - Kirim konfirmasi peminjaman
- `sendReturnReminder()` - Kirim pengingat pengembalian
- `sendLateReturnNotification()` - Kirim notifikasi keterlambatan
- `createNotification()` - Buat in-app notification
- `getUnreadNotifications()` - Dapatkan notifikasi belum dibaca

### ReportService
**Methods:**
- `getDashboardStats()` - Statistik dashboard
- `getLoansReport()` - Laporan peminjaman
- `getMostBorrowedItems()` - Item paling sering dipinjam
- `getMostActiveUsers()` - User paling aktif meminjam
- `getMonthlyLoanStats()` - Statistik bulanan
- `getOverdueRate()` - Presentase keterlambatan

---

## 🎨 Views Structure

```
resources/views/
├── dashboard/
│   └── index.blade.php          (Dashboard utama)
├── items/
│   ├── index.blade.php          (Daftar barang)
│   ├── create.blade.php         (Form tambah barang)
│   ├── edit.blade.php           (Form edit barang)
│   └── show.blade.php           (Detail barang)
├── loans/
│   ├── index.blade.php          (Daftar peminjaman)
│   ├── create.blade.php         (Form pinjam)
│   ├── show.blade.php           (Detail peminjaman)
│   └── return.blade.php         (Form kembalikan)
├── reports/
│   ├── index.blade.php          (Dashboard laporan)
│   ├── loans.blade.php          (Laporan peminjaman)
│   ├── users.blade.php          (Laporan user)
│   ├── items.blade.php          (Laporan barang)
│   └── pdf/
│       ├── loans.blade.php      (Template PDF loans)
│       ├── users.blade.php      (Template PDF users)
│       └── items.blade.php      (Template PDF items)
├── mails/
│   ├── loan-confirmation.blade.php
│   ├── loan-reminder.blade.php
│   └── late-return.blade.php
└── layouts/
    └── app.blade.php
```

---

## 🔑 Permissions & Roles

### Mengatur Roles (Seeder)

```php
// Database Seeder
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$adminRole = Role::create(['name' => 'admin']);
$userRole = Role::create(['name' => 'user']);

// Admin can do everything
$adminRole->givePermissionTo('*');

// Create default admin user
$admin = User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password')
]);
$admin->assignRole('admin');
```

### Controllers & Authorization

```php
// Check role
if ($user->hasRole('admin')) { ... }

// Check permission
if ($user->hasPermissionTo('create_item')) { ... }

// Policy authorization
$this->authorize('create', Item::class);
$this->authorize('borrow', $item);
```

---

## 💬 Traits

### GeneratesCode
Auto-generate unique codes untuk:
- Barang: `BR-XXXXXXXX`
- Peminjaman: `LN-YYYYMMDDHHiiss-XXXX`

### HandlesStatus
Helper untuk display status:
- `getStatusLabel()` - Label display
- `getStatusBadgeClass()` - CSS class untuk badge
- `getStatusIcon()` - Icon FontAwesome
- `isActive()`, `isProblematic()`

---

## 📧 Email Templates

### Loan Confirmation
```
Subjek: Konfirmasi Peminjaman Barang
Isi: Barang berhasil dipinjam, kode: [kode_peminjaman]
```

### Loan Reminder
```
Subjek: Pengingat Pengembalian Barang
Isi: Barang harus dikembalikan pada [tanggal]
```

### Late Return
```
Subjek: Barang Terlambat Dikembalikan
Isi: Barang [item] sudah terlambat [X] hari
```

---

## 🤖 Background Jobs

### CheckLateReturnsJob
- Berjalan: Setiap hari (setup di `console.php`)
- Fungsi: Update peminjaman yang sudah melewati batas pengembalian

### SendLoanReminderJob
- Berjalan: Setiap hari
- Fungsi: Kirim reminder email untuk peminjaman yang dekat tanggal kembali

---

## 🧪 Testing

### Unit Tests
```bash
php artisan test
```

### Cek Error
```bash
php artisan lint
```

---

## 📦 Packages yang Digunakan

```json
{
  "require": {
    "laravel/framework": "^12.0",
    "laravel/breeze": "^2.3",
    "spatie/laravel-permission": "^6.24",
    "barryvdh/laravel-dompdf": "^3.1",
    "maatwebsite/excel": "^3.1",
    "intervention/image": "^3.11"
  }
}
```

---

## 🐛 Troubleshooting

### 1. Migration Error
```bash
# Drop all tables and re-create
php artisan migrate:reset
php artisan migrate
```

### 2. Permission Denied (File Upload)
```bash
# Set write permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### 3. Queue Not Running
```bash
# Check queue status
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Listen to queue (development)
php artisan queue:listen
```

### 4. Email Not Sending
```bash
# Test email configuration
php artisan tinker
Mail::to('test@example.com')->send(new TestMail());
```

---

## 📝 Development Checklist

- [x] Database migrations
- [x] Models & relationships
- [x] Controllers & services
- [x] Views & templates
- [x] Policies & authorization
- [x] Mail notifications
- [x] Background jobs
- [x] Routes
- [ ] Unit tests
- [ ] Integration tests
- [ ] API documentation (Swagger)
- [ ] Frontend improvements (UX)
- [ ] Performance optimization
- [ ] Security hardening

---

## 🎓 Next Steps (Optional)

1. **Calendar View** - Tampilkan jadwal peminjaman
2. **Multi-Image** - Multiple gambar per barang
3. **Rating System** - Rating kondisi barang
4. **SMS Notification** - Integrasi Twilio
5. **Mobile App** - React Native/Flutter
6. **Advanced Analytics** - Chart.js/ApexCharts
7. **API Public** - RESTful API untuk mobile
8. **Real-time Notifications** - WebSocket dengan Laravel Echo

---

## 📄 License

MIT License - Bebas digunakan untuk keperluan komersial maupun non-komersial.

---

**Dokumentasi dibuat: 11 Februari 2026**
