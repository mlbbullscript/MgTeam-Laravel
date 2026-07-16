# TASKS.md — Task Board Project

> Format: `[ ]` = belum dikerjakan | `[~]` = sedang dikerjakan | `[x]` = selesai
> Update file ini di AKHIR setiap sesi kerja.
> Kerjakan task dari atas ke bawah. Jangan loncat fase.

---

## FASE 0 — Setup & Fondasi

- [x] Inisialisasi project Laravel 11 baru via `composer create-project` ✓
- [x] Setup `.env` untuk koneksi ke filess.io (MySQL) ✓
- [x] Buat `vercel.json` dan file `api/index.php` sebagai entry point Vercel ✓
- [x] Test koneksi database berhasil (`php artisan migrate`) ✓
- [x] Install Tailwind CSS via CDN (tambahkan ke layout, bukan via npm) ✓
- [x] Buat semua file Migration sesuai skema di ARCHITECTURE.md ✓
- [x] Jalankan semua migration ✓ (9/9 Ran) ✓
- [x] Buat Seeder: user SuperAdmin default + settings default ✓
- [x] Buat semua Model dengan relationship yang benar ✓
- [x] Daftarkan `RoleMiddleware` dan `IzinUploadMiddleware` di `bootstrap/app.php` ✓

## FASE 1 — Autentikasi

- [x] Buat `LoginController` dengan method `showLogin`, `login`, `logout` ✓
- [x] Buat view `auth/login.blade.php` (form username + password) ✓
- [x] Buat `layouts/auth.blade.php` ✓
- [x] Setup session-based auth (guard default Laravel) ✓
- [x] Implementasi `RoleMiddleware`: redirect SuperAdmin ke `/superadmin/dashboard`, Rekan ke `/rekan/dashboard` ✓
- [x] Test: login SuperAdmin berhasil, redirect benar ✓
- [x] Test: login Rekan berhasil, redirect benar ✓
- [x] Test: akses URL SuperAdmin oleh Rekan ter-redirect ✓

## FASE 2 — Layout & Komponen UI

- [x] Buat `layouts/app.blade.php` dengan sidebar responsif ✓
- [x] Buat sidebar berbeda untuk SuperAdmin dan Rekan ✓
- [x] Buat Blade Component: `stat-card`, `chart-card`, `alert`, `modal-confirm` ✓
- [x] Pastikan tampilan mobile-friendly (Tailwind responsive classes) ✓

## FASE 3 — Fitur SuperAdmin: Data Master

- [x] `RekanController` — index (daftar rekan + saham + poin kerja) ✓
- [x] `RekanController` — create & store (dengan set foto profil) ✓
- [x] `RekanController` — edit & update ✓
- [x] `RekanController` — destroy (soft delete atau deactivate) ✓
- [x] Validasi: total coin_saham tidak boleh melebihi 100 saat tambah/edit ✓
- [x] `ScheduleController` — full CRUD (hari/tanggal, tugas, deskripsi, poin, assigned_to) ✓
- [x] `OvertimeTaskController` — full CRUD (nama, deskripsi, poin) ✓
- [x] Test semua validasi form ✓

## FASE 4 — Fitur SuperAdmin: Keuangan & Profit

- [x] `FinancialReportController` — index dengan filter (harian, mingguan, bulanan, custom max 3 bulan) ✓
- [x] `FinancialReportController` — create & store (termasuk upload screenshot) ✓
- [x] `FinancialReportController` — edit & update ✓
- [x] `FinancialReportController` — destroy (soft delete) ✓
- [x] Buat `ProfitService` dengan method kalkulasi lengkap ✓
- [x] `ProfitController` — halaman hitung profit (tampilkan breakdown per rekan) ✓
- [x] `ProfitController` — aksi distribusi profit (simpan ke tabel distribusi) ✓
- [x] Setting: toggle izin upload rekan ✓
- [x] Setting: edit persentase alokasi saham/kerja (validasi total = 100%) ✓

## FASE 5 — Fitur Rekan

- [x] `Rekan\DashboardController` — dashboard controller ✓
- [x] `Rekan\FinancialReportController` — lihat laporan (semua rekan), dengan filter waktu ✓
- [x] `Rekan\FinancialReportController` — upload laporan (diproteksi `IzinUploadMiddleware`) ✓
- [x] `Rekan\ScheduleController` — lihat jadwal yang ditugaskan ke diri sendiri ✓
- [x] `Rekan\OvertimeTaskController` — lihat semua lembur tersedia + ambil lembur ✓
- [x] Implementasi race condition protection saat ambil lembur (`DB::transaction` + `lockForUpdate()`) ✓
- [x] `Rekan\ProfitCalculatorController` — kalkulator profit real-time (tanpa distribusi) ✓
- [x] `Rekan\PersonalIncomeController` — riwayat penghasilan yang sudah ditransfer ✓
- [x] `Rekan\ProfileController` — lihat & edit profil (username, password, foto) ✓

## FASE 6 — Dashboard & Grafik

- [x] Dashboard SuperAdmin: total pemasukan, total pengeluaran, laba bersih, jumlah rekan aktif ✓
- [x] Dashboard SuperAdmin: grafik arus keuangan 30 hari terakhir (Chart.js line chart) ✓
- [x] Dashboard SuperAdmin: pie chart distribusi koin saham ✓
- [x] Dashboard Rekan: kartu poin kerja pribadi, koin saham, estimasi bagian profit ✓
- [x] Dashboard Rekan: grafik poin kerja pribadi per minggu ✓

## FASE 7 — Polish & Deploy

- [ ] Review semua Form Request validation — pastikan pesan error dalam Bahasa Indonesia
- [ ] Tambahkan konfirmasi (modal) untuk aksi destruktif (hapus, distribusi profit) ✓ (Sebagian besar sudah ditambahkan via `modal-confirm` di views)
- [ ] Test end-to-end semua alur utama
- [ ] Buat `.env.example` dengan semua key yang dibutuhkan ✓
- [ ] Setup environment variables di Vercel dashboard
- [ ] Deploy ke Vercel dan test production
- [ ] Update CONTEXT.md dengan URL production

---

## Ditemukan / Bug

*Kosong — semua halaman sudah bebas dari error view not found*

---

## Backlog (Tidak Wajib untuk MVP)

- [ ] Export laporan ke PDF/Excel
- [ ] Halaman riwayat distribusi profit lengkap (SuperAdmin)
- [ ] Filter lembur berdasarkan tanggal
- [ ] Foto profil via Cloudinary (solusi ephemeral storage Vercel)
