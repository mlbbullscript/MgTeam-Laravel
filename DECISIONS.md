# DECISIONS.md — Catatan Keputusan Teknis (ADR)

> Architecture Decision Records — dokumentasi MENGAPA setiap keputusan teknis diambil.
> Agent: Tambahkan entri baru di SINI setiap kali membuat keputusan yang tidak ada di dokumentasi lain.
> Format: ADR-XXX, tanggal, status, konteks, keputusan, konsekuensi, alternatif yang ditolak.

---

## ADR-001: Gunakan Tailwind CSS via CDN, Bukan Build Process

**Tanggal:** *(isi saat project dimulai)*
**Status:** Accepted

**Konteks:** Project ini di-deploy di Vercel yang bisa menjalankan build step, tapi kita ingin meminimalkan kompleksitas setup dan menghindari dependency npm yang bisa bermasalah di serverless environment.

**Keputusan:** Gunakan Tailwind CSS via CDN Play CDN untuk development. Untuk production, gunakan Tailwind standalone CLI untuk generate CSS file statis, lalu commit hasilnya.

**Konsekuensi:** Development lebih cepat tanpa perlu `npm install`. CSS yang di-generate mungkin sedikit lebih besar karena tidak di-purge secara otomatis, tapi untuk project internal ukuran ini tidak menjadi masalah.

**Alternatif yang ditolak:** Vite + npm — terlalu kompleks untuk kebutuhan project ini dan bisa menimbulkan masalah di Vercel PHP runtime.

---

## ADR-002: Poin Kerja Dihitung Dinamis, Tidak Disimpan di Tabel users

**Tanggal:** *(isi saat project dimulai)*
**Status:** Accepted

**Konteks:** Ada dua pilihan: simpan `total_poin_kerja` sebagai kolom di tabel users (denormalized), atau hitung secara dinamis dari tabel schedules dan overtime_tasks setiap kali dibutuhkan.

**Keputusan:** Hitung dinamis via query/service. Tidak ada kolom `poin_kerja` di tabel users.

**Konsekuensi:** Setiap kalkulasi membutuhkan query tambahan. Namun untuk skala tim kecil (< 20 orang) ini tidak akan menjadi bottleneck performa. Keuntungan besarnya: tidak ada risiko data poin kerja menjadi tidak sinkron jika admin mengedit jadwal atau lembur di masa lalu.

**Alternatif yang ditolak:** Kolom cached `poin_kerja` di tabel users — lebih cepat tapi rawan inkonsistensi data. Butuh event/observer untuk menjaga sinkronisasi, menambah kompleksitas yang tidak perlu.

---

## ADR-003: File Upload Disimpan Sebagai Path Lokal (Bukan Base64 di DB)

**Tanggal:** *(isi saat project dimulai)*
**Status:** Accepted — dengan catatan

**Konteks:** Vercel memiliki ephemeral filesystem — file yang diupload akan hilang saat deployment berikutnya. Ada tiga pilihan: base64 di database, layanan storage eksternal (Cloudinary/S3), atau path lokal.

**Keputusan:** Untuk MVP, simpan sebagai path lokal di `storage/app/public`. Ini sudah cukup untuk testing dan demonstrasi. Sebelum go-live dengan data riil, migrate ke Cloudinary (free tier 25GB).

**Konsekuensi:** File upload tidak persisten di Vercel production. Ini adalah hutang teknis yang HARUS diselesaikan sebelum penggunaan riil. Sudah masuk Backlog di TASKS.md.

**Alternatif yang ditolak:** Base64 di database — ukuran database akan membengkak dan query menjadi sangat lambat. Langsung pakai Cloudinary dari awal — menambah kompleksitas setup yang tidak perlu untuk MVP.

---

## ADR-004: Soft Delete Hanya untuk financial_reports

**Tanggal:** *(isi saat project dimulai)*
**Status:** Accepted

**Konteks:** Perlu memutuskan apakah semua tabel menggunakan soft delete (deleted_at) atau hard delete.

**Keputusan:** Hanya `financial_reports` yang menggunakan soft delete. Tabel lain menggunakan hard delete atau deactivation flag (untuk users).

**Konsekuensi:** Data laporan keuangan yang dihapus masih tersimpan di database (audit trail). Data rekan yang dihapus akan hilang permanen — namun rekan bisa di-nonaktifkan via `is_active = false` sebagai alternatif yang lebih aman.

**Alternatif yang ditolak:** Soft delete di semua tabel — menambah kompleksitas query (selalu harus ingat scope `withoutTrashed`) tanpa manfaat signifikan untuk data non-finansial.

---

## ADR-005: Inisialisasi Laravel di Direktori Non-Kosong

**Tanggal:** 2026-05-24
**Status:** Accepted

**Konteks:** Direktori project sudah berisi file dokumentasi (PROMPT.md, PROJECT.md, ARCHITECTURE.md, TASKS.md, CONTEXT.md, DECISIONS.md, .env.example). Composer `create-project` menolak direktori yang tidak kosong.

**Keputusan:** Install Laravel di subdirectory sementara (`_laravel_temp`), lalu pindahkan semua file Laravel ke root direktori. File dokumentasi yang sudah ada dipertahankan.

**Konsekuensi:** Proses setup sedikit lebih panjang, tapi file dokumentasi tetap terjaga. File `.env.example` yang sudah ada akan digantikan oleh versi yang lebih lengkap setelah setup.

**Alternatif yang ditolak:** Hapus semua file dokumentasi lalu install — berisiko kehilangan konteks project. Gunakan `--force` flag — tidak tersedia di `composer create-project`.

---

## ADR-006: Gunakan `username` Sebagai Field Autentikasi (Bukan `email`)

**Tanggal:** 2026-05-24
**Status:** Accepted

**Konteks:** Laravel secara default menggunakan `email` sebagai field identifikasi user. Sistem ini menggunakan `username` — lebih sederhana untuk tim internal yang tidak membutuhkan email.

**Keputusan:** Tidak menggunakan email sama sekali. `Auth::attempt` menerima array `['username' => ..., 'password' => ..., 'is_active' => true]`. Model User tidak memiliki kolom `email` atau `remember_token`.

**Konsekuensi:** Fitur "lupa password via email" tidak tersedia (tidak dibutuhkan untuk project ini — ada SuperAdmin yang bisa reset manual). Perlu memastikan tidak ada code bawaan Laravel yang mengasumsikan kolom email ada di tabel users.

**Alternatif yang ditolak:** Tetap pakai email tapi tidak wajib (nullable) — lebih membingungkan dan tidak konsisten dengan domain problem.

---

## ADR-007: Reusable Blade Components dan Client-side AlpineJS profit simulator

**Tanggal:** 2026-05-24
**Status:** Accepted

**Konteks:** Perlu menyederhanakan kode visual, menyatukan desain estetika premium, dan menambahkan simulator interaktif bagi Rekan kerja tanpa memberatkan server.

**Keputusan:** Buat komponen Blade (`stat-card`, `chart-card`, `modal-confirm`) untuk struktur UI yang konsisten dan elegan. Gunakan Alpine.js untuk menggerakkan state konfirmasi modal dinamis serta simulator hitung profit di browser Rekan secara real-time (kalkulasi client-side).

**Konsekuensi:** Mengurangi duplikasi kode HTML visual di seluruh halaman, meningkatkan performa interaksi (tanpa reload halaman atau Ajax payload saat mensimulasikan profit), dan memberikan pengalaman pengguna yang sangat premium dan hidup.

**Alternatif yang ditolak:** Kalkulasi simulasi profit via Ajax/PHP Controller — memakan bandwidth, menimbulkan delay latency, dan membuat server terbebani untuk kalkulasi sementara yang tidak disimpan.

---

*Agent: Tambahkan ADR baru di bawah garis ini saat membuat keputusan teknis baru.

---

## ADR-008: Chart.js Integrasi & Zero-Fill untuk Grafik Operasional Kontinu

**Tanggal:** 2026-05-24
**Status:** Accepted

**Konteks:** Perlu menampilkan grafik keuangan harian (30 hari terakhir) bagi SuperAdmin dan grafik poin kerja (7 hari terakhir) bagi Rekan dengan kurva kontinu yang mulus, tanpa terputus jika ada hari-hari tanpa transaksi atau aktivitas.

**Keputusan:** Integrasikan Chart.js. Pada level controller, siapkan data dengan perulangan tanggal mundur (`subDays($i)`) menggunakan algoritma *zero-fill* (mengisi nilai default `0.0` pada setiap tanggal di rentang waktu tersebut sebelum menimpa dengan sum data nyata dari database).

**Konsekuensi:** Grafik garis Chart.js akan selalu ter-render kontinu, presisi secara kronologis, dan bebas bug tata letak visual saat tidak ada transaksi pada tanggal tertentu.

**Alternatif yang ditolak:** Mengirim data mentah grouping dari database langsung ke Chart.js — jika ada hari tanpa transaksi, tanggal tersebut akan melompati sumbu X, merusak konsistensi rentang waktu grafik.
*Agent: Tambahkan ADR baru di bawah garis ini saat membuat keputusan teknis baru.

---

## ADR-009: Inklusi Akun SuperAdmin dalam Daftar Rekan dan Distribusi Profit

**Tanggal:** 2026-05-24
**Status:** Accepted

**Konteks:** Sistem awalnya hanya menampilkan user dengan role `rekan` di halaman "Kelola Rekan". Namun, dalam bisnis skala tim kecil, pemilik usaha (SuperAdmin) juga bertindak sebagai rekan aktif yang bekerja harian, memiliki alokasi saham, dan berhak atas dividen/poin kerja.

**Keputusan:** Hilangkan filter `role = 'rekan'` pada *RekanController*, *UpdateSahamRequest*, *PoinKerjaService*, *ProfitService*, dan *DashboardController*. Akun SuperAdmin dimasukkan sebagai baris interaktif di daftar Rekan untuk mengizinkan input porsi `coin_saham` dan kalkulasi poin kerja baginya. Tambahkan pengaman visual dan otorisasi agar SuperAdmin tidak bisa menonaktifkan akunnya sendiri secara tidak sengaja.

**Konsekuensi:** SuperAdmin dapat mengelola porsi sahamnya sendiri dan mendapatkan bagi hasil profit tim secara transparan dan adil, selaras dengan tagline tim.

**Alternatif yang ditolak:** Memisahkan porsi saham SuperAdmin di tabel terpisah — menambah kompleksitas skema database dan formula perolehan bagi hasil.

---

*Agent: Tambahkan ADR baru di bawah garis ini saat membuat keputusan teknis baru.

---

## ADR-010: Ekstraksi ID dari Route Model Binding pada FormRequest

**Tanggal:** 2026-05-24
**Status:** Accepted

**Konteks:** Saat melakukan update data rekan, sistem memicu error database `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'username:\"superadmin\"' in 'where clause'`. Ini terjadi karena parameter rute `rekan` yang diinjeksikan berupa objek model `User` utuh akibat *route model binding*, bukan integer ID biasa. Ketika dilewatkan langsung ke aturan `'unique:users,username,' . $userId`, PHP secara otomatis men-cast model tersebut menjadi string JSON yang memicu parser internal aturan validasi unik Laravel memecah JSON tersebut menjadi klausa SQL `where` yang rusak.

**Keputusan:** Lakukan ekstraksi ID integer secara eksplisit di awal method `rules()` dan `withValidator()` pada *UpdateSahamRequest* dengan mengecek apakah parameter rute berupa objek model:
```php
$rekan = $this->route('rekan');
$userId = is_object($rekan) ? $rekan->id : $rekan;
```

**Konsekuensi:** Menghilangkan bug *query crash* 100% dan membuat form validasi aman digunakan baik untuk route model bound parameters (seperti method PUT/PATCH) maupun parameter mentah (seperti metode store/create).

**Alternatif yang ditolak:** Menonaktifkan route model binding di rute `superadmin.rekan.update` — ditolak karena merusak kekonsistenan penulisan standar RESTful Laravel.

---

*Agent: Tambahkan ADR baru di bawah garis ini saat membuat keputusan teknis baru.*
