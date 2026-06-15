@php
    $pembimbingUser = auth()->user();
    $pembimbingAvatar = $pembimbingUser?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($pembimbingUser?->name ?? 'Pembimbing Akademik') . '&background=2a8fbd&color=fff';
    $pembimbingNotificationCount = $pembimbingUser
        ? \App\Models\Notification::query()
            ->where('user_id', $pembimbingUser->id)
            ->where('dibaca', false)
            ->count()
        : 0;
    $pembimbingUnreadMessages = $pembimbingUser?->pembimbing
        ? \App\Models\Message::query()
            ->whereNull('dibaca_pada')
            ->where('sender_id', '!=', $pembimbingUser->id)
            ->whereHas('conversation', function ($query) use ($pembimbingUser) {
                $query->where('pembimbing_id', $pembimbingUser->pembimbing->id);
            })
            ->count()
        : 0;
@endphp
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">@yield('page-title', 'Dashboard Pembimbing Akademik')</h2>
        <p class="text-muted mb-0">Sistem Informasi dan Kearsipan Portal Magang</p>
    </div>

    <div class="d-flex align-items-center gap-3">
        <a class="btn btn-light position-relative" href="{{ route('pembimbing.komunikasi.notifikasi') }}" title="Notifikasi" aria-label="Notifikasi pembimbing">
            <i class="bi bi-bell fs-5"></i>
            @if ($pembimbingNotificationCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $pembimbingNotificationCount > 99 ? '99+' : $pembimbingNotificationCount }}
                </span>
            @endif
        </a>
        <a class="btn btn-light position-relative" href="{{ route('pembimbing.komunikasi.pesan') }}" title="Pesan" aria-label="Pesan pembimbing">
            <i class="bi bi-chat-dots fs-5"></i>
            @if ($pembimbingUnreadMessages > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $pembimbingUnreadMessages > 99 ? '99+' : $pembimbingUnreadMessages }}
                </span>
            @endif
        </a>
        <div class="d-flex align-items-center">
            <img src="{{ $pembimbingAvatar }}" class="rounded-circle me-2 js-pembimbing-avatar" width="42" height="42" alt="Avatar">
            <div>
                <strong>{{ auth()->user()->name ?? 'Pembimbing Akademik' }}</strong>
                <div class="small text-muted">Pembimbing Akademik</div>
            </div>
        </div>
    </div>
</div>
