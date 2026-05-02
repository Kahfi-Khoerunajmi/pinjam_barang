# 📦 Aplikasi Pinjaman Barang - Setup Guide

Aplikasi web pinjaman barang dengan Laravel yang dilengkapi sistem manajemen penuh, notifikasi, laporan, dan dashboard admin.

## ⚡ Quick Start

### 1. Persiapan Database

Buat database di MySQL:
```sql
CREATE DATABASE pinjam_barang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Setup Environment (.env)

```bash
# Copy template env
cp .env.example .env

# Generate key
php artisan key:generate
```

Edit `.env`:
```env
APP_NAME="Pinjam Barang"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pinjam_barang
DB_USERNAME=root
DB_PASSWORD=

# Mail Configuration (Optional untuk development)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

### 3. Install Dependencies

```bash
# PHP dependencies
composer install

# NPM dependencies
npm install
```

### 4. Database Migration & Seeding

```bash
# Run migrations
php artisan migrate

# Seed database dengan data dummy (optional)
php artisan db:seed
```

### 5. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 6. Run Application

Buka 3 terminal terpisah:

**Terminal 1 - Web Server:**
```bash
php artisan serve
```
Akses di http://localhost:8000

**Terminal 2 - Asset Watch (optional untuk development):**
```bash
npm run dev
```

**Terminal 3 - Queue Worker (untuk email notifications):**
```bash
php artisan queue:listen
```

---

## 🔐 Default Credentials (Seeded)

| Role  | Email                    | Password |
|-------|--------------------------|----------|
| Admin | admin@pinjambarang.test  | password |
| User  | user1@pinjambarang.test  | password |
| User  | user2@pinjambarang.test  | password |
| ... dan 3 user lainnya      | password |

---

## 📋 Fitur Utama

### 👤 User Features
- ✅ Dashboard dengan statistik peminjaman
- ✅ Browse dan cari barang
- ✅ Pinjam barang dengan validasi otomatis
- ✅ Tracking peminjaman aktif
- ✅ Riwayat peminjaman lengkap
- ✅ Notifikasi email & in-app
- ✅ Kembalikan barang dan konfirmasi

### 👨‍💼 Admin Features
- ✅ Kelola barang (CRUD)
- ✅ Kelola kategori barang
- ✅ Dashboard statistik real-time
- ✅ Daftar peminjaman aktif & terlambat
- ✅ Status management peminjaman
- ✅ Laporan peminjaman dengan filter
- ✅ Export ke PDF & Excel
- ✅ Monitoring barang
- ✅ Manajemen user (dengan Spatie Permission)

---

## 🗂️ Struktur File Penting

```
app/
├── Http/Controllers/
│   ├── DashboardController.php    # Dashboard logic
│   ├── ItemController.php         # Barang management
│   ├── LoanController.php         # Peminjaman management
│   └── ReportController.php       # Laporan & export
├── Models/
│   ├── User.php
│   ├── Item.php
│   ├── Loan.php
│   ├── Category.php
│   └── Notification.php
├── Services/
│   ├── LoanService.php            # Business logic peminjaman
│   ├── NotificationService.php    # Notifikasi
│   └── ReportService.php          # Laporan
├── Mail/
│   ├── LoanConfirmationMail.php
│   ├── LoanReminderMail.php
│   └── LateReturnMail.php
├── Jobs/
│   ├── CheckLateReturnsJob.php
│   └── SendLoanReminderJob.php
├── Policies/
│   ├── ItemPolicy.php
│   └── LoanPolicy.php
└── Traits/
    ├── GeneratesCode.php
    └── HandlesStatus.php

database/
├── migrations/          # Skema database
└── seeders/            # Data dummy

resources/views/
├── dashboard/          # Dashboard views
├── items/             # Item management views
├── loans/             # Loan management views
├── reports/           # Report views
└── mails/             # Email templates
```

---

## 🚀 Workflow Peminjaman

```
1. User melihat daftar barang
                ↓
2. User klik "Pinjam" pada barang yang tersedia
                ↓
3. System validasi: barang harus "tersedia"
                ↓
4. User mengisi form:
   - Tanggal kembali rencana (min 1 hari)
   - Catatan (optional)
                ↓
5. Sistem generate kode peminjaman unique
   Format: LN-YYYYMMDDHHiiss-XXXX
                ↓
6. Update status item → "dipinjam"
                ↓
7. Email konfirmasi → User
                ↓
8. In-app notification → User
                ↓
9. Admin bisa melihat & mengkonfirmasi
                ↓
10. User mengisi form pengembalian
                ↓
11. System beri validasi:
    - Ketimbang vs tanggal rencana
    - Update status item → "tersedia"
                ↓
12. Email notifikasi pengembalian
```

---

## 📊 Fitur Laporan

### Laporan Peminjaman
```
Filter:
- Tanggal mulai & akhir
- User
- Status (dipinjam, dikembalikan, terlambat)

Export:
- PDF (Barryvdh/DomPDF)
- Excel (Maatwebsite/Excel)
- View online
```

### Dashboard Statistik
- Total barang
- Barang tersedia
- Barang sedang dipinjam
- Barang dalam perbaikan
- Barang hilang
- Total peminjaman aktif
- Peminjaman terlambat

### Laporan Khusus
- Barang paling sering dipinjam
- User paling aktif meminjam
- Tingkat keterlambatan
- Statistik bulanan

---

## 🔧 Configuration & Customization

### Setup Admin User

Jika perlu membuat admin baru:
```bash
php artisan tinker

# Di dalam tinker:
$admin = User::create([
    'name' => 'Admin Name',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'email_verified_at' => now(),
]);
$admin->assignRole('admin');
```

### Change Table Names

Edit di migration files jika ingin ubah nama tabel:
```php
Schema::create('nama_tabel_baru', function (Blueprint $table) {
    // ...
});
```

### Add Custom Status

Edit enum di migration `create_items_table.php` dan `create_loans_table.php`:
```php
$table->enum('status', ['tersedia', 'dipinjam', 'perbaikan', 'hilang', 'rusak']);
```

---

## 🐛 Troubleshooting

### Error: "Class 'Spatie\Permission\Models\Role' not found"

```bash
# Publish Spatie Permission config
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Run migrations
php artisan migrate
```

### Error: "SQLSTATE[42S02]: Table 'pinjam_barang.roles' doesn't exist"

Pastikan migration Spatie Permission sudah di-run:
```bash
php artisan migrate
```

### Email tidak terkirim?

1. Check `.env` MAIL configuration
2. Cek log: `storage/logs/laravel.log`
3. Test mail: `php artisan tinker` → `Mail::to('test@example.com')->send(new TestMail())`
4. Queue harus berjalan: `php artisan queue:listen`

### File upload tidak berfungsi?

```bash
# Setup symbolic link untuk storage
php artisan storage:link

# Set permissions
chmod -R 775 storage/
chmod -R 755 public/storage
```

---

## 📚 API Routes

### Items
```
GET  /items              Daftar barang
GET  /items/{id}         Detail barang
POST /items              Buat barang (Admin)
PUT  /items/{id}         Update barang (Admin/Creator)
DEL  /items/{id}         Hapus barang (Admin/Creator)
```

### Loans
```
GET  /loans              Daftar peminjaman
GET  /loans/{id}         Detail peminjaman
POST /loans              Buat peminjaman
PUT  /loans/{id}/return  Kembalikan barang
GET  /my-loans           Peminjaman saya
```

### Reports
```
GET  /reports            Dashboard laporan
POST /reports/generate-loans    Generate laporan
GET  /reports/overdue    Daftar terlambat
```

---

## 📱 Mobile Responsiveness

Aplikasi sudah fully responsive dengan Bootstrap 5:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (< 768px)

---

## 🔒 Security Features

- ✅ CSRF Protection
- ✅ Password Hashing (bcrypt)
- ✅ SQL Injection Prevention (Eloquent ORM)
- ✅ Authorization Policies
- ✅ Role-based Access Control
- ✅ Email Verification (optional)

---

## 🎓 Next Steps

1. **Customize Design:** Edit di `resources/views`
2. **Add More Features:** Dashboard charts, SMS notifications, etc.
3. **Setup Email:** Configure SMTP untuk production
4. **Deploy:** Ke hosting (Heroku, Digital Ocean, etc.)
5. **Optimize:** Caching, pagination, queue optimization

---

## 📖 Dokumentasi Lengkap

Lihat file [DOKUMENTASI.md](./DOKUMENTASI.md) untuk dokumentasi lengkap tentang:
- Struktur database detailed
- Model relationships
- Service classes
- Controllers
- Views structure
- Testing
- dan lebih banyak lagi

---

## 💪 Support & Contribution

Jika ada pertanyaan atau ingin contribute:
1. Buat issue
2. Submit pull request
3. Hubungi tim development

---

## 📄 License

MIT License - Bebas digunakan untuk keperluan apapun

---

**Last Updated: 11 Februari 2026**
