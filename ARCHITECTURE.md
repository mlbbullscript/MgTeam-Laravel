# ARCHITECTURE.md — Arsitektur Teknis Sistem

## Gambaran Tingkat Tinggi

```
Browser (User)
    │
    ▼
Vercel Edge (vercel-php runtime)
    │  Routes semua request ke index.php Laravel
    ▼
Laravel 11 Application
    ├── Middleware (Auth, RoleCheck, IzinUpload)
    ├── Controllers (request handling)
    ├── Services (business logic)
    ├── Models + Eloquent ORM
    │
    ▼
MySQL Database (filess.io)
```

---

## Skema Database Lengkap

### Tabel `users`
```sql
CREATE TABLE users (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50) UNIQUE NOT NULL,
    password      VARCHAR(255) NOT NULL,           -- bcrypt hash
    photo_profile VARCHAR(255) DEFAULT NULL,        -- path ke file
    role          ENUM('superadmin', 'rekan') NOT NULL DEFAULT 'rekan',
    coin_saham    DECIMAL(5,2) NOT NULL DEFAULT 0, -- max 100.00 total semua user
    is_active     BOOLEAN NOT NULL DEFAULT TRUE,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```
*Catatan: coin_saham pakai DECIMAL(5,2) agar bisa menyimpan nilai seperti 33.33.*

### Tabel `settings`
```sql
CREATE TABLE settings (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key_name   VARCHAR(100) UNIQUE NOT NULL,
    value      VARCHAR(255) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed data awal (wajib ada):
INSERT INTO settings (key_name, value) VALUES
    ('pct_saham', '50'),           -- persentase pool saham dari laba bersih
    ('pct_kerja', '50'),           -- persentase pool kerja dari laba bersih
    ('izin_upload_rekan', 'false'); -- izin upload laporan untuk rekan
```

### Tabel `financial_reports`
```sql
CREATE TABLE financial_reports (
    id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type           ENUM('pemasukan', 'pengeluaran') NOT NULL,
    name           VARCHAR(100) NOT NULL,
    description    TEXT DEFAULT NULL,
    amount         DECIMAL(15,2) NOT NULL,          -- nominal rupiah
    screenshot     VARCHAR(255) DEFAULT NULL,        -- path file screenshot
    created_by     BIGINT UNSIGNED NOT NULL,
    report_date    DATE NOT NULL,                    -- tanggal laporan (bukan created_at)
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at     TIMESTAMP DEFAULT NULL,           -- soft delete
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### Tabel `schedules`
```sql
CREATE TABLE schedules (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_name   VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    poin_kerja  DECIMAL(8,2) NOT NULL DEFAULT 0,
    assigned_to BIGINT UNSIGNED NOT NULL,   -- rekan atau superadmin
    schedule_date DATE NOT NULL,            -- tanggal spesifik jadwal
    created_by  BIGINT UNSIGNED NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### Tabel `overtime_tasks` (Tugas Lembur)
```sql
CREATE TABLE overtime_tasks (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    poin_kerja  DECIMAL(8,2) NOT NULL DEFAULT 0,
    status      ENUM('tersedia', 'diambil') NOT NULL DEFAULT 'tersedia',
    taken_by    BIGINT UNSIGNED DEFAULT NULL,   -- null jika belum diambil
    taken_at    TIMESTAMP DEFAULT NULL,
    created_by  BIGINT UNSIGNED NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (taken_by) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);
```

### Tabel `profit_distributions` (Riwayat Distribusi)
```sql
CREATE TABLE profit_distributions (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    distributed_by      BIGINT UNSIGNED NOT NULL,
    laba_bersih         DECIMAL(15,2) NOT NULL,
    pct_saham_used      DECIMAL(5,2) NOT NULL,   -- snapshot pct saat distribusi
    pct_kerja_used      DECIMAL(5,2) NOT NULL,
    total_pool_saham    DECIMAL(15,2) NOT NULL,
    total_pool_kerja    DECIMAL(15,2) NOT NULL,
    notes               TEXT DEFAULT NULL,
    distributed_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (distributed_by) REFERENCES users(id)
);
```

### Tabel `profit_distribution_details`
```sql
CREATE TABLE profit_distribution_details (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    distribution_id BIGINT UNSIGNED NOT NULL,
    user_id         BIGINT UNSIGNED NOT NULL,
    coin_saham      DECIMAL(5,2) NOT NULL,    -- snapshot saham saat distribusi
    poin_kerja      DECIMAL(8,2) NOT NULL,    -- snapshot poin kerja saat distribusi
    bagian_saham    DECIMAL(15,2) NOT NULL,
    bagian_kerja    DECIMAL(15,2) NOT NULL,
    total           DECIMAL(15,2) NOT NULL,
    status          ENUM('pending', 'ditransfer') NOT NULL DEFAULT 'pending',
    transferred_at  TIMESTAMP DEFAULT NULL,
    FOREIGN KEY (distribution_id) REFERENCES profit_distributions(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

## Kalkulasi Poin Kerja Per User

Poin kerja seorang user dihitung SECARA DINAMIS, bukan disimpan di tabel users, untuk menghindari inkonsistensi data:

```
Poin Kerja User X = 
    SUM(schedules.poin_kerja WHERE assigned_to = X AND schedule_date <= HARI_INI)
    + SUM(overtime_tasks.poin_kerja WHERE taken_by = X AND status = 'diambil')
```

Kalkulasi ini dilakukan via Eloquent relationship atau query di `ProfitService`.

---

## Struktur Folder Laravel

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   └── LoginController.php
│   │   ├── SuperAdmin/
│   │   │   ├── DashboardController.php
│   │   │   ├── RekanController.php
│   │   │   ├── ScheduleController.php
│   │   │   ├── OvertimeTaskController.php
│   │   │   ├── FinancialReportController.php
│   │   │   └── ProfitController.php
│   │   └── Rekan/
│   │       ├── DashboardController.php
│   │       ├── FinancialReportController.php
│   │       ├── ScheduleController.php
│   │       ├── OvertimeTaskController.php
│   │       ├── ProfitCalculatorController.php
│   │       ├── PersonalIncomeController.php
│   │       └── ProfileController.php
│   ├── Middleware/
│   │   ├── RoleMiddleware.php          -- cek role superadmin/rekan
│   │   └── IzinUploadMiddleware.php    -- cek setting izin_upload_rekan
│   └── Requests/
│       ├── StoreFinancialReportRequest.php
│       ├── StoreScheduleRequest.php
│       ├── StoreOvertimeTaskRequest.php
│       ├── UpdateProfileRequest.php
│       └── UpdateSahamRequest.php
│
├── Services/
│   ├── ProfitService.php       -- semua kalkulasi bisnis profit
│   ├── PoinKerjaService.php    -- kalkulasi total poin kerja
│   └── ReportService.php       -- filter & agregasi laporan keuangan
│
├── Models/
│   ├── User.php
│   ├── FinancialReport.php
│   ├── Schedule.php
│   ├── OvertimeTask.php
│   ├── ProfitDistribution.php
│   ├── ProfitDistributionDetail.php
│   └── Setting.php
│
resources/views/
├── layouts/
│   ├── app.blade.php           -- layout utama dengan sidebar
│   └── auth.blade.php          -- layout halaman login
├── components/
│   ├── sidebar.blade.php
│   ├── stat-card.blade.php     -- kartu statistik dashboard
│   ├── chart-card.blade.php    -- wrapper chart.js
│   └── alert.blade.php
├── auth/
│   └── login.blade.php
├── superadmin/
│   ├── dashboard.blade.php
│   ├── rekan/index, create, edit
│   ├── schedules/index, create, edit
│   ├── overtime/index, create, edit
│   ├── reports/index, create, edit
│   └── profit/index.blade.php
└── rekan/
    ├── dashboard.blade.php
    ├── reports/index.blade.php
    ├── schedules/index.blade.php
    ├── overtime/index.blade.php
    ├── profit-calculator.blade.php
    ├── personal-income.blade.php
    └── profile.blade.php
```

---

## Konfigurasi Vercel (`vercel.json`)

```json
{
    "version": 2,
    "functions": {
        "api/index.php": {
            "runtime": "vercel-php@0.7.2"
        }
    },
    "routes": [
        {
            "src": "/(.*)",
            "dest": "/api/index.php"
        }
    ]
}
```

File `api/index.php` adalah entry point yang me-load `public/index.php` Laravel. Tambahkan file ini ke `.gitignore` exclude list.

**Catatan Storage:** Karena Vercel filesystem bersifat ephemeral (file hilang setiap deployment), foto profil dan screenshot laporan sebaiknya disimpan sebagai base64 di database, atau menggunakan Cloudinary free tier. Keputusan ini didokumentasikan di DECISIONS.md.

---

## Routing Overview

```
POST   /login                           → Auth\LoginController@login
POST   /logout                          → Auth\LoginController@logout

-- SuperAdmin Routes (middleware: auth, role:superadmin)
GET    /superadmin/dashboard
GET    /superadmin/rekan
POST   /superadmin/rekan
GET    /superadmin/rekan/{id}/edit
PUT    /superadmin/rekan/{id}
DELETE /superadmin/rekan/{id}
-- ... (schedules, overtime, reports, profit — pola sama)
PATCH  /superadmin/settings/izin-upload
PATCH  /superadmin/settings/alokasi

-- Rekan Routes (middleware: auth, role:rekan)
GET    /rekan/dashboard
GET    /rekan/laporan-keuangan
POST   /rekan/laporan-keuangan          (middleware tambahan: izin-upload)
GET    /rekan/daftar-rekan
GET    /rekan/jadwal
GET    /rekan/lembur
POST   /rekan/lembur/{id}/ambil
GET    /rekan/kalkulator-profit
GET    /rekan/penghasilan
GET    /rekan/profil
PUT    /rekan/profil
```
