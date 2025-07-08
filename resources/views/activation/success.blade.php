<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun Berhasil</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        
        body {
            background-color: #f4f7f6;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #333;
        }

        .container {
            text-align: center;
            background-color: #ffffff;
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
        }

        .icon {
            color: #28a745; /* Warna hijau sukses */
            font-size: 80px;
            line-height: 1;
        }

        h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-top: 20px;
            margin-bottom: 15px;
        }

        p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }

        .login-button {
            display: inline-block;
            background-color: #007bff; /* Warna biru primer */
            color: #ffffff;
            padding: 12px 30px;
            margin-top: 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .login-button:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        {{-- Ikon centang menggunakan SVG inline agar tidak perlu file eksternal --}}
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </svg>
        </div>

        <h1>Aktivasi Berhasil!</h1>
        <p>
            Akun Anda telah berhasil diaktifkan. <br>
            Anda sekarang dapat melanjutkan untuk masuk ke portal.
        </p>
        
        {{-- Ganti URL ini dengan URL halaman login di aplikasi frontend Anda --}}
         <a href="{{ config('app.url_fe') }}/login" class="login-button">Lanjut ke Halaman Login</a>
    </div>
</body>
</html>