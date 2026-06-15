<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Custom CSS --}}
    <style>

        body{
            background-color:#f4f6f9;
        }

        .sidebar{
            width:270px;
            min-height:100vh;
            background:#0b5f86;
            color:white;
            position:fixed;
            left:0;
            top:0;
            max-height:100vh;
            overflow-y:auto;
            overscroll-behavior:contain;
            scrollbar-gutter:stable;
        }

        .sidebar a{
            color:white;
            text-decoration:none;
            display:block;
            padding:11px 14px;
            border-radius:10px;
            margin-bottom:5px;
            font-size:14px;
        }

        .sidebar a:hover{
            background:#0a4f73;
        }

        .sidebar-profile{
            background:rgba(255,255,255,.16);
            border:1px solid rgba(255,255,255,.22);
            border-radius:12px;
            padding:12px;
        }

        .sidebar-profile .avatar{
            width:58px;
            height:58px;
            border-radius:50%;
            object-fit:cover;
            border:3px solid rgba(255,255,255,.35);
            box-shadow:0 8px 18px rgba(0,0,0,.12);
        }

        .sidebar a.active,
        .sidebar-parent-menu.active{
            background:#0a4f73;
        }

        .sidebar-menu-group{
            margin-bottom:5px;
        }

        .sidebar-parent-menu{
            display:flex;
            align-items:center;
            border-radius:10px;
            overflow:hidden;
        }

        .sidebar-parent-menu .sidebar-parent-link{
            flex:1;
            min-width:0;
            margin-bottom:0;
            border-radius:10px 0 0 10px;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
            padding-right:8px;
        }

        .sidebar-parent-menu .sidebar-parent-link:hover{
            background:transparent;
        }

        .sidebar-toggle{
            width:32px;
            min-width:32px;
            flex:0 0 32px;
            align-self:stretch;
            color:white;
            background:transparent;
            border:0;
            border-radius:0 10px 10px 0;
            padding:0;
            position:relative;
            z-index:1;
        }

        .sidebar-toggle:hover{
            background:#094967;
        }

        .sidebar-toggle[aria-expanded="true"] .bi-chevron-down{
            transform:rotate(180deg);
        }

        .sidebar-toggle .bi-chevron-down{
            transition:0.2s ease;
        }

        .sidebar-submenu{
            padding:5px 0 3px 18px;
        }

        .sidebar-submenu a{
            font-size:14px;
            padding:9px 16px;
            margin-bottom:4px;
            border-radius:8px;
            color:#e8f4f9;
        }

        .sidebar-submenu a.active,
        .sidebar-submenu a:hover{
            background:#094967;
            color:white;
        }

        .sidebar .logout-button{
            width:100%;
            color:white;
            text-align:left;
            background:transparent;
            border:0;
            display:block;
            padding:11px 14px;
            border-radius:10px;
            margin-bottom:5px;
            font-size:14px;
        }

        .sidebar .logout-button:hover{
            background:#0a4f73;
        }

        .main-content{
            margin-left:270px;
            padding:20px;
        }

        .card-dashboard{
            border:none;
            border-radius:20px;
            transition:0.3s;
        }

        .card-dashboard:hover{
            transform:translateY(-5px);
            box-shadow:0 10px 25px rgba(0,0,0,0.1);
        }

        .banner{
            border-radius:25px;
            background:linear-gradient(135deg,#0b5f86,#0a4f73);
            color:white;
            overflow:hidden;
        }

        .banner .btn,
        .sidebar .badge.bg-success{
            background-color:#62bd42 !important;
            border-color:#62bd42 !important;
        }

        .activity-list{
            max-height:400px;
            overflow-y:auto;
        }

        .footer-system{
            text-align:center;
            padding:20px;
            font-size:14px;
            color:#777;
        }

    </style>

    @stack('styles')
</head>
<body>

    {{-- Sidebar --}}
    @include('admin.partials.sidebar')

    <div class="main-content">

        {{-- Header --}}
        @include('admin.partials.header')

        {{-- Content --}}
        @yield('content')

        {{-- Footer --}}
        @include('admin.partials.footer')

    </div>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>

    @stack('scripts')

</body>
</html>
