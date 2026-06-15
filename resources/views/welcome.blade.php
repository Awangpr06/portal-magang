<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Portal Magang LLDIKTI</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    min-height: 100vh;
    background: #0b5f86;
    overflow-x: hidden;
    color: white;
}

.welcome-page {
    min-height: 100vh;
    position: relative;
    display: flex;
    justify-content: center;
    text-align: center;
    padding-top: 60px;
    overflow: hidden;
}

.content {
    z-index: 5;
    width: 100%;
    padding: 0 20px;
}

.logo {
    width: 130px;
    margin-bottom: 15px;
}

h1 {
    font-size: 52px;
    font-weight: 400;
    margin-bottom: 10px;
}

h1 span {
    font-weight: 800;
}

p {
    font-size: 20px;
    margin-bottom: 10px;
}

.illustration {
    width: 100%;
    max-width: 1100px;
    height: auto;
    margin-top: -20px;
    object-fit: contain;
}

.buttons {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin-top: -30px;
    position: relative;
    z-index: 10;
    flex-wrap: wrap;
}

.btn {
    width: 260px;
    padding: 18px;
    border-radius: 16px;
    text-decoration: none;
    font-weight: 800;
    font-size: 20px;
    transition: 0.3s;
}

.btn:hover {
    transform: translateY(-3px);
}

.btn-login {
    background: white;
    color: #0b5f86;
}

.btn-register {
    background: #62bd42;
    color: white;
}

.wave {
    position: absolute;
    bottom: -20px;
    width: 120%;
    height: 140px;
    background: #0a4f73;
    border-radius: 50% 50% 0 0;
    z-index: 1;
}

@media (max-width: 768px) {

    .welcome-page {
        padding-top: 40px;
    }

    h1 {
        font-size: 36px;
    }

    p {
        font-size: 16px;
    }

    .illustration {
        max-width: 100%;
        margin-top: 0;
    }

    .buttons {
        flex-direction: column;
        align-items: center;
        gap: 20px;
        margin-top: 10px;
    }

    .btn {
        width: 90%;
        max-width: 320px;
    }
}
    </style>
</head>
<body>

<div class="welcome-page">

    <div class="content">

        <img src="{{ asset('images/logo-lldikti.png') }}" class="logo">

        <h1><span>HALLO!</span> Warga Lima</h1>

        <p>
            Portal ini digunakan untuk pengelolaan kegiatan magang & kearsipan mahasiswa
        </p>

        <img src="{{ asset('images/welcome-illustration.png') }}" class="illustration">

        <div class="buttons">
            <a href="{{ route('login') }}" class="btn btn-login">
                LOGIN
            </a>

            <a href="{{ route('register') }}" class="btn btn-register">
                DAFTAR AKUN
            </a>
        </div>

    </div>

    <div class="wave"></div>

</div>

</body>
</html>