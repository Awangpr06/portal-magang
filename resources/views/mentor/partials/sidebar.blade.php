@php
    $mentorAvatar = auth()->user()?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'Mentor Lapangan') . '&background=fff6df&color=8a5a00';
@endphp
<aside class="sidebar p-3">
    <div class="text-center mb-3">
        <img src="{{ asset('images/logo-lldikti.png') }}" width="84" class="mb-2" alt="Logo LLDIKTI">
        <h6 class="fw-bold mb-0">Portal Magang</h6>
        <small>LLDIKTI Wilayah V Yogyakarta</small>
    </div>

    <div class="sidebar-profile text-center mb-3">
        <img src="{{ $mentorAvatar }}" class="rounded-circle mb-2" width="58" height="58" alt="Avatar Mentor">
        <div class="fw-bold">{{ auth()->user()->name ?? 'Mentor Lapangan' }}</div>
        <span class="badge bg-light text-dark mt-1">Akun Aktif</span>
    </div>

    <hr>

    <a href="{{ route('mentor.dashboard') }}" class="{{ request()->routeIs('mentor.dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid-fill"></i> Dashboard
    </a>
    <a href="{{ route('mentor.peserta') }}" class="{{ request()->routeIs('mentor.peserta') ? 'active' : '' }}">
        <i class="bi bi-people-fill"></i> Daftar Peserta Magang
    </a>
    @php
        $monitoringOpen = request()->routeIs('mentor.monitoring*') || request()->routeIs('mentor.absensi') || request()->routeIs('mentor.laporan');
    @endphp
    <div class="sidebar-menu-group">
        <div class="d-flex align-items-center rounded {{ $monitoringOpen ? 'active' : '' }}">
            <a href="{{ route('mentor.monitoring') }}" class="flex-grow-1 mb-0 {{ request()->routeIs('mentor.monitoring') ? 'active' : '' }}">
                <i class="bi bi-activity"></i> Monitoring
            </a>
            <button class="btn btn-sm border-0"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mentorMonitoringSubmenu"
                    aria-expanded="{{ $monitoringOpen ? 'true' : 'false' }}"
                    aria-controls="mentorMonitoringSubmenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
        <div class="collapse {{ $monitoringOpen ? 'show' : '' }}" id="mentorMonitoringSubmenu">
            <div class="ps-3 pt-1">
                <a href="{{ route('mentor.monitoring.absensi') }}" class="{{ request()->routeIs('mentor.monitoring.absensi') || request()->routeIs('mentor.absensi') ? 'active' : '' }}">Absensi</a>
                <a href="{{ route('mentor.monitoring.penugasan') }}" class="{{ request()->routeIs('mentor.monitoring.penugasan') ? 'active' : '' }}">Penugasan</a>
                <a href="{{ route('mentor.monitoring.status') }}" class="{{ request()->routeIs('mentor.monitoring.status') ? 'active' : '' }}">Status Magang</a>
                <a href="{{ route('mentor.laporan') }}" class="{{ request()->routeIs('mentor.laporan') ? 'active' : '' }}">Laporan</a>
            </div>
        </div>
    </div>
    @php
        $penilaianOpen = request()->routeIs('mentor.penilaian*');
    @endphp
    <div class="sidebar-menu-group">
        <div class="d-flex align-items-center rounded {{ $penilaianOpen ? 'active' : '' }}">
            <a href="{{ route('mentor.penilaian') }}" class="flex-grow-1 mb-0 {{ request()->routeIs('mentor.penilaian') ? 'active' : '' }}">
                <i class="bi bi-award-fill"></i> Penilaian
            </a>
            <button class="btn btn-sm border-0"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mentorPenilaianSubmenu"
                    aria-expanded="{{ $penilaianOpen ? 'true' : 'false' }}"
                    aria-controls="mentorPenilaianSubmenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
        <div class="collapse {{ $penilaianOpen ? 'show' : '' }}" id="mentorPenilaianSubmenu">
            <div class="ps-3 pt-1">
                <a href="{{ route('mentor.penilaian.input') }}" class="{{ request()->routeIs('mentor.penilaian.input') ? 'active' : '' }}">Input Nilai</a>
                <a href="{{ route('mentor.penilaian.rekap') }}" class="{{ request()->routeIs('mentor.penilaian.rekap') ? 'active' : '' }}">Rekap Nilai</a>
            </div>
        </div>
    </div>
    @php
        $komunikasiOpen = request()->routeIs('mentor.komunikasi*') || request()->routeIs('mentor.notifikasi');
    @endphp
    <div class="sidebar-menu-group">
        <div class="d-flex align-items-center rounded {{ $komunikasiOpen ? 'active' : '' }}">
            <a href="{{ route('mentor.komunikasi') }}" class="flex-grow-1 mb-0 {{ request()->routeIs('mentor.komunikasi') ? 'active' : '' }}">
                <i class="bi bi-chat-dots-fill"></i> Komunikasi
            </a>
            <button class="btn btn-sm border-0"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mentorKomunikasiSubmenu"
                    aria-expanded="{{ $komunikasiOpen ? 'true' : 'false' }}"
                    aria-controls="mentorKomunikasiSubmenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
        <div class="collapse {{ $komunikasiOpen ? 'show' : '' }}" id="mentorKomunikasiSubmenu">
            <div class="ps-3 pt-1">
                <a href="{{ route('mentor.komunikasi.pesan') }}" class="{{ request()->routeIs('mentor.komunikasi.pesan') ? 'active' : '' }}">Pesan</a>
                <a href="{{ route('mentor.komunikasi.pengumuman') }}" class="{{ request()->routeIs('mentor.komunikasi.pengumuman') ? 'active' : '' }}">Pengumuman</a>
                <a href="{{ route('mentor.notifikasi') }}" class="{{ request()->routeIs('mentor.notifikasi') ? 'active' : '' }}">Notifikasi</a>
            </div>
        </div>
    </div>
    @php
        $pengaturanOpen = request()->routeIs('mentor.pengaturan*');
    @endphp
    <div class="sidebar-menu-group">
        <div class="d-flex align-items-center rounded {{ $pengaturanOpen ? 'active' : '' }}">
            <a href="{{ route('mentor.pengaturan') }}" class="flex-grow-1 mb-0 {{ request()->routeIs('mentor.pengaturan') ? 'active' : '' }}">
                <i class="bi bi-gear-fill"></i> Pengaturan Akun
            </a>
            <button class="btn btn-sm border-0"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mentorPengaturanSubmenu"
                    aria-expanded="{{ $pengaturanOpen ? 'true' : 'false' }}"
                    aria-controls="mentorPengaturanSubmenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>
        <div class="collapse {{ $pengaturanOpen ? 'show' : '' }}" id="mentorPengaturanSubmenu">
            <div class="ps-3 pt-1">
                <a href="{{ route('mentor.pengaturan.profil') }}" class="{{ request()->routeIs('mentor.pengaturan.profil') ? 'active' : '' }}">Profil</a>
                <a href="{{ route('mentor.pengaturan.password') }}" class="{{ request()->routeIs('mentor.pengaturan.password') ? 'active' : '' }}">Ubah Password</a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button type="submit" class="logout-button">
            <i class="bi bi-box-arrow-right"></i> Log Out
        </button>
    </form>
</aside>
