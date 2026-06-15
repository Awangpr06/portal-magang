<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Portal Magang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family: Arial, sans-serif;
            background:#f3f3f3;
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            padding:24px;
        }

        .container{
            width:100%;
            max-width:1180px;
            height:calc(100vh - 48px);
            max-height:680px;
            min-height:560px;
            display:grid;
            grid-template-columns:1fr 1fr;
            border-radius:24px;
            overflow:hidden;
        }

        .left{
            background:white;
            border:3px solid #0b6f9f;
            padding:32px 40px;
            display:flex;
            flex-direction:column;
            justify-content:center;
        }

        .left h1{
            font-size:clamp(48px, 5vw, 70px);
            color:#0b5f86;
            font-weight:900;
            line-height:1;
        }

        .left h2{
            font-size:clamp(30px, 3vw, 42px);
            color:#0b5f86;
            margin-bottom:24px;
            font-weight:400;
        }

        .left img{
            width:100%;
            max-width:460px;
            max-height:52vh;
            object-fit:contain;
            margin:auto;
        }

        .right{
            background:#0b6f9f;
            padding:32px 64px;
            color:white;
            display:flex;
            flex-direction:column;
            justify-content:center;
        }

        .brand{
            display:flex;
            justify-content:flex-end;
            align-items:center;
            gap:15px;
            margin-bottom:26px;
            font-weight:800;
            font-size:20px;
        }

        .brand img{
            height:50px;
        }

        .right h1{
            font-size:clamp(44px, 4vw, 58px);
            font-weight:900;
            line-height:1;
        }

        .right p{
            font-size:clamp(22px, 2vw, 28px);
            margin-bottom:24px;
        }

        label{
            display:block;
            margin-top:14px;
            margin-bottom:8px;
            font-size:18px;
        }

        select,
        input{
            width:100%;
            padding:12px 14px;
            border:none;
            outline:none;
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

        .remember{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-top:18px;
            margin-bottom:20px;
            font-size:15px;
        }

        .remember-left{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .remember-left input{
            width:20px;
            height:20px;
        }

        .remember a{
            color:#ffe45c;
            text-decoration:none;
        }

        .btn{
            width:100%;
            padding:13px;
            border:none;
            border-radius:35px;
            font-size:18px;
            color:white;
            cursor:pointer;
            margin-bottom:12px;
            font-weight:bold;
        }

        .btn-login{
            background:#74d64a;
        }

        .btn-reset{
            background:#ff6700;
        }

        .error-box{
            margin-top:15px;
            background:#ffe3de;
            color:#333;
            padding:15px;
            border-radius:10px;
        }

        .footer{
            margin-top:18px;
            font-size:14px;
            opacity:0.7;
            letter-spacing:2px;
        }

        @media(max-width:900px){

            .container{
                grid-template-columns:1fr;
                height:auto;
                min-height:calc(100vh - 48px);
                max-height:none;
            }

            .left{
                display:none;
            }

            .right{
                padding:40px 30px;
            }

            .right h1{
                font-size:48px;
            }

            .brand{
                justify-content:center;
                text-align:center;
            }

        }

    </style>

</head>
<body>

<div class="container">

    {{-- LEFT --}}
    <div class="left">

        <h1>HALLO!</h1>
        <h2>Warga Lima</h2>

        <img src="{{ asset('images/login-illustration.png') }}">

    </div>

    {{-- RIGHT --}}
    <div class="right">

        <div class="brand">
            <span>PORTAL MAGANG & KEARSIPAN</span>
            <img src="{{ asset('images/logo-lldikti.png') }}">
        </div>

        <h1>LOGIN</h1>
        <p>Masuk ke akun Anda!</p>

        {{-- ERROR --}}
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">

            @csrf

            <label>Login Sebagai</label>

            <select name="role">
                <option value="super_admin">Super Admin</option>
                <option value="mentor">Mentor</option>
                <option value="pembimbing">Pembimbing Akademik</option>
                <option value="peserta">Peserta Magang</option>
            </select>

            {{-- EMAIL --}}
            <label>Email</label>

            <input
                type="email"
                name="email"
                class="line-input"
                placeholder="Masukkan email"
                value="{{ old('email') }}"
                required
            >

            <x-input-error :messages="$errors->get('email')" class="mt-2" />

            {{-- PASSWORD --}}
            <label>Password</label>

            <input
                type="password"
                name="password"
                class="line-input"
                placeholder="Masukkan password"
                required
            >

            <x-input-error :messages="$errors->get('password')" class="mt-2" />

            {{-- REMEMBER --}}
            <div class="remember">

                <div class="remember-left">
                    <input type="checkbox" name="remember">
                    <span>Remember me</span>
                </div>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        Klik Reset
                    </a>
                @endif

            </div>

            {{-- BUTTON --}}
            <button type="submit" class="btn btn-login">
                Submit
            </button>

            <button type="reset" class="btn btn-reset">
                Clear
            </button>

        </form>

        <div class="footer">
            &copy; 2026 Sistem Portal Magang LLDIKTI Wilayah V
        </div>

    </div>

</div>

</body>
</html>
