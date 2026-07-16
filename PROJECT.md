# PROJECT.md — Gambaran Umum Project

## Nama Project
**Bisnis Manajemen Tim** — Sistem Manajemen Tim Bisnis

## Tagline
*"Adil, Jelas, dan Transparan"*

## Tujuan Sistem
Membangun platform manajemen internal untuk tim bisnis kecil yang memungkinkan setiap anggota (Rekan) memantau arus keuangan, jadwal kerja, dan porsi penghasilan mereka secara real-time — sehingga tidak ada ketidakjelasan dalam pembagian hasil usaha.

---

## Target Pengguna

**SuperAdmin** adalah pemilik/pengelola utama bisnis. Bertanggung jawab atas semua data master: mengelola rekan, saham, jadwal, laporan keuangan, dan melakukan distribusi profit. Ada hanya **satu** SuperAdmin (atau beberapa, tapi dengan hak akses identik).

**Rekan** adalah anggota tim bisnis. Memiliki porsi saham dan akumulasi poin kerja. Bisa melihat kondisi keuangan tim, mengambil tugas lembur, dan memantau penghasilan pribadi mereka.

---

## Konsep Inti Sistem

Sistem ini bekerja dengan dua "mata uang internal" yang menentukan penghasilan setiap anggota:

**Koin Saham** merepresentasikan kepemilikan bisnis. Total seluruh koin = 100. Sifatnya permanen sampai SuperAdmin mengubahnya. Semakin besar koin saham seseorang, semakin besar porsi 50% pertama dari laba bersih yang ia terima.

**Poin Kerja** merepresentasikan kontribusi aktif harian. Didapat dari menyelesaikan jadwal yang ditugaskan dan mengambil tugas lembur. Menentukan porsi dari 50% kedua laba bersih. Sistem ini memastikan orang yang bekerja lebih keras mendapat kompensasi lebih, terlepas dari kepemilikan sahamnya.

---

## Scope Project (Dalam Lingkup)

- Sistem autentikasi berbasis session (login/logout)
- Dashboard dengan data analitik dan grafik keuangan
- Manajemen laporan arus keuangan (CRUD + upload screenshot)
- Manajemen jadwal dan tugas lembur
- Kalkulasi profit real-time
- Halaman penghasilan pribadi per rekan
- Manajemen profil pengguna
- Panel SuperAdmin untuk semua operasi master data

## Scope Project (Di Luar Lingkup — Tidak Dikerjakan)

- Sistem notifikasi email/SMS (butuh layanan berbayar)
- Mobile app (hanya web responsive)
- Integrasi payment gateway
- Multi-bahasa
- Fitur chat/komunikasi internal
- Backup otomatis ke cloud (bukan prioritas MVP)

---

## Informasi Deployment

| Komponen | Layanan | Tier |
|---|---|---|
| Hosting aplikasi | Vercel | Free |
| Database MySQL | filess.io | Free |
| File storage | Vercel + Laravel public storage | Free |

**Catatan penting:** Vercel adalah platform serverless. Laravel harus di-setup dengan `vercel-php` runtime (package: `juampi92/vercel-php` atau konfigurasi `vercel.json` manual). File upload harus dipertimbangkan dengan baik karena filesystem Vercel bersifat ephemeral — storage foto profil dan screenshot laporan perlu strategi khusus (lihat ARCHITECTURE.md).
