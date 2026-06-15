@php
    $adminAvatar = auth()->user()?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'Admin') . '&background=ffffff&color=0b5f86&size=160';
@endphp
<div class="sidebar p-3">

    <div class="text-center mb-4">
        <img src="{{ asset('images/logo-lldikti.png') }}"
             width="90"
             class="mb-3">

        <h5>LLDIKTI Wilayah V</h5>
    </div>

    <div class="sidebar-profile text-center mb-3">
        <img src="{{ $adminAvatar }}"
             class="avatar mb-2 js-admin-avatar"
             alt="Avatar Admin">
        <div class="fw-bold">{{ auth()->user()->name ?? 'Admin' }}</div>
        <small class="d-block text-white-50">{{ auth()->user()->email ?? 'admin@lldikti5.id' }}</small>
        <span class="badge bg-light text-dark mt-2">Akun Aktif</span>
    </div>

    <hr>

    <a href="{{ route('admin.dashboard') }}">
        <i class="bi bi-grid-fill"></i>
        Dashboard
    </a>

    @php
        $verifikasiOpen = request()->routeIs('admin.verifikasi.*');
        $penggunaOpen = request()->routeIs('admin.pengguna.*');
        $perguruanTinggiOpen = request()->routeIs('admin.perguruan-tinggi.*');
        $magangOpen = request()->routeIs('admin.magang.*');
        $komunikasiOpen = request()->routeIs('admin.komunikasi.*');
        $laporanMonitoringOpen = request()->routeIs('admin.laporan-monitoring.*');
        $pengaturanOpen = request()->routeIs('admin.pengaturan.*');
    @endphp

    <div class="sidebar-menu-group">
        <div class="sidebar-parent-menu {{ $verifikasiOpen ? 'active' : '' }}">
            <a href="{{ route('admin.verifikasi.index') }}" class="sidebar-parent-link">
                <i class="bi bi-check-circle-fill"></i>
                Verifikasi
            </a>

            <button class="sidebar-toggle"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#verifikasiSubmenu"
                    aria-expanded="{{ $verifikasiOpen ? 'true' : 'false' }}"
                    aria-controls="verifikasiSubmenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>

        <div class="collapse {{ $verifikasiOpen ? 'show' : '' }}" id="verifikasiSubmenu">
            <div class="sidebar-submenu">
                <a href="{{ route('admin.verifikasi.akun') }}"
                   class="{{ request()->routeIs('admin.verifikasi.akun') ? 'active' : '' }}">
                    Verifikasi Akun
                </a>

                <a href="{{ route('admin.verifikasi.riwayat') }}"
                   class="{{ request()->routeIs('admin.verifikasi.riwayat') ? 'active' : '' }}">
                    Riwayat Verifikasi
                </a>
            </div>
        </div>
    </div>

    <div class="sidebar-menu-group">
        <div class="sidebar-parent-menu {{ $penggunaOpen ? 'active' : '' }}">
            <a href="{{ route('admin.pengguna.index') }}" class="sidebar-parent-link">
                <i class="bi bi-people-fill"></i>
                Manajemen Pengguna
            </a>

            <button class="sidebar-toggle"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#penggunaSubmenu"
                    aria-expanded="{{ $penggunaOpen ? 'true' : 'false' }}"
                    aria-controls="penggunaSubmenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>

        <div class="collapse {{ $penggunaOpen ? 'show' : '' }}" id="penggunaSubmenu">
            <div class="sidebar-submenu">
                <a href="{{ route('admin.pengguna.peserta') }}"
                   class="{{ request()->routeIs('admin.pengguna.peserta') ? 'active' : '' }}">
                    Peserta Magang
                </a>

                <a href="{{ route('admin.pengguna.mentor') }}"
                   class="{{ request()->routeIs('admin.pengguna.mentor') ? 'active' : '' }}">
                    Mentor
                </a>

                <a href="{{ route('admin.pengguna.pembimbing') }}"
                   class="{{ request()->routeIs('admin.pengguna.pembimbing') ? 'active' : '' }}">
                    Pembimbing Akademik
                </a>
            </div>
        </div>
    </div>

    <div class="sidebar-menu-group">
        <div class="sidebar-parent-menu {{ $perguruanTinggiOpen ? 'active' : '' }}">
            <a href="{{ route('admin.perguruan-tinggi.index') }}" class="sidebar-parent-link">
                <i class="bi bi-building"></i>
                Manajemen Perguruan Tinggi
            </a>

            <button class="sidebar-toggle"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#perguruanTinggiSubmenu"
                    aria-expanded="{{ $perguruanTinggiOpen ? 'true' : 'false' }}"
                    aria-controls="perguruanTinggiSubmenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>

        <div class="collapse {{ $perguruanTinggiOpen ? 'show' : '' }}" id="perguruanTinggiSubmenu">
            <div class="sidebar-submenu">
                <a href="{{ route('admin.perguruan-tinggi.data') }}"
                   class="{{ request()->routeIs('admin.perguruan-tinggi.data') ? 'active' : '' }}">
                    Data Perguruan Tinggi
                </a>

                <a href="{{ route('admin.perguruan-tinggi.kerjasama') }}"
                   class="{{ request()->routeIs('admin.perguruan-tinggi.kerjasama') ? 'active' : '' }}">
                    Data Kerja Sama
                </a>
            </div>
        </div>
    </div>

    <div class="sidebar-menu-group">
        <div class="sidebar-parent-menu {{ $magangOpen ? 'active' : '' }}">
            <a href="{{ route('admin.magang.index') }}" class="sidebar-parent-link">
                <i class="bi bi-briefcase-fill"></i>
                Manajemen Magang
            </a>

            <button class="sidebar-toggle"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#magangSubmenu"
                    aria-expanded="{{ $magangOpen ? 'true' : 'false' }}"
                    aria-controls="magangSubmenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>

        <div class="collapse {{ $magangOpen ? 'show' : '' }}" id="magangSubmenu">
            <div class="sidebar-submenu">
                <a href="{{ route('admin.magang.dokumen') }}"
                   class="{{ request()->routeIs('admin.magang.dokumen') ? 'active' : '' }}">
                    Dokumen Peserta
                </a>

                <a href="{{ route('admin.magang.absensi') }}"
                   class="{{ request()->routeIs('admin.magang.absensi') ? 'active' : '' }}">
                    Absensi
                </a>

                <a href="{{ route('admin.magang.kegiatan') }}"
                   class="{{ request()->routeIs('admin.magang.kegiatan') ? 'active' : '' }}">
                    Kegiatan Magang
                </a>

                <a href="{{ route('admin.magang.laporan') }}"
                   class="{{ request()->routeIs('admin.magang.laporan') ? 'active' : '' }}">
                    Laporan Berkala
                </a>

                <a href="{{ route('admin.magang.laporan-akhir') }}"
                   class="{{ request()->routeIs('admin.magang.laporan-akhir') ? 'active' : '' }}">
                    Laporan Akhir
                </a>

                <a href="{{ route('admin.magang.periode') }}"
                   class="{{ request()->routeIs('admin.magang.periode') ? 'active' : '' }}">
                    Periode Magang
                </a>

                <a href="{{ route('admin.magang.penempatan') }}"
                   class="{{ request()->routeIs('admin.magang.penempatan') ? 'active' : '' }}">
                    Penempatan
                </a>

                <a href="{{ route('admin.magang.penilaian') }}"
                   class="{{ request()->routeIs('admin.magang.penilaian') ? 'active' : '' }}">
                    Penilaian
                </a>
            </div>
        </div>
    </div>

    <div class="sidebar-menu-group">
        <div class="sidebar-parent-menu {{ $laporanMonitoringOpen ? 'active' : '' }}">
            <a href="{{ route('admin.laporan-monitoring.index') }}" class="sidebar-parent-link">
                <i class="bi bi-bar-chart-fill"></i>
                Monitoring
            </a>

            <button class="sidebar-toggle"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#laporanMonitoringSubmenu"
                    aria-expanded="{{ $laporanMonitoringOpen ? 'true' : 'false' }}"
                    aria-controls="laporanMonitoringSubmenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>

        <div class="collapse {{ $laporanMonitoringOpen ? 'show' : '' }}" id="laporanMonitoringSubmenu">
            <div class="sidebar-submenu">
                <a href="{{ route('admin.laporan-monitoring.rekap-absensi') }}"
                   class="{{ request()->routeIs('admin.laporan-monitoring.rekap-absensi') ? 'active' : '' }}">
                    Rekap Absensi
                </a>

                <a href="{{ route('admin.laporan-monitoring.rekap-kegiatan') }}"
                   class="{{ request()->routeIs('admin.laporan-monitoring.rekap-kegiatan') ? 'active' : '' }}">
                    Rekap Kegiatan
                </a>

                <a href="{{ route('admin.laporan-monitoring.statistik-pengguna') }}"
                   class="{{ request()->routeIs('admin.laporan-monitoring.statistik-pengguna') ? 'active' : '' }}">
                    Statistik Pengguna
                </a>

                <a href="{{ route('admin.laporan-monitoring.statistik-perguruan-tinggi') }}"
                   class="{{ request()->routeIs('admin.laporan-monitoring.statistik-perguruan-tinggi') ? 'active' : '' }}">
                    Statistik Perguruan Tinggi
                </a>
            </div>
        </div>
    </div>

    <div class="sidebar-menu-group">
        <div class="sidebar-parent-menu {{ $komunikasiOpen ? 'active' : '' }}">
            <a href="{{ route('admin.komunikasi.index') }}" class="sidebar-parent-link">
                <i class="bi bi-chat-dots-fill"></i>
                Komunikasi
            </a>

            <button class="sidebar-toggle"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#komunikasiSubmenu"
                    aria-expanded="{{ $komunikasiOpen ? 'true' : 'false' }}"
                    aria-controls="komunikasiSubmenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>

        <div class="collapse {{ $komunikasiOpen ? 'show' : '' }}" id="komunikasiSubmenu">
            <div class="sidebar-submenu">
                <a href="{{ route('admin.komunikasi.pesan') }}"
                   class="{{ request()->routeIs('admin.komunikasi.pesan') ? 'active' : '' }}">
                    Pesan
                </a>

                <a href="{{ route('admin.komunikasi.pengumuman') }}"
                   class="{{ request()->routeIs('admin.komunikasi.pengumuman') ? 'active' : '' }}">
                    Pengumuman
                </a>

                <a href="{{ route('admin.komunikasi.notifikasi') }}"
                   class="{{ request()->routeIs('admin.komunikasi.notifikasi') ? 'active' : '' }}">
                    Notifikasi
                </a>
            </div>
        </div>
    </div>

    <div class="sidebar-menu-group">
        <div class="sidebar-parent-menu {{ $pengaturanOpen ? 'active' : '' }}">
            <a href="{{ route('admin.pengaturan.index') }}" class="sidebar-parent-link">
                <i class="bi bi-gear-fill"></i>
                Pengaturan Akun
            </a>

            <button class="sidebar-toggle"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#pengaturanSubmenu"
                    aria-expanded="{{ $pengaturanOpen ? 'true' : 'false' }}"
                    aria-controls="pengaturanSubmenu">
                <i class="bi bi-chevron-down"></i>
            </button>
        </div>

        <div class="collapse {{ $pengaturanOpen ? 'show' : '' }}" id="pengaturanSubmenu">
            <div class="sidebar-submenu">
                <a href="{{ route('admin.pengaturan.profil') }}"
                   class="{{ request()->routeIs('admin.pengaturan.profil') ? 'active' : '' }}">
                    Profil Akun
                </a>

                <a href="{{ route('admin.pengaturan.password') }}"
                   class="{{ request()->routeIs('admin.pengaturan.password') ? 'active' : '' }}">
                    Ubah Password
                </a>

                <a href="{{ route('admin.pengaturan.hak-akses') }}"
                   class="{{ request()->routeIs('admin.pengaturan.hak-akses') ? 'active' : '' }}">
                    Hak Akses
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf

        <button type="submit" class="logout-button">
            <i class="bi bi-box-arrow-right"></i>
            Log Out
        </button>
    </form>

</div>
