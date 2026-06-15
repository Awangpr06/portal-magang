<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { background:#f4f6f9; color:#163342; }
        .sidebar { width:270px; min-height:100vh; background:#2a8fbd; color:#fff; position:fixed; left:0; top:0; max-height:100vh; overflow-y:auto; overscroll-behavior:contain; scrollbar-gutter:stable; }
        .sidebar a, .sidebar .logout-button { color:#fff; text-decoration:none; display:block; padding:11px 14px; border-radius:10px; margin-bottom:5px; font-size:14px; background:transparent; border:0; width:100%; text-align:left; }
        .sidebar a:hover, .sidebar a.active, .sidebar .logout-button:hover { background:#1f739a; }
        .sidebar-profile { background:rgba(255,255,255,.16); border:1px solid rgba(255,255,255,.22); border-radius:12px; padding:12px; }
        .sidebar-profile .avatar { width:58px; height:58px; border-radius:50%; object-fit:cover; border:3px solid rgba(255,255,255,.35); box-shadow:0 8px 18px rgba(0,0,0,.12); }
        .sidebar-menu-group { margin-bottom:5px; }
        .sidebar-parent-menu { display:flex; align-items:center; border-radius:10px; overflow:hidden; }
        .sidebar-parent-menu.active { background:#1f739a; }
        .sidebar-parent-menu .sidebar-parent-link { flex:1; min-width:0; margin-bottom:0; border-radius:10px 0 0 10px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; padding-right:8px; }
        .sidebar-parent-menu .sidebar-parent-link:hover { background:transparent; }
        .sidebar-toggle { width:32px; min-width:32px; align-self:stretch; color:#fff; background:transparent; border:0; border-radius:0 10px 10px 0; padding:0; }
        .sidebar-toggle:hover { background:#185f80; }
        .sidebar-toggle[aria-expanded="true"] .bi-chevron-down { transform:rotate(180deg); }
        .sidebar-toggle .bi-chevron-down { transition:.2s ease; }
        .sidebar-submenu { padding:5px 0 3px 18px; }
        .sidebar-submenu a { font-size:14px; padding:9px 16px; margin-bottom:4px; border-radius:8px; color:#e8f4f9; }
        .sidebar-submenu a.active,
        .sidebar-submenu a:hover { background:#185f80; color:#fff; }
        .main-content { margin-left:270px; padding:20px; }
        .page-title { font-weight:700; color:#163342; }
        .card-soft, .stat-card, .table-card, .filter-card { border:0; border-radius:8px; }
        .stat-card { transition:.2s ease; }
        .stat-card:hover { transform:translateY(-3px); box-shadow:0 12px 28px rgba(11,95,134,.16); }
        .stat-icon, .menu-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
        .hero-panel { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; overflow:hidden; }
        .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .breadcrumb a { color:#2a8fbd; text-decoration:none; }
        .footer-system { text-align:center; padding:20px; font-size:14px; color:#777; }
        @media (max-width: 991px) {
            .sidebar { position:static; width:100%; min-height:auto; max-height:none; }
            .main-content { margin-left:0; }
        }
    </style>

    @stack('styles')
</head>
<body>
    @include('pembimbing.partials.sidebar')

    <main class="main-content">
        @include('pembimbing.partials.header')
        @yield('content')
        @include('pembimbing.partials.footer')
    </main>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
