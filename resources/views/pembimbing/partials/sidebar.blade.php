@php
    $pembimbingAvatar = auth()->user()?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'Pembimbing Akademik') . '&background=ffffff&color=2a8fbd&size=160';
@endphp
<aside class="sidebar p-3">
    <div class="text-center mb-4">
        <img src="{{ asset('images/logo-lldikti.png') }}" width="90" class="mb-3" alt="Logo LLDIKTI">
        <h5 class="mb-1">LLDIKTI Wilayah V</h5>
        <span class="badge bg-success">Pembimbing Akademik</span>
    </div>

    <div class="sidebar-profile text-center mb-3">
        <img src="{{ $pembimbingAvatar }}"
             class="avatar mb-2 js-pembimbing-avatar"
             alt="Avatar Pembimbing Akademik">
        <div class="fw-bold">{{ auth()->user()->name ?? 'Pembimbing Akademik' }}</div>
        <small class="d-block text-white-50">{{ auth()->user()->email ?? 'pembimbing@example.com' }}</small>
        <span class="badge bg-light text-dark mt-2">Akun Aktif</span>
    </div>

    <hr>

    <a href="{{ route('pembimbing.dashboard') }}" class="{{ request()->routeIs('pembimbing.dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid-fill"></i> Dashboard
    </a>
    <a href="{{ route('pembimbing.mahasiswa') }}" class="{{ request()->routeIs('pembimbing.mahasiswa') ? 'active' : '' }}">
        <i class="bi bi-people-fill"></i> Mahasiswa Bimbingan
    </a>

    @php
        $monitoringOpen = request()->routeIs('pembimbing.monitoring.*');
    @endphp

    <div class="sidebar-menu-group">
        <div class="sidebar-parent-menu {{ $monitoringOpen ? 'active' : '' }}">
            <a href="{{ route('pembimbing.monitoring.index') }}" class="sidebar-parent-link">
                <i class="bi bi-activity"></i> Monitoring
            </a>

            <button class="sidebar-toggle"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#monitoringSubmenu"
                    aria-expanded="{{ $monitoringOpen ? 'true' : 'false' }}"
                    aria-controls="monitoringSubmenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>

        <div class="collapse {{ $monitoringOpen ? 'show' : '' }}" id="monitoringSubmenu">
            <div class="sidebar-submenu">
                <a href="{{ route('pembimbing.monitoring.absensi') }}"
                   class="{{ request()->routeIs('pembimbing.monitoring.absensi') ? 'active' : '' }}">
                    Absensi
                </a>
                <a href="{{ route('pembimbing.monitoring.kegiatan') }}"
                   class="{{ request()->routeIs('pembimbing.monitoring.kegiatan') ? 'active' : '' }}">
                    Kegiatan Magang
                </a>
                <a href="{{ route('pembimbing.monitoring.kerjasama') }}"
                   class="{{ request()->routeIs('pembimbing.monitoring.kerjasama') ? 'active' : '' }}">
                    Kerja Sama
                </a>
                <a href="{{ route('pembimbing.monitoring.status') }}"
                   class="{{ request()->routeIs('pembimbing.monitoring.status') ? 'active' : '' }}">
                    Status Magang
                </a>
                <a href="{{ route('pembimbing.monitoring.laporan') }}"
                   class="{{ request()->routeIs('pembimbing.monitoring.laporan') ? 'active' : '' }}">
                    Laporan
                </a>
            </div>
        </div>
    </div>

    @php
        $assessmentOpen = request()->routeIs('pembimbing.penilaian.*');
    @endphp

    <div class="sidebar-menu-group">
        <div class="sidebar-parent-menu {{ $assessmentOpen ? 'active' : '' }}">
            <a href="{{ route('pembimbing.penilaian.index') }}" class="sidebar-parent-link">
                <i class="bi bi-award-fill"></i> Penilaian
            </a>

            <button class="sidebar-toggle"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#penilaianSubmenu"
                    aria-expanded="{{ $assessmentOpen ? 'true' : 'false' }}"
                    aria-controls="penilaianSubmenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>

        <div class="collapse {{ $assessmentOpen ? 'show' : '' }}" id="penilaianSubmenu">
            <div class="sidebar-submenu">
                <a href="{{ route('pembimbing.penilaian.input') }}"
                   class="{{ request()->routeIs('pembimbing.penilaian.input') ? 'active' : '' }}">
                    Input Nilai
                </a>
                <a href="{{ route('pembimbing.penilaian.rekap') }}"
                   class="{{ request()->routeIs('pembimbing.penilaian.rekap') ? 'active' : '' }}">
                    Rekap Nilai
                </a>
            </div>
        </div>
    </div>

    @php
        $communicationOpen = request()->routeIs('pembimbing.komunikasi*');
    @endphp

    <div class="sidebar-menu-group">
        <div class="sidebar-parent-menu {{ $communicationOpen ? 'active' : '' }}">
            <a href="{{ route('pembimbing.komunikasi') }}" class="sidebar-parent-link">
                <i class="bi bi-chat-dots-fill"></i> Komunikasi
            </a>

            <button class="sidebar-toggle"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#komunikasiSubmenu"
                    aria-expanded="{{ $communicationOpen ? 'true' : 'false' }}"
                    aria-controls="komunikasiSubmenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>

        <div class="collapse {{ $communicationOpen ? 'show' : '' }}" id="komunikasiSubmenu">
            <div class="sidebar-submenu">
                <a href="{{ route('pembimbing.komunikasi.pesan') }}"
                   class="{{ request()->routeIs('pembimbing.komunikasi.pesan') ? 'active' : '' }}">
                    Pesan
                </a>
                <a href="{{ route('pembimbing.komunikasi.pengumuman') }}"
                   class="{{ request()->routeIs('pembimbing.komunikasi.pengumuman') ? 'active' : '' }}">
                    Pengumuman
                </a>
                <a href="{{ route('pembimbing.komunikasi.notifikasi') }}"
                   class="{{ request()->routeIs('pembimbing.komunikasi.notifikasi') ? 'active' : '' }}">
                    Notifikasi
                </a>
            </div>
        </div>
    </div>
    @php
        $settingsOpen = request()->routeIs('pembimbing.pengaturan*');
    @endphp

    <div class="sidebar-menu-group">
        <div class="sidebar-parent-menu {{ $settingsOpen ? 'active' : '' }}">
            <a href="{{ route('pembimbing.pengaturan') }}" class="sidebar-parent-link">
                <i class="bi bi-gear-fill"></i> Pengaturan Akun
            </a>

            <button class="sidebar-toggle"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#pengaturanSubmenu"
                    aria-expanded="{{ $settingsOpen ? 'true' : 'false' }}"
                    aria-controls="pengaturanSubmenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>

        <div class="collapse {{ $settingsOpen ? 'show' : '' }}" id="pengaturanSubmenu">
            <div class="sidebar-submenu">
                <a href="{{ route('pembimbing.pengaturan.profil') }}"
                   class="{{ request()->routeIs('pembimbing.pengaturan.profil') ? 'active' : '' }}">
                    Profil
                </a>
                <a href="{{ route('pembimbing.pengaturan.password') }}"
                   class="{{ request()->routeIs('pembimbing.pengaturan.password') ? 'active' : '' }}">
                    Ubah Password
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-button">
            <i class="bi bi-box-arrow-right"></i> Log Out
        </button>
    </form>
</aside>
