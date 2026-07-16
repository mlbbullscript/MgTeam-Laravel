# CONTEXT.md — Status & Kondisi Terkini Project

> File ini adalah "memori hidup" project. Agent WAJIB membaca ini di awal setiap sesi dan WAJIB memperbarui di akhir setiap sesi.
> Tulis dalam past tense — catat apa yang SUDAH terjadi, bukan rencana.

---

## Status Terkini

**Fase saat ini:** Fase 6 — Selesai & Teruji. Halaman login, CRUD lengkap, fungsionalitas Rekan, serta visualisasi grafik Chart.js dinamis (Pie chart saham, Line chart kas, & Line chart poin) telah fully implemented.
**Terakhir diupdate:** 2026-05-24
**Dikerjakan oleh sesi terakhir:** Menuntaskan Fase 5 (fitur operasional Rekan) & Fase 6 (Dashboard & Grafik). Mengintegrasikan Chart.js dengan data riil dari database menggunakan algoritma *zero-fill* untuk visualisasi keuangan 30 hari & pelacakan aktivitas poin 7 hari.

---

## Environment

**URL Production:** *(isi setelah deploy ke Vercel)*
**Database host:** filess.io (MySQL) ✓ Koneksi & Migrasi Sukses
**PHP version target:** 8.2
**Laravel version:** 11.53.1

---

## Apa yang Sudah Ada

### Framework & Infrastruktur
- Laravel 11.53.1 berhasil dikonfigurasi dan berjalan dengan baik.
- `vercel.json` dan `api/index.php` siap digunakan untuk deployment ke Vercel serverless.
- Seluruh migration (9/9) telah dijalankan dan database terisi penuh.
- Default tests berhasil lewat dengan hasil PASS (2 passed, 3 assertions).

### Models & Services
- `app/Models/User.php` — relationships ke jadwal, lembur, laporan, profit.
- `app/Models/Setting.php` — static helpers: getValue(), setValue(), izinUploadRekan(), pctSaham(), pctKerja().
- `app/Models/FinancialReport.php` — SoftDeletes, scopes: pemasukan(), pengeluaran(), tanggal().
- `app/Models/Schedule.php` — scopes: sudahBerlalu(), untukUser().
- `app/Models/OvertimeTask.php` — scopes: tersedia(), diambilOleh().
- `app/Models/ProfitDistribution.php` — relationship ke detail.
- `app/Models/ProfitDistributionDetail.php` — helper tandaiDitransfer().
- `app/Services/PoinKerjaService.php` — menghitung poin kerja dinamis harian (jadwal berlalu + lembur diklaim).
- `app/Services/ProfitService.php` — kalkulasi pembagian dividen profit (50% Saham, 50% Kerja) dan distribusi ke DB.

### Middleware & Controllers
- `RoleMiddleware.php` — proteksi akses halaman berdasarkan role user.
- `IzinUploadMiddleware.php` — proteksi upload laporan oleh rekan berdasarkan setting global.
- `LoginController.php` — mengurus login session-based dengan custom redirect.
- `RekanController.php` (SA) — mengurus CRUD rekan kerja beserta validasi koin saham (maks 100).
- `ScheduleController.php` (SA) & `OvertimeTaskController.php` (SA) — mengurus penugasan kerja dan lembur.
- `FinancialReportController.php` (SA) & `ProfitController.php` (SA) — mengurus cashflow dan bagi hasil dividen.
- `Rekan\ScheduleController.php` & `Rekan\OvertimeTaskController.php` — mengurus panel personal tugas & klaim lembur (FCFS race condition protected).
- `Rekan\FinancialReportController.php` — mengurus transparansi kas bagi Rekan.
- `Rekan\ProfitCalculatorController.php` — mengurus estimator profit real-time.
- `Rekan\PersonalIncomeController.php` — mengurus invoice tagihan transfer.
- `Rekan\ProfileController.php` — mengurus update username, password, dan foto.

### UI & Blade Views (Premium Tailwind CSS + AlpineJS)
- `layouts/app.blade.php` — layout utama dengan sidebar responsif mobile-friendly.
- `layouts/auth.blade.php` — layout login dengan estetik Glassmorphism.
- Komponen Blade: `alert`, `stat-card`, `chart-card`, `modal-confirm`.
- Seluruh 20 views untuk dashboard, index, create, edit, kalkulator, dan profil telah selesai diimplementasikan.

---

## File & Folder Penting yang Sudah Dibuat

```
app/Models/                                      -- 7 model database lengkap
app/Services/PoinKerjaService.php                -- kalkulasi poin kerja realtime
app/Services/ProfitService.php                   -- alokasi profit & dividen
app/Http/Controllers/                            -- semua controller SuperAdmin & Rekan
app/Http/Requests/                               -- FormRequest validasi rules Bahasa Indonesia
bootstrap/app.php                                -- pendaftaran middleware kustom
routes/web.php                                   -- pemetaan seluruh route aplikasi
resources/views/layouts/                         -- layouts (auth & app)
resources/views/components/                      -- reusable blade components (stat-card, dsb.)
resources/views/superadmin/                      -- 14 file view khusus admin
resources/views/rekan/                           -- 7 file view khusus rekan
tests/Feature/ExampleTest.php                    -- unit test root redirect
```

---

## Keputusan yang Sudah Dibuat Sesi Ini

- **ADR-007: Reusable Blade Components dan Client-side AlpineJS profit simulator:** Menggunakan AlpineJS untuk kalkulasi zero-latency simulator profit di browser Rekan agar tidak membebani server dan memberikan interaksi premium (lihat DECISIONS.md).

---

## Hal yang Perlu Diperhatikan Sesi Berikutnya

1. **Fase Berikutnya adalah Fase 6 (Dashboard & Grafik) & Fase 7 (Polish & Deploy)**.
2. Dashboard SuperAdmin dan Rekan saat ini masih menampilkan placeholder statis (Chart dan data ringkasan). Ini siap dihubungkan dengan data riil Chart.js pada Fase 6.
3. Seluruh navigasi menu dan fitur CRUD operasional telah aktif dan siap dites langsung oleh user.

---

## Hutang Teknis (Technical Debt)

- Ephemeral storage Vercel: Foto profil dan screenshot bukti laporan yang diunggah akan terhapus saat Vercel melakukan redeployment. Solusi: Migrasi ke Cloudinary untuk persistent upload (Backlog).

---

## Credentials Default (JANGAN COMMIT KE GIT)

Setelah seeder dijalankan, akun default yang tersedia adalah:

| Role | Username | Password |
|---|---|---|
| SuperAdmin | `superadmin` | `password123` |
| Rekan Kerja | `rekan` | *dapat dibuat via panel SuperAdmin* |

---

## Catatan Sesi

---
**Sesi 1 — 2026-05-24:**
- Setup fondasi project (Laravel, migrations, models, seeders, middlewares).
**Sesi 2 — 2026-05-24 (Sesi Ini):**
- Membuat komponen Blade kustom: `stat-card.blade.php`, `chart-card.blade.php`, dan `modal-confirm.blade.php`.
- Mengimplementasikan 14 views SuperAdmin (Rekan, Jadwal, Lembur, Laporan, Profit, Settings) dengan visual premium.
- Mengimplementasikan 7 views Rekan (Dashboard, Laporan, Jadwal, Lembur FCFS, Kalkulator interaktif client-side AlpineJS, Penghasilan, Profil).
- Menghubungkan FormRequest, Controller, dan View, serta menuntaskan bug "error semua page selain login" akibat file view kosong.
- Melengkapi logic `Rekan\ProfileController` (Secure update username, password & avatar).
- Mengimplementasikan **FASE 6 — Dashboard & Grafik** (integrasi Chart.js untuk line chart kas 30 hari, pie chart koin saham, dan line chart perolehan poin personal 7 hari).
- Menyelesaikan request user untuk menyertakan akun **SuperAdmin sebagai partisi rekan aktif** di seluruh tabel, grafik pie saham, akumulasi poin kerja harian, dan breakdown pembagian profit.
- Memodifikasi unit test agar PASS 100%.
