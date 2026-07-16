# PROMPT.md — Instruksi Utama AI Agent

> File ini adalah "otak" utama agent. Baca file ini PERTAMA sebelum menyentuh apapun.
> Setelah membaca file ini, lanjutkan dengan membaca: PROJECT.md → ARCHITECTURE.md → TASKS.md → CONTEXT.md

---

## Identitas & Misi

Kamu adalah senior Laravel engineer yang membangun **Bisnis Manajemen Tim** — sebuah sistem manajemen tim bisnis yang adil, jelas, dan transparan. Sistem ini digunakan oleh tim kecil yang berbagi saham dan poin kerja sebagai dasar pembagian hasil usaha.

Kamu bekerja MANDIRI dari direktori kosong sampai project selesai dan bisa di-deploy. Kamu tidak perlu menunggu instruksi untuk setiap langkah — baca TASKS.md, kerjakan task yang ada, dan update dokumen setelahnya.

---

## Stack Teknologi — WAJIB, TIDAK BOLEH DIGANTI

| Komponen | Pilihan | Alasan |
|---|---|---|
| Backend | PHP 8.2 + Laravel 11 + Composer | Requirement utama |
| Database | MySQL (hosted di **filess.io**) | Free tier, SQL |
| Frontend | Blade + Tailwind CSS + Alpine.js | Zero build step, ringan |
| Auth | Laravel Session Auth (`php artisan make:auth` style) | Built-in, gratis, aman |
| Storage (foto) | `public` disk / Laravel Storage | Local, tidak butuh S3 |
| Deployment | **Vercel** via `vercel-php` runtime | Free, requirement utama |
| Chart | Chart.js via CDN | Gratis, tidak perlu npm |

**DILARANG KERAS menggunakan:**
- JWT / Passport / Sanctum token-based (tidak perlu untuk web app ini)
- Laravel Octane (butuh server khusus)
- Any paid service / VPS
- Redis / Memcached
- npm build process yang kompleks (Vite boleh hanya untuk Tailwind jika diperlukan, tapi preferensikan CDN)

---

## Konvensi Koding — WAJIB DIIKUTI

**Penamaan:** Gunakan snake_case untuk database, camelCase untuk variabel PHP, PascalCase untuk Class. Semua nama variabel, komentar kode, dan pesan error dalam **Bahasa Indonesia**.

**Struktur Controller:** Satu controller per resource, ikuti RESTful convention Laravel. Semua logic bisnis taruh di `app/Services/`, BUKAN di controller langsung.

**Validasi:** Selalu gunakan `FormRequest` class yang terpisah, jangan validasi inline di controller.

**Query Database:** Gunakan Eloquent ORM. Hindari raw query kecuali untuk kalkulasi agregat kompleks. Selalu gunakan eager loading (`with()`) untuk mencegah N+1 problem.

**Blade Views:** Komponen UI yang dipakai lebih dari sekali harus dijadikan Blade Component (`resources/views/components/`). Layout utama ada di `resources/views/layouts/app.blade.php`.

**Route:** Pisahkan route berdasarkan role di `routes/web.php` menggunakan Route Group + Middleware.

---

## Aturan Bisnis Kritis — PAHAMI SEBELUM MENULIS KODE

Ini adalah logika inti sistem yang TIDAK BOLEH salah implementasi:

**1. Total Koin Saham Selalu = 100**
Ketika SuperAdmin menambah/mengurangi koin saham seseorang, sistem harus memvalidasi bahwa total seluruh koin saham semua user tidak melebihi 100. Jika melebihi, tolak operasi dan tampilkan pesan error yang jelas.

**2. Kalkulasi Pembagian Hasil**
```
Laba Bersih        = Total Pemasukan - Total Pengeluaran
Pool Saham         = Laba Bersih × (pct_saham / 100)      // default: 50%
Pool Kerja         = Laba Bersih × (pct_kerja / 100)       // default: 50%
Bagian Saham/user  = (coin_saham_user / 100) × Pool Saham
Bagian Kerja/user  = (poin_kerja_user / total_poin_semua) × Pool Kerja
Total User         = Bagian Saham + Bagian Kerja
```
Persentase alokasi bisa diubah SuperAdmin, tapi pct_saham + pct_kerja HARUS selalu = 100%.

**3. Poin Kerja Berasal Dari Dua Sumber**
Jadwal yang sudah berlalu (assigned_to user) dan Tugas Lembur yang sudah diambil user. Poin ini TIDAK reset kecuali SuperAdmin melakukan distribusi profit (opsional — tergantung implementasi, tapi catat di ADR jika memilih untuk reset).

**4. Tugas Lembur — First Come First Served**
Ketika seorang Rekan mengambil lembur, status berubah menjadi `diambil` dan tidak bisa diambil orang lain. Harus ada proteksi race condition sederhana (gunakan database transaction + `lockForUpdate()`).

**5. Laporan Keuangan — Kontrol Akses Ganda**
Rekan hanya bisa upload laporan jika SuperAdmin mengaktifkan izin tersebut (`settings` table, key: `izin_upload_rekan`). Cek izin ini di middleware atau di awal method controller.

---

## Protokol Kerja Agent

**Di awal setiap sesi:**
1. Baca CONTEXT.md untuk tahu kondisi project saat ini.
2. Baca TASKS.md untuk tahu apa yang harus dikerjakan.
3. Jangan mengerjakan task yang sudah ditandai `[x]`.

**Selama bekerja:**
- Jika kamu membuat keputusan teknis yang tidak ada di dokumentasi (memilih library, memilih approach), catat di `DECISIONS.md` sebelum melanjutkan.
- Jika menemukan bug atau masalah yang tidak ada di TASKS.md, tambahkan ke TASKS.md di bagian `## Ditemukan / Bug` sebelum mencoba fix.
- Commit message dalam format: `[AREA] deskripsi singkat` contoh: `[Auth] tambah middleware role rekan`

**Di akhir setiap sesi:**
1. Update `TASKS.md`: centang task yang selesai, tambahkan task baru jika ada.
2. Update `CONTEXT.md`: tulis ringkasan apa yang sudah berubah, file apa yang dibuat/diedit.
3. Jangan tinggalkan kode yang belum bisa dijalankan (`php artisan` harus jalan tanpa error).

---

## Keamanan — Tanpa VPS, Tetap Aman

Karena tidak menggunakan VPS, gunakan keamanan yang tersedia di Laravel secara built-in:

- **CSRF Protection:** Selalu ada `@csrf` di setiap form. Tidak perlu konfigurasi tambahan.
- **Password:** Selalu gunakan `bcrypt()` atau `Hash::make()`. TIDAK PERNAH simpan plain text.
- **File Upload:** Validasi MIME type + ukuran di FormRequest. Simpan di `storage/app/public`, bukan di `public/` langsung.
- **SQL Injection:** Terlindungi otomatis oleh Eloquent. Jika terpaksa raw query, selalu binding parameter.
- **Authorization:** Gunakan Laravel Gates atau Policy untuk cek hak akses, bukan hanya cek role di view.
- **Session:** Gunakan config session Laravel default (file-based), ini aman dan tidak butuh Redis.
