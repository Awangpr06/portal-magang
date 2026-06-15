<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pilih Jenis Akun</title>
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
            background:#0e6388;
            color:white;
            overflow-x:hidden;
        }

        .register-page{
            min-height:100vh;
            position:relative;
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            padding:28px 24px 40px;
            isolation:isolate;
        }

        .register-page::before{
            content:"";
            position:absolute;
            inset:auto 0 0;
            height:34%;
            background:
                radial-gradient(circle at 10% 100%, rgba(10, 91, 126, 0.95) 0 24%, transparent 25%),
                radial-gradient(circle at 45% 110%, rgba(10, 91, 126, 0.85) 0 28%, transparent 29%),
                radial-gradient(circle at 84% 105%, rgba(10, 91, 126, 0.9) 0 26%, transparent 27%);
            z-index:-2;
        }

        .register-page::after{
            content:"";
            position:absolute;
            left:-5%;
            right:-5%;
            bottom:112px;
            height:88px;
            background:#f7eeee;
            border-radius:50% 50% 0 0 / 58% 58% 0 0;
            transform:rotate(1deg);
            z-index:-2;
        }

        .scenery{
            position:absolute;
            inset:0;
            pointer-events:none;
            overflow:hidden;
            z-index:-1;
        }

        .scenery .building{
            position:absolute;
            right:-30px;
            bottom:135px;
            width:min(300px, 22vw);
            height:min(430px, 52vh);
            border:8px solid rgba(255,255,255,0.32);
            background:
                linear-gradient(90deg, rgba(255,255,255,0.22) 1px, transparent 1px) 0 0 / 52px 100%,
                linear-gradient(rgba(255,255,255,0.22) 1px, transparent 1px) 0 0 / 100% 76px,
                rgba(98, 180, 210, 0.34);
            opacity:0.7;
            transform:skewY(7deg);
        }

        .scenery .monument{
            position:absolute;
            left:8%;
            bottom:128px;
            width:72px;
            height:215px;
            opacity:0.82;
        }

        .scenery .monument::before{
            content:"";
            position:absolute;
            left:50%;
            top:0;
            width:18px;
            height:68px;
            background:#f6c44c;
            clip-path:polygon(50% 0, 100% 100%, 0 100%);
            transform:translateX(-50%);
        }

        .scenery .monument::after{
            content:"";
            position:absolute;
            left:22px;
            right:22px;
            bottom:0;
            height:158px;
            background:#f7f1dd;
            border:4px solid #d5ad43;
            border-radius:45px 45px 8px 8px;
            box-shadow:
                0 64px 0 -40px #d5ad43,
                0 128px 0 -40px #d5ad43;
        }

        .header{
            text-align:center;
            margin-bottom:26px;
        }

        .brand{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            margin-bottom:18px;
            font-weight:800;
            letter-spacing:0.5px;
        }

        .brand img{
            height:58px;
            width:auto;
            filter:drop-shadow(0 4px 10px rgba(0,0,0,0.15));
        }

        .header h1{
            font-size:clamp(34px, 4vw, 48px);
            font-weight:900;
            line-height:1;
            margin-bottom:14px;
        }

        .header p{
            font-size:clamp(16px, 1.6vw, 20px);
            line-height:1.45;
        }

        .account-grid{
            width:100%;
            max-width:1080px;
            display:grid;
            grid-template-columns:repeat(3, minmax(0, 1fr));
            gap:22px;
        }

        .account-card{
            min-height:392px;
            border-radius:34px;
            padding:20px 34px 24px;
            color:#050505;
            text-align:center;
            display:flex;
            flex-direction:column;
            align-items:center;
            box-shadow:0 24px 40px rgba(0,0,0,0.12);
        }

        .account-card.blue{
            background:#e3f5ff;
        }

        .account-card.green{
            background:#efffe9;
        }

        .account-card.yellow{
            background:#fff6df;
        }

        .illustration{
            width:100%;
            height:132px;
            display:flex;
            align-items:center;
            justify-content:center;
            margin-bottom:8px;
            overflow:hidden;
        }

        .illustration img{
            width:178px;
            height:130px;
            object-fit:contain;
        }

        .account-card:nth-child(2) .illustration img{
            width:196px;
        }

        .account-card:nth-child(3) .illustration img{
            width:206px;
        }

        .account-card h2{
            font-size:23px;
            font-weight:900;
            margin-bottom:10px;
            line-height:1.2;
            white-space:nowrap;
        }

        .account-card p{
            min-height:82px;
            font-size:15px;
            line-height:1.5;
            letter-spacing:0.7px;
            margin-bottom:18px;
        }

        .register-button{
            width:100%;
            margin-top:auto;
            display:block;
            border-radius:16px;
            padding:14px 18px;
            color:white;
            font-size:22px;
            line-height:1;
            font-weight:900;
            text-decoration:none;
            transition:0.2s ease;
        }

        .register-button:hover{
            transform:translateY(-2px);
            filter:brightness(0.96);
        }

        .register-button.blue{
            background:#0e78ad;
        }

        .register-button.green{
            background:#62be42;
        }

        .register-button.yellow{
            background:#f5a916;
        }

        .login-link{
            margin-top:44px;
            font-size:18px;
            text-align:center;
        }

        .login-link a{
            color:#8ed8ff;
            margin-left:10px;
            text-decoration:underline;
        }

        @media(max-width:1050px){
            .register-page{
                justify-content:flex-start;
            }

            .account-grid{
                grid-template-columns:1fr;
                max-width:520px;
            }

            .account-card{
                min-height:auto;
            }

            .scenery .building,
            .scenery .monument{
                opacity:0.26;
            }

            .login-link{
                margin-top:42px;
            }
        }

        @media(max-width:560px){
            .register-page{
                padding:26px 16px 40px;
            }

            .brand{
                justify-content:center;
            }

            .account-card{
                border-radius:28px;
                padding:22px 22px 26px;
            }

            .account-card h2{
                font-size:21px;
            }

            .account-card p{
                font-size:15px;
                letter-spacing:0.5px;
            }

            .register-button{
                font-size:20px;
            }
        }
    </style>
</head>
<body>

<main class="register-page">
    <div class="scenery" aria-hidden="true">
        <div class="monument"></div>
        <div class="building"></div>
    </div>

    <header class="header">
        <div class="brand">
            <img src="{{ asset('images/logo-lldikti.png') }}" alt="Logo LLDIKTI">
        </div>

        <h1>Pilih Jenis Akun</h1>
        <p>Silahkan pilih jenis akun untuk melanjutkan proses pendaftaran</p>
    </header>

    <section class="account-grid" aria-label="Pilihan jenis akun">
        <article class="account-card blue">
            <div class="illustration">
                <img src="{{ asset('images/2.png') }}" alt="">
            </div>

            <h2>Pembimbing Akademik</h2>
            <p>Akun untuk dosen pembimbing akademik yang bertugas memantau dan mengevaluasi kegiatan magang mahasiswa</p>

            <a href="{{ route('register', ['role' => 'pembimbing']) }}" class="register-button blue">
                DAFTAR
            </a>
        </article>

        <article class="account-card green">
            <div class="illustration">
                <img src="{{ asset('images/3.png') }}" alt="">
            </div>

            <h2>Peserta Magang</h2>
            <p>Akun untuk mahasiswa yang akan mengikuti program magang dan mengelola aktivitas magangnya melalui sistem</p>

            <a href="{{ route('register', ['role' => 'peserta']) }}" class="register-button green">
                DAFTAR
            </a>
        </article>

        <article class="account-card yellow">
            <div class="illustration">
                <img src="{{ asset('images/4.png') }}" alt="">
            </div>

            <h2>Mentor</h2>
            <p>Akun untuk mentor dari instansi yang bertugas membimbing peserta magang selama kegiatan berlangsung</p>

            <a href="{{ route('register', ['role' => 'mentor']) }}" class="register-button yellow">
                DAFTAR
            </a>
        </article>
    </section>

    <p class="login-link">
        Sudah memiliki akun?
        <a href="{{ route('login') }}">Login</a>
    </p>
</main>

</body>
</html>
