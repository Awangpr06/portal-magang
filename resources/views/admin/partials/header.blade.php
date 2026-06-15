@php
    $adminUser = auth()->user();
    $adminHeaderAvatar = $adminUser?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($adminUser?->name ?? 'Admin');
    $adminNotificationCount = $adminUser
        ? \App\Models\Notification::query()
            ->where('user_id', $adminUser->id)
            ->where('dibaca', false)
            ->count()
        : 0;
    $adminUnreadMessages = $adminUser
        ? \App\Models\Message::query()
            ->whereNull('dibaca_pada')
            ->where('sender_id', '!=', $adminUser->id)
            ->whereHas('conversation', function ($query) use ($adminUser) {
                $query->where('admin_id', $adminUser->id);
            })
            ->count()
        : 0;
@endphp
<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="fw-bold">
            Dashboard Super Admin
        </h2>

        <p class="text-muted">
            Sistem Informasi dan Kearsipan Portal Magang
        </p>
    </div>

    <div class="d-flex align-items-center gap-3">
        <a class="position-relative text-dark text-decoration-none" href="{{ route('admin.komunikasi.notifikasi') }}" title="Notifikasi" aria-label="Notifikasi admin">
            <i class="bi bi-bell fs-4"></i>
            @if ($adminNotificationCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $adminNotificationCount > 99 ? '99+' : $adminNotificationCount }}
                </span>
            @endif
        </a>
        <a class="position-relative text-dark text-decoration-none" href="{{ route('admin.komunikasi.pesan') }}" title="Pesan" aria-label="Pesan admin">
            <i class="bi bi-chat-dots fs-4"></i>
            @if ($adminUnreadMessages > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $adminUnreadMessages > 99 ? '99+' : $adminUnreadMessages }}
                </span>
            @endif
        </a>

        <div class="d-flex align-items-center">
            <img src="{{ $adminHeaderAvatar }}"
                 alt="Avatar Admin"
                 class="rounded-circle me-2 js-admin-avatar"
                 width="42"
                 height="42"
                 style="width:42px;height:42px;object-fit:cover;">

            <div>
                <strong>Super Admin</strong>
            </div>
        </div>

    </div>

</div>
