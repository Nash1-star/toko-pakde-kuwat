# Deployment Gratis Toko Pakde Kuwat

## Arsitektur

- Aplikasi Laravel: Render Web Service (free)
- Database: Neon atau Supabase PostgreSQL (free tier)
- Source code: GitHub

SQLite lokal tidak dipakai di production karena filesystem service gratis dapat kembali ke kondisi awal saat restart/deploy.

## Persiapan lokal

1. Install Git dari https://git-scm.com/download/win.
2. Buat repository baru di GitHub, misalnya `toko-pakde-kuwat`.
3. Jalankan dari folder proyek:

```powershell
git init
git add .
git commit -m "Prepare Toko Pakde Kuwat for deployment"
git branch -M main
git remote add origin https://github.com/USERNAME/toko-pakde-kuwat.git
git push -u origin main
```

## Buat database gratis

Pilih salah satu:

- Neon: https://neon.tech
- Supabase: https://supabase.com

Buat project PostgreSQL lalu catat host, port, database, username, dan password. Jangan masukkan URL database ke source code atau commit.

## Deploy ke Render

1. Buka https://render.com dan masuk dengan GitHub.
2. Pilih **New > Blueprint**.
3. Pilih repository `toko-pakde-kuwat`.
4. Render akan membaca `render.yaml` dan membangun Dockerfile.
5. Isi variable `ACCOUNT_MANAGEMENT_PASSWORD` dengan sandi khusus yang kuat.
6. Isi `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` dari Neon/Supabase.
7. Deploy.

Saat container mulai, `docker/start.sh` menjalankan migrasi otomatis. Setelah selesai, Render memberikan alamat seperti:

```text
https://toko-pakde-kuwat.onrender.com
```

## Catatan free tier

Render free dapat tidur ketika tidak digunakan, sehingga akses pertama bisa membutuhkan waktu beberapa detik. Database free juga memiliki batas kapasitas. Untuk data toko nyata, aktifkan backup/export berkala.

## Data awal

Database production masih kosong setelah migrasi. Jika ingin data contoh, jalankan seeder hanya sekali dari shell deployment:

```bash
php artisan db:seed --force
```

Jangan menjalankan seeder berulang kali karena data contoh akan berduplikasi.