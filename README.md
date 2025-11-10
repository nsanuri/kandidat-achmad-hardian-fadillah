🛠️ Langkah Instalasi
1️⃣ Clone / Download Repository
git clone https://github.com/username/otp-api.git
cd otp-api

2️⃣ Install Dependency
composer install

3️⃣ Buat File .env

Salin dari contoh:

cp .env.example .env


# Lalu edit .env sesuai konfigurasi lokal:

APP_NAME="OTP API"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=otp_api
DB_USERNAME=root
DB_PASSWORD=

# Email (jika nanti kirim OTP via email)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=youremail@gmail.com
MAIL_PASSWORD=yourpassword
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=youremail@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

4️⃣ Generate Key
php artisan key:generate

5️⃣ Jalankan Migrasi Database
php artisan migrate

6️⃣ Jalankan Server
php artisan serve


# Server akan berjalan di:
👉 http://localhost:8000

🌐 Endpoint API
🔹 1. Generate OTP

# URL: POST /api/generate-otp

Body (JSON):

{
  "email": "u@example.com"
}


# Respons Berhasil:

{
  "status": true,
  "message": "OTP Code generated successfully",
  "data": {
    "otp": "60027",
    "email": "u@example.com",
    "created_at": "2025-11-10 06:22:27"
  }
}


# Respons Gagal:

{
  "status": false,
  "message": "The email is not registered",
  "data": {
    "email": "u@example.com"
  }
}

🔹 2. Verify OTP

 # URL: POST /api/verify-otp

Body (JSON):

{
  "email": "u@example.com",
  "otp": "60027"
}


Respons Berhasil:

{
  "status": true,
  "message": "OTP verified successfully",
  "data": {
    "user_id": 1,
    "email": "u@example.com",
    "token": "xxxxxxxx"
  }
}


Respons Gagal:

{
  "status": false,
  "message": "Invalid or expired OTP"
}

🧪 Pengujian via Postman
🔸 Tahapan:

Generate OTP

Method: POST

 # URL: http://localhost:8000/api/generate-otp

Body JSON: { "email": "u@example.com" }

Lihat respons → ambil otp dari hasilnya.

Verify OTP

Method: POST

 # URL: http://localhost:8000/api/verify-otp

Body JSON: { "email": "u@example.com", "otp": "60027" }

Jika benar, akan muncul token / status sukses.
