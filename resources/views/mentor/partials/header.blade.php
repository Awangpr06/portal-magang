@php
    $mentorUser = auth()->user();
    $mentorAvatar = $mentorUser?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($mentorUser?->name ?? 'Mentor Lapangan') . '&background=2a8fbd&color=ffffff';
    $mentor = $mentorUser?->mentor;
    $mentorNotificationCount = $mentorUser
        ? \App\Models\Notification::query()
            ->where('user_id', $mentorUser->id)
            ->where('dibaca', false)
            ->count()
        : 0;
    $mentorUnreadMessages = $mentor
        ? \App\Models\Message::query()
            ->whereNull('dibaca_pada')
            ->where('sender_id', '!=', $mentorUser->id)
            ->whereHas('conversation', function ($query) use ($mentor) {
                $query->where('mentor_id', $mentor->id);
            })
            ->count()
        : 0;
@endphp
<div class="top-header d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-outline-warning" type="button" id="sidebarToggle" title="Menu">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div>
            <h2 class="fw-bold mb-1">@yield('page-title', 'Dashboard Mentor')</h2>
            <p class="text-muted mb-0">Sistem Informasi dan Kearsipan Portal Magang</p>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <a class="btn btn-light position-relative" href="{{ route('mentor.notifikasi') }}" title="Notifikasi" aria-label="Notifikasi mentor">
            <i class="bi bi-bell fs-5"></i>
            @if ($mentorNotificationCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $mentorNotificationCount > 99 ? '99+' : $mentorNotificationCount }}
                </span>
            @endif
        </a>
        <a class="btn btn-light position-relative" href="{{ route('mentor.komunikasi.pesan') }}" title="Pesan" aria-label="Pesan mentor">
            <i class="bi bi-chat-dots fs-5"></i>
            @if ($mentorUnreadMessages > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $mentorUnreadMessages > 99 ? '99+' : $mentorUnreadMessages }}
                </span>
            @endif
        </a>
        <div class="dropdown">
            <button class="btn btn-light d-flex align-items-center gap-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ $mentorAvatar }}" class="rounded-circle" width="42" height="42" alt="Avatar">
                <span class="text-start d-none d-sm-block">
                    <strong class="d-block">{{ auth()->user()->name ?? 'Mentor Lapangan' }}</strong>
                    <small class="text-muted">Mentor Lapangan</small>
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('mentor.pengaturan') }}"><i class="bi bi-person me-2"></i>Profil Akun</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>
