@php
    $pesertaStats = data_get($pesertaContext ?? [], 'stats', []);
    $communicationStats = data_get($communicationData ?? [], 'stats', []);

    $notificationUnread = (int) data_get($pesertaStats, 'notification_unread', 0);
    $messageUnread = (int) data_get($communicationStats, 'unread', data_get($pesertaStats, 'message_unread', 0));

    $notificationHref = route('peserta.komunikasi.notifikasi');
    $messageHref = route('peserta.komunikasi.pesan');
@endphp

<a class="icon-button position-relative" href="{{ $notificationHref }}" title="Notifikasi" aria-label="Lihat notifikasi">
    <i class="bi bi-bell fs-5"></i>
    @if ($notificationUnread > 0)
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            {{ $notificationUnread > 99 ? '99+' : $notificationUnread }}
        </span>
    @endif
</a>

<a class="icon-button position-relative" href="{{ $messageHref }}" title="Pesan" aria-label="Lihat pesan">
    <i class="bi bi-chat-dots fs-5"></i>
    @if ($messageUnread > 0)
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
            {{ $messageUnread > 99 ? '99+' : $messageUnread }}
        </span>
    @endif
</a>
