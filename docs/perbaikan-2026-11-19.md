# Perbaikan booking, akses, dan fitur anggota

## Deployment

1. Cadangkan database sebelum deployment.
2. Jalankan `php artisan migrate --force` untuk menambahkan hubungan absensi ke booking dan tabel reset password.
3. Jalankan `php artisan optimize:clear`.
4. Pastikan konfigurasi email berfungsi untuk OTP dan reset password.
5. Jalankan `php artisan test` pada lingkungan pengujian.

Migration baru tidak menghapus data absensi lama. Catatan lama tetap memiliki `pendaftaran_tari_id` kosong; tidak dipasangkan otomatis karena data lama tidak menyimpan jam booking yang cukup untuk pemetaan pasti. Absensi booking baru menggunakan ID pendaftaran unik.

## Perubahan alur

- Booking web, dashboard pengunjung, API, dan registrasi memakai `BookingService`.
- Booking `pending` ikut menghabiskan kapasitas. Maksimal dua tarian dan lima orang per tarian per slot.
- Akun pengunjung kedaluwarsa harus diperpanjang admin sebelum dapat mengakses kembali area anggota.
- Scan anggota menggunakan QR dinamis. QR statis lama ditolak; gunakan absensi manual untuk koreksi data lama.
- Admin dapat membuka `/admin/kehadiran/booking` untuk absensi per tanggal dan jam booking. Form jadwal lama masih tersedia untuk data lama.
- Anggota dapat membuka `/perkembangan` untuk pengumuman, rapor, dan lencana.
- `/link-storage` dihapus. Gunakan `php artisan storage:link` melalui deployment.
- `/test-code` memerlukan admin.
- Reset password memakai route aktif serta tabel `password_reset_tokens`.

## Layanan eksternal

- Pembayaran event tetap melalui transfer manual. Webhook Midtrans diperbaiki pada URL `/admin/midtrans/webhook`, namun pembuatan transaksi Snap belum diaktifkan.
- Notifikasi FCM tetap ditujukan ke aplikasi mobile. Web anggota membaca pengumuman melalui halaman perkembangan; push browser belum ditambahkan.
- Email, Google OAuth, FCM, dan callback Midtrans perlu diuji dengan konfigurasi layanan deployment.
- Isi `PAYMENT_BANK_NAME`, `PAYMENT_ACCOUNT_NUMBER`, dan `PAYMENT_ACCOUNT_HOLDER` dengan rekening resmi. Tanpa konfigurasi tersebut, formulir meminta peserta menghubungi admin; nomor rekening contoh tidak ditampilkan.

## Pengujian

Tes semu `assertTrue(true)` diganti dengan assertion autentikasi, respons, email, password, database, kapasitas booking, rollback registrasi, dan absensi terpisah per booking. SQLite in-memory dipakai untuk menghindari perubahan database aplikasi.
