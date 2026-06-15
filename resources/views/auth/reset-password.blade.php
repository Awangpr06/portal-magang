<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Password Baru</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            min-height:100vh;
            font-family:Arial, sans-serif;
            background:#f3f3f3;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:24px;
        }

        .reset-card{
            width:100%;
            max-width:540px;
            background:#0b6f9f;
            color:white;
            border-radius:24px;
            padding:42px;
            box-shadow:0 18px 40px rgba(0,0,0,0.12);
        }

        .brand{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            margin-bottom:34px;
            font-weight:800;
            font-size:18px;
        }

        .brand img{
            height:48px;
        }

        h1{
            font-size:42px;
            line-height:1;
            margin-bottom:14px;
        }

        p{
            font-size:17px;
            line-height:1.5;
            margin-bottom:24px;
        }

        label{
            display:block;
            margin-top:16px;
            margin-bottom:8px;
            font-size:18px;
        }

        input{
            width:100%;
            padding:13px 14px;
            border:0;
            outline:0;
            font-size:16px;
        }

        .line-input{
            background:transparent;
            border-bottom:2px solid rgba(255,255,255,0.8);
            color:white;
            padding-left:0;
        }

        .line-input::placeholder{
            color:white;
        }

        .btn{
            width:100%;
            border:0;
            border-radius:35px;
            padding:14px;
            margin-top:28px;
            color:white;
            background:#74d64a;
            font-weight:bold;
            font-size:17px;
            cursor:pointer;
        }

        .error-box{
            padding:12px 14px;
            border-radius:10px;
            margin-top:10px;
            color:#163342;
            background:#ffe3de;
        }

        @media(max-width:600px){
            .reset-card{
                padding:32px 24px;
            }

            h1{
                font-size:34px;
            }

            .brand{
                align-items:flex-start;
                flex-direction:column-reverse;
            }
        }
    </style>
</head>
<body>

<div class="reset-card">
    <div class="brand">
        <span>PORTAL MAGANG & KEARSIPAN</span>
        <img src="{{ asset('images/logo-lldikti.png') }}" alt="Logo LLDIKTI">
    </div>

    <h1>Password Baru</h1>
    <p>Silakan masukkan password baru untuk akun Anda.</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <label for="email">Email</label>
        <input
            id="email"
            type="email"
            name="email"
            class="line-input"
            placeholder="Masukkan email"
            value="{{ old('email', $request->email) }}"
            required
            autofocus
            autocomplete="username"
        >

        @error('email')
            <div class="error-box">{{ $message }}</div>
        @enderror

        <label for="password">Password Baru</label>
        <input
            id="password"
            type="password"
            name="password"
            class="line-input"
            placeholder="Masukkan password baru"
            required
            autocomplete="new-password"
        >

        @error('password')
            <div class="error-box">{{ $message }}</div>
        @enderror

        <label for="password_confirmation">Konfirmasi Password</label>
        <input
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            class="line-input"
            placeholder="Ulangi password baru"
            required
            autocomplete="new-password"
        >

        @error('password_confirmation')
            <div class="error-box">{{ $message }}</div>
        @enderror

        <button type="submit" class="btn">
            Simpan Password Baru
        </button>
    </form>
</div>

</body>
</html>
