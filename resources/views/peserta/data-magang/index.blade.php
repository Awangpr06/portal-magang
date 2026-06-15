<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Magang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root { --brand:#2a8fbd; --brand-dark:#185f80; --accent:#62bd42; --ink:#163342; --muted:#697b86; --line:#d7eaf2; --page:#f6fbfd; }
        * { box-sizing:border-box; }
        body { min-height:100vh; background:var(--page); color:var(--ink); font-family:Arial,sans-serif; overflow-x:auto; min-width:1200px; }
        .sidebar { width:286px; min-height:100vh; position:fixed; inset:0 auto 0 0; background:var(--brand); color:#fff; overflow-y:auto; padding:18px; z-index:1030; }
        .brand-logo { width:68px; height:68px; object-fit:contain; }
        .profile-photo { width:62px; height:62px; object-fit:cover; border:3px solid rgba(255,255,255,.7); }
        .status-dot { width:9px; height:9px; display:inline-block; border-radius:50%; background:var(--accent); }
        .sidebar a,.logout-button { width:100%; display:flex; align-items:center; gap:10px; color:#edf8fc; text-decoration:none; padding:11px 13px; border-radius:8px; border:0; background:transparent; font-size:14px; text-align:left; margin-bottom:5px; }
        .sidebar a:hover,.sidebar a.active,.logout-button:hover,.sidebar-parent.active { background:var(--brand-dark); color:#fff; }
        .sidebar-parent { display:flex; align-items:stretch; border-radius:8px; margin-bottom:5px; overflow:hidden; }
        .sidebar-parent a { flex:1; margin-bottom:0; border-radius:8px 0 0 8px; }
        .sidebar-parent a:hover { background:transparent; }
        .sidebar-toggle { width:38px; border:0; color:#fff; background:transparent; }
        .sidebar-submenu { padding:4px 0 7px 20px; }
        .sidebar-submenu a { padding:9px 12px; font-size:13px; color:#d9eef6; }
        .main-content { margin-left:286px; min-height:100vh; display:flex; flex-direction:column; }
        .top-header { background:#fff; border-bottom:1px solid var(--line); padding:16px 24px; }
        .content-wrap { width:100%; padding:24px; flex:1; }
        .overview-band { border:1px solid var(--line); background:#fff; border-radius:8px; padding:24px; }
        .summary-card,.menu-card { border:1px solid var(--line); border-radius:8px; background:#fff; height:100%; }
        .menu-card { display:block; color:inherit; text-decoration:none; transition:.2s ease; }
        .menu-card:hover { color:inherit; transform:translateY(-3px); box-shadow:0 14px 30px rgba(42,143,189,.14); }
        .icon-box { width:44px; height:44px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; font-size:20px; }
        .info-row { display:flex; justify-content:space-between; gap:16px; padding:12px 0; border-bottom:1px solid var(--line); }
        .info-row:last-child { border-bottom:0; }
        .small-muted { color:var(--muted); font-size:13px; }
        .footer-system { padding:18px 24px; text-align:center; font-size:13px; color:#6d7d86; border-top:1px solid var(--line); background:#fff; }
        @media (max-width:991px) { .sidebar { position:fixed; width:286px; min-height:100vh; } .main-content { margin-left:286px; } .content-wrap { padding:16px; } }
    </style>
</head>
<body>
    @include('peserta.partials.sidebar-toggle')
@php
    $user = $user ?? auth()->user();
    $peserta = $peserta ?? $user?->peserta ?? null;
    $perguruanTinggi = $perguruanTinggi ?? $peserta?->perguruanTinggi ?? null;
    $internship = $internship ?? $peserta?->internship ?? null;
    $mentorUser = $mentorUser ?? $internship?->mentor?->user ?? null;
    $pembimbingUser = $pembimbingUser ?? $internship?->pembimbing?->user ?? null;
    $userName = $user?->name ?? 'Peserta Magang';
    $avatar = $user?->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($userName).'&background=2a8fbd&color=ffffff';
    $accountStatus = $user?->account_status ?? 'menunggu';
    $placementInstitution = $placementInstitution ?? ($internship?->instansi ?? 'LLDIKTI Wilayah V Yogyakarta');
    $placementUnit = $placementUnit ?? ($internship?->divisi ?? $internship?->unit_kerja ?? $internship?->posisi ?? '-');
    $placementName = $placementUnit !== '-'
        ? $placementInstitution . ' - ' . $placementUnit
        : $placementInstitution;
    $placementStatus = $internship?->status ?? 'belum tersedia';
@endphp

<aside class="sidebar">
    <div class="text-center mb-3">
        <img src="{{ asset('images/logo-lldikti.png') }}" class="brand-logo mb-2" alt="Logo Portal Magang">
        <h6 class="fw-bold mb-1">Portal Magang</h6>
        <small>LLDIKTI Wilayah V Yogyakarta</small>
    </div>
    <div class="text-center mb-3">
        <img src="{{ $avatar }}" class="rounded-circle profile-photo mb-2" alt="Foto profil peserta">
        <div class="fw-bold">{{ $userName }}</div>
        <div class="small d-inline-flex align-items-center gap-2 mt-1"><span class="status-dot"></span> Online</div>
    </div>
    <hr class="border-light opacity-25">
    <a href="{{ route('peserta.dashboard') }}"><i class="bi bi-grid-fill"></i> Dashboard</a>
    <div class="sidebar-parent active">
        <a href="{{ route('peserta.data-magang') }}"><i class="bi bi-briefcase-fill"></i> Data Magang</a>
        <button class="sidebar-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#dataMagangMenu" aria-expanded="true"><i class="bi bi-chevron-down"></i></button>
    </div>
    <div class="collapse show" id="dataMagangMenu">
        <div class="sidebar-submenu">
            <a href="{{ route('peserta.data-magang.profil') }}">Profil Peserta</a>
            <a href="{{ route('peserta.data-magang.penempatan') }}">Penempatan</a>
        </div>
    </div>
    <a href="{{ route('peserta.aktivitas-magang') }}"><i class="bi bi-activity"></i> Aktivitas Magang</a>
    <a href="{{ route('peserta.dokumen') }}"><i class="bi bi-folder2-open"></i> Dokumen</a>
    <a href="{{ route('peserta.laporan') }}"><i class="bi bi-file-earmark-text-fill"></i> Laporan</a>
    <a href="{{ route('peserta.penilaian') }}"><i class="bi bi-award-fill"></i> Penilaian</a>
    <a href="{{ route('peserta.komunikasi') }}"><i class="bi bi-chat-dots-fill"></i> Komunikasi</a>
    <a href="{{ route('peserta.pengaturan') }}"><i class="bi bi-gear-fill"></i> Pengaturan Akun</a>
    <form method="POST" action="{{ route('logout') }}" class="mt-3">@csrf<button type="submit" class="logout-button"><i class="bi bi-box-arrow-right"></i> Log Out</button></form>
</aside>

<main class="main-content">
    <header class="top-header">
        <h2 class="fw-bold mb-1">Data Magang</h2>
        <div class="small-muted">Ringkasan profil peserta dan penempatan di LLDIKTI Wilayah V.</div>
    </header>

    <div class="content-wrap">
        <section class="overview-band mb-4">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                <div>
                    <div class="small-muted">Peserta Magang</div>
                    <h3 class="fw-bold mb-1">{{ $userName }}</h3>
                    <div>{{ $peserta?->nim ?? '-' }} · {{ $peserta?->jurusan ?? '-' }} · {{ $perguruanTinggi?->nama_pt ?? '-' }}</div>
                </div>
                <div class="d-flex flex-wrap align-items-start gap-2">
                    <span class="badge bg-primary">{{ ucfirst($accountStatus) }}</span>
                    <span class="badge bg-success">{{ ucfirst($placementStatus) }}</span>
                    <span class="badge bg-light text-dark border">{{ $peserta?->program_magang ?? 'Program belum tersedia' }}</span>
                </div>
            </div>
        </section>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <a class="menu-card p-3" href="{{ route('peserta.data-magang.profil') }}">
                    <span class="icon-box bg-primary mb-3"><i class="bi bi-person-vcard"></i></span>
                    <h5 class="fw-bold">Profil Peserta</h5>
                    <p class="small-muted mb-2">Identitas, kontak, dan data akademik peserta.</p>
                    <span class="text-primary fw-semibold">Buka profil <i class="bi bi-arrow-right"></i></span>
                </a>
            </div>
            <div class="col-md-4">
                <a class="menu-card p-3" href="{{ route('peserta.data-magang.penempatan') }}">
                    <span class="icon-box bg-success mb-3"><i class="bi bi-building-check"></i></span>
                    <h5 class="fw-bold">Penempatan</h5>
                    <p class="small-muted mb-2">Divisi/sub bagian, mentor, dan periode magang di LLDIKTI Wilayah V.</p>
                    <span class="text-success fw-semibold">Buka penempatan <i class="bi bi-arrow-right"></i></span>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-6">
                <section class="summary-card p-3">
                    <h5 class="fw-bold mb-3">Ringkasan Akademik</h5>
                    <div class="info-row"><span class="small-muted">Perguruan Tinggi</span><strong>{{ $perguruanTinggi?->nama_pt ?? '-' }}</strong></div>
                    <div class="info-row"><span class="small-muted">Program Studi</span><strong>{{ $peserta?->jurusan ?? '-' }}</strong></div>
                    <div class="info-row"><span class="small-muted">Fakultas</span><strong>{{ $peserta?->fakultas ?? '-' }}</strong></div>
                    <div class="info-row"><span class="small-muted">Pembimbing Akademik</span><strong>{{ $pembimbingUser?->name ?? $peserta?->pembimbing_akademik ?? '-' }}</strong></div>
                </section>
            </div>
            <div class="col-xl-6">
                <section class="summary-card p-3">
                    <h5 class="fw-bold mb-3">Ringkasan Penempatan</h5>
                    <div class="info-row"><span class="small-muted">Instansi</span><strong>{{ $internship?->instansi ?? 'LLDIKTI Wilayah V Yogyakarta' }}</strong></div>
                    <div class="info-row"><span class="small-muted">Divisi/Sub Bagian</span><strong>{{ $internship?->divisi ?? $internship?->unit_kerja ?? $internship?->posisi ?? '-' }}</strong></div>
                    <div class="info-row"><span class="small-muted">Mentor</span><strong>{{ $internship?->mentor?->user?->name ?? '-' }}</strong></div>
                    <div class="info-row"><span class="small-muted">Status</span><strong>{{ ucfirst($placementStatus) }}</strong></div>
                </section>
            </div>
        </div>
    </div>
    <footer class="footer-system">&copy; 2026 Portal Magang LLDIKTI Wilayah V Yogyakarta</footer>
</main>
<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
