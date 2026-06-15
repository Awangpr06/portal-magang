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
        body { background:#f6fbfd; color:#163342; }
        .sidebar { width:280px; min-height:100vh; background:#2a8fbd; color:#fff; position:fixed; left:0; top:0; max-height:100vh; overflow-y:auto; overscroll-behavior:contain; scrollbar-gutter:stable; z-index:1040; transition:.2s ease; }
        .sidebar a, .sidebar .logout-button { color:#edf8fc; text-decoration:none; display:flex; align-items:center; gap:10px; padding:11px 14px; border-radius:8px; margin-bottom:5px; font-size:14px; background:transparent; border:0; width:100%; text-align:left; font-weight:600; }
        .sidebar a:hover, .sidebar a.active, .sidebar .logout-button:hover { background:#1f739a; color:#fff; }
        .sidebar-profile { background:rgba(255,255,255,.28); border:1px solid rgba(255,255,255,.34); border-radius:8px; padding:12px; }
        .main-content { margin-left:280px; padding:20px; transition:.2s ease; }
        body.sidebar-collapsed .sidebar { transform:translateX(-100%); }
        body.sidebar-collapsed .main-content { margin-left:0; }
        .top-header { background:#fff; border:1px solid #d7eaf2; border-radius:8px; padding:14px; box-shadow:0 8px 22px rgba(22,51,66,.05); }
        .page-title { font-weight:700; color:#163342; }
        .card-soft, .stat-card, .table-card, .filter-card { border:0; border-radius:8px; }
        .stat-card { transition:.2s ease; }
        .stat-card:hover { transform:translateY(-3px); box-shadow:0 12px 28px rgba(42,143,189,.18); }
        .stat-icon, .menu-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
        .hero-panel { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; overflow:hidden; }
        .hero-illustration { max-height:150px; object-fit:contain; filter:drop-shadow(0 12px 18px rgba(22,51,66,.18)); }
        .quick-action-card { border:1px solid #d7eaf2; border-radius:8px; background:#fff; color:#163342; text-decoration:none; padding:14px; display:flex; align-items:center; gap:12px; height:100%; transition:.2s ease; }
        .quick-action-card:hover { border-color:#2a8fbd; transform:translateY(-2px); box-shadow:0 10px 22px rgba(42,143,189,.16); color:#163342; }
        .quick-action-card .menu-icon { background:#2a8fbd; color:#fff; flex:0 0 auto; }
        .activity-row, .document-row, .progress-row { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; }
        .progress { height:9px; background:#e8eef2; }
        .progress-bar { background:#2a8fbd; }
        .table thead th { background:#eef8fc; color:#3b5664; font-size:13px; white-space:nowrap; }
        .table tbody tr:hover { background:#f7fcfe; }
        .breadcrumb a { color:#2a8fbd; text-decoration:none; }
        .btn-warning { --bs-btn-color:#fff; --bs-btn-bg:#2a8fbd; --bs-btn-border-color:#2a8fbd; --bs-btn-hover-color:#fff; --bs-btn-hover-bg:#1f739a; --bs-btn-hover-border-color:#1f739a; --bs-btn-active-color:#fff; --bs-btn-active-bg:#185f80; --bs-btn-active-border-color:#185f80; }
        .btn-outline-warning { --bs-btn-color:#2a8fbd; --bs-btn-border-color:#2a8fbd; --bs-btn-hover-color:#fff; --bs-btn-hover-bg:#2a8fbd; --bs-btn-hover-border-color:#2a8fbd; --bs-btn-active-color:#fff; --bs-btn-active-bg:#1f739a; --bs-btn-active-border-color:#1f739a; }
        .footer-system { text-align:center; padding:20px; font-size:13px; color:#777; }
        .modal { z-index:1080; }
        .modal-backdrop { z-index:1070; }
        @media (max-width: 991px) {
            .sidebar { transform:translateX(-100%); max-height:none; }
            body.sidebar-open .sidebar { transform:translateX(0); box-shadow:0 0 0 999px rgba(0,0,0,.35); }
            .main-content { margin-left:0; }
            body.sidebar-collapsed .sidebar { transform:translateX(-100%); }
        }
    </style>

    @stack('styles')
</head>
<body>
    @include('mentor.partials.sidebar')

    <main class="main-content">
        @include('mentor.partials.header')
        @yield('content')
        @include('mentor.partials.footer')
    </main>

    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggle = document.getElementById('sidebarToggle');
            if (!toggle) return;
            toggle.addEventListener('click', () => {
                if (window.matchMedia('(max-width: 991px)').matches) {
                    document.body.classList.toggle('sidebar-open');
                    return;
                }
                document.body.classList.toggle('sidebar-collapsed');
            });
        });
    </script>
    @stack('scripts')
    <script>
        document.querySelectorAll('.modal').forEach((modal) => {
            document.body.appendChild(modal);
        });

        document.addEventListener('click', (event) => {
            const actionButton = event.target.closest('button[data-action][data-id]');
            if (!actionButton) return;

            window.setTimeout(() => {
                if (document.querySelector('.modal.show')) return;
                const action = actionButton.dataset.action || 'detail';
                const isDetailAction = /detail|lihat|dokumen|lampiran|riwayat/i.test(action);
                if (isDetailAction) return;
                if (action === 'edit tugas') return;
                const modalElement = document.getElementById(isDetailAction ? 'detailModal' : 'confirmModal');
                if (!modalElement || typeof bootstrap === 'undefined') return;

                const detailContent = modalElement.querySelector('#detailContent');
                if (detailContent && !detailContent.textContent.trim()) {
                    detailContent.innerHTML = `<div class="alert alert-info mb-0">Informasi untuk aksi <strong>${action}</strong> sedang ditampilkan.</div>`;
                }

                const confirmSummary = modalElement.querySelector('#confirmSummary');
                if (confirmSummary && !confirmSummary.textContent.trim()) {
                    confirmSummary.innerHTML = `<div class="alert alert-warning mb-0">Konfirmasi aksi <strong>${action}</strong>.</div>`;
                }

                bootstrap.Modal.getOrCreateInstance(modalElement).show();
            }, 50);
        });
    </script>
</body>
</html>
