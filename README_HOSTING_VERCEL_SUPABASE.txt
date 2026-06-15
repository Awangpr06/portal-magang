CATATAN HOSTING PORTAL MAGANG KE VERCEL + SUPABASE

1. Project ini adalah Laravel 10 + Vite/Tailwind, jadi Vercel perlu PHP Runtime komunitas: vercel-php.
2. File yang sudah ditambahkan:
   - api/index.php
   - vercel.json
   - .vercelignore
   - .env.example.vercel
3. Jangan upload folder vendor dan node_modules ke GitHub/Vercel.
4. Database lokal saat diekstrak masih MySQL: DB_CONNECTION=mysql, DB_DATABASE=db_laravel.
   Untuk database kemarin, pakai Supabase PostgreSQL dan ganti env menjadi DB_CONNECTION=pgsql + DATABASE_URL dari Supabase.
5. Karena Vercel serverless, upload file ke storage lokal Laravel tidak permanen. Untuk produksi, pindahkan file upload ke Supabase Storage / S3 / Cloudinary / Vercel Blob.
6. Jalankan migrasi ke Supabase dari komputer lokal:
   php artisan migrate --force
   php artisan db:seed --force
