@extends('mentor.layout.mentor')

@section('title', 'Komunikasi')
@section('page-title', 'Komunikasi')

@push('styles')
<style>
    .communication-page .communication-banner { background:linear-gradient(135deg,#2a8fbd,#1f739a); color:#fff; border-radius:8px; }
    .communication-page .stat-card,
    .communication-page .filter-card,
    .communication-page .table-card,
    .communication-page .panel-card { border:0; border-radius:8px; }
    .communication-page .stat-card { cursor:pointer; transition:.2s ease; }
    .communication-page .stat-card:hover,
    .communication-page .stat-card.active { transform:translateY(-3px); box-shadow:0 12px 28px rgba(42,143,189,.18); }
    .communication-page .stat-icon { width:42px; height:42px; display:inline-flex; align-items:center; justify-content:center; border-radius:8px; color:#fff; }
    .communication-page .chat-list { max-height:360px; overflow:auto; }
    .communication-page .message-box { min-height:280px; max-height:360px; overflow:auto; background:#f8f9fa; border-radius:8px; border:1px solid #d7eaf2; }
    .communication-page .bubble { max-width:78%; border-radius:8px; padding:10px 12px; display:inline-block; }
    .communication-page .info-row { border:1px solid #d7eaf2; border-radius:8px; padding:12px; background:#fff; margin-bottom:10px; }
    .communication-page .action-group { display:flex; flex-wrap:wrap; gap:6px; min-width:420px; }
    .communication-page .empty-state { min-height:210px; display:flex; align-items:center; justify-content:center; text-align:center; color:#6c757d; }
    .communication-page .pagination .page-link { color:#2a8fbd; }
    .communication-page .pagination .active .page-link { background:#2a8fbd; border-color:#2a8fbd; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid communication-page">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('mentor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Komunikasi</li>
        </ol>
    </nav>

    <section class="communication-banner p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Komunikasi Mentor</h3>
                <p class="mb-0">Kelola pertukaran informasi dengan peserta magang, pembimbing akademik, dan pengelola sistem secara terintegrasi.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a class="btn btn-light me-2" href="{{ route('mentor.notifikasi') }}"><i class="bi bi-bell"></i> Notifikasi</a>
                <button class="btn btn-dark" type="button" id="composeTopButton"><i class="bi bi-send"></i> Kirim Pesan</button>
            </div>
        </div>
    </section>

    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-chat-left-text-fill"></i>
        <div>4 pesan belum dibaca, 6 pesan masuk hari ini, dan 2 percakapan prioritas membutuhkan respons mentor.</div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl">
            <div class="card stat-card active" data-status-card="semua">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Total Percakapan</p><h3 class="mb-0" id="statTotal">0</h3></div>
                    <span class="stat-icon bg-primary"><i class="bi bi-chat-dots"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-status-card="belum dibaca">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Belum Dibaca</p><h3 class="mb-0" id="statUnread">0</h3></div>
                    <span class="stat-icon bg-warning"><i class="bi bi-envelope-exclamation"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Pesan Hari Ini</p><h3 class="mb-0" id="statToday">0</h3></div>
                    <span class="stat-icon bg-success"><i class="bi bi-calendar-check"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-recipient-card="Peserta">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Peserta Aktif</p><h3 class="mb-0" id="statStudents">0</h3></div>
                    <span class="stat-icon bg-info"><i class="bi bi-person-lines-fill"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card" data-recipient-card="Pembimbing Akademik">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Pembimbing Aktif</p><h3 class="mb-0" id="statLecturers">0</h3></div>
                    <span class="stat-icon bg-dark"><i class="bi bi-mortarboard"></i></span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl">
            <div class="card stat-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><p class="text-muted mb-1">Respons</p><h3 class="mb-0" id="statResponse">0%</h3></div>
                    <span class="stat-icon bg-danger"><i class="bi bi-speedometer2"></i></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label" for="searchInput">Pesan / Pengguna</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input class="form-control" id="searchInput" type="search" placeholder="Cari pesan, subjek, pengguna">
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="typeFilter">Jenis Komunikasi</label>
                    <select class="form-select" id="typeFilter">
                        <option value="semua">Semua Jenis</option>
                        <option value="Pesan Mentor">Pesan Mentor</option>
                        <option value="Pesan Pembimbing">Pesan Pembimbing</option>
                        <option value="Pesan Admin">Pesan Admin</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="recipientFilter">Penerima</label>
                    <select class="form-select" id="recipientFilter">
                        <option value="semua">Semua Penerima</option>
                        <option value="Peserta">Peserta</option>
                        <option value="Pembimbing Akademik">Pembimbing Akademik</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="statusFilter">Status Pesan</label>
                    <select class="form-select" id="statusFilter">
                        <option value="semua">Semua Status</option>
                        <option value="belum dibaca">Belum Dibaca</option>
                        <option value="dibaca">Dibaca</option>
                        <option value="terkirim">Terkirim</option>
                        <option value="arsip">Arsip</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label" for="dateFilter">Tanggal</label>
                    <input class="form-control" id="dateFilter" type="date">
                </div>
                <div class="col-lg-1 col-md-6 d-grid">
                    <button class="btn btn-warning" type="button" id="applyFilter"><i class="bi bi-funnel"></i></button>
                </div>
                <div class="col-lg-12 d-flex justify-content-end">
                    <button class="btn btn-outline-secondary" type="button" id="resetFilter"><i class="bi bi-arrow-counterclockwise"></i> Reset Filter</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4">
            <div class="card panel-card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Percakapan</h5>
                    <span class="badge bg-warning text-dark" id="conversationCount">0</span>
                </div>
                <div class="card-body chat-list" id="conversationList"></div>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="card panel-card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center gap-2">
                    <div>
                        <h5 class="mb-1" id="chatTitle">Percakapan Aktif</h5>
                        <small class="text-muted" id="chatSubtitle">Pilih percakapan untuk membaca pesan.</small>
                    </div>
                    <button class="btn btn-sm btn-outline-warning" type="button" id="markReadButton"><i class="bi bi-check2-all"></i></button>
                </div>
                <div class="card-body">
                    <div class="message-box p-3 mb-3" id="messageBox"></div>
                    <div class="input-group">
                        <input class="form-control" id="replyInput" type="text" placeholder="Tulis balasan">
                        <button class="btn btn-warning" type="button" id="replyButton"><i class="bi bi-send"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card panel-card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">Informasi Pengguna</h5>
                    <div id="userInfoPanel"></div>
                    <hr>
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning text-start" type="button" id="composePanelButton"><i class="bi bi-send me-2"></i> Kirim Pesan</button>
                        <button class="btn btn-outline-warning text-start" type="button" id="archiveButton"><i class="bi bi-archive me-2"></i> Arsipkan</button>
                        <button class="btn btn-outline-warning text-start" type="button" id="exportPanelButton"><i class="bi bi-download me-2"></i> Export Data</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="composeModal" tabindex="-1" aria-labelledby="composeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="composeModalLabel">Kirim Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="recipientInput">Penerima</label>
                    <select class="form-select" id="recipientInput">
                        <option>Ayu Lestari</option>
                        <option>Dr. Budi Santoso</option>
                        <option>Admin Portal</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="subjectInput">Subjek</label>
                    <input class="form-control" id="subjectInput" type="text" placeholder="Subjek pesan">
                </div>
                <div>
                    <label class="form-label" for="messageInput">Isi Pesan</label>
                    <textarea class="form-control" id="messageInput" rows="4" placeholder="Tulis pesan"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="sendComposeButton">Kirim</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Komunikasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body" id="confirmText">Lanjutkan aksi komunikasi?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning" id="confirmActionButton">Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="communicationToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">Komunikasi diperbarui.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const communicationData = @json($communicationData ?? []);
    const csrfToken = @json(csrf_token());
    const sendUrl = @json(route('komunikasi.send'));
    const replyUrlBase = @json(url('/komunikasi'));
    const readUrlBase = @json(url('/komunikasi'));
    const archiveUrlBase = @json(url('/mentor/komunikasi'));
    const exportUrl = @json(route('mentor.komunikasi.export'));
    const conversations = (Array.isArray(communicationData.conversations) ? communicationData.conversations : []).map((conversation) => {
        const thread = Array.isArray(conversation.messages) ? conversation.messages : [];
        const latestMessage = thread[thread.length - 1] || null;
        const latestIso = conversation.last_message_at_iso || latestMessage?.created_at_iso || null;
        const latestDate = latestIso ? new Date(latestIso) : null;

        return {
            id: conversation.id,
            user: conversation.contact_name || 'Kontak',
            role: conversation.contact_role || 'Pesan',
            type: conversation.type || 'Pesan Langsung',
            subject: conversation.subject || 'Percakapan',
            time: latestDate && !Number.isNaN(latestDate.getTime())
                ? latestDate.toISOString().slice(0, 10)
                : '-',
            status: Number(conversation.unread_count || 0) > 0 ? 'belum dibaca' : 'dibaca',
            response: conversation.last_message_label || '-',
            priority: Number(conversation.unread_count || 0) > 0 ? 'Tinggi' : 'Rendah',
            thread,
            recipient_type: conversation.recipient_type,
            recipient_id: conversation.recipient_id,
        };
    });

    let filtered = [...conversations];
    let selectedId = conversations[0]?.id || null;
    let pendingAction = null;
    const contacts = Array.isArray(communicationData.contacts) ? communicationData.contacts : [];

    const composeModal = new bootstrap.Modal(document.getElementById('composeModal'));
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    const toast = new bootstrap.Toast(document.getElementById('communicationToast'));

    const titleCase = text => text.replace(/\b\w/g, char => char.toUpperCase());
    const statusBadge = status => {
        const map = { 'belum dibaca':'warning text-dark', dibaca:'success', terkirim:'info', arsip:'secondary' };
        return `<span class="badge bg-${map[status] || 'secondary'}">${titleCase(status)}</span>`;
    };
    const priorityBadge = priority => {
        const map = { Tinggi:'danger', Sedang:'warning text-dark', Rendah:'secondary' };
        return `<span class="badge bg-${map[priority] || 'secondary'}">${priority}</span>`;
    };
    const escapeHtml = text => String(text ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
    const showToast = message => {
        document.getElementById('toastMessage').textContent = message;
        toast.show();
    };

    const postForm = async (url, formData) => {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        });

        if (!response.ok) {
            let message = 'Permintaan gagal diproses.';
            try {
                const payload = await response.json();
                message = payload.message || message;
            } catch (error) {
                // ignore parse error
            }
            throw new Error(message);
        }

        return response.json();
    };

    const populateRecipients = () => {
        const recipientInput = document.getElementById('recipientInput');
        if (!recipientInput) return;

        const options = contacts.map((contact) => `
            <option value="${contact.recipient_type}:${contact.recipient_id}">
                ${escapeHtml(contact.name)} - ${escapeHtml(contact.role)}
            </option>
        `).join('');

        recipientInput.innerHTML = options || '<option value="">Tidak ada kontak tersedia</option>';
    };

    const selectedConversation = () => conversations.find(item => item.id === selectedId) || conversations[0];

    const updateStats = () => {
        document.getElementById('statTotal').textContent = conversations.length;
        document.getElementById('statUnread').textContent = conversations.filter(item => item.status === 'belum dibaca').length;
        document.getElementById('statToday').textContent = conversations.filter(item => item.time === new Date().toISOString().slice(0, 10)).length;
        document.getElementById('statStudents').textContent = conversations.filter(item => item.role === 'Peserta').length;
        document.getElementById('statLecturers').textContent = conversations.filter(item => item.role === 'Pembimbing Akademik').length;
        document.getElementById('statResponse').textContent = '86%';
        document.getElementById('conversationCount').textContent = filtered.length;
    };

    const renderConversations = () => {
        document.getElementById('conversationList').innerHTML = filtered.map(item => `
            <button class="list-group-item list-group-item-action ${item.id === selectedId ? 'active' : ''}" type="button" data-conversation="${item.id}">
                <div class="d-flex justify-content-between gap-2">
                    <strong>${item.user}</strong>
                    ${priorityBadge(item.priority)}
                </div>
                <small class="d-block">${item.role} - ${item.subject}</small>
                <small class="d-block">${statusBadge(item.status)} <span class="ms-1">${item.response}</span></small>
            </button>
        `).join('') || '<p class="text-muted mb-0">Tidak ada percakapan.</p>';
    };

    const renderChat = () => {
        const item = selectedConversation();
        if (!item) {
            document.getElementById('chatTitle').textContent = 'Tidak ada percakapan';
            document.getElementById('chatSubtitle').textContent = 'Belum ada data percakapan dari database.';
            document.getElementById('messageBox').innerHTML = '<div class="empty-state"><div><i class="bi bi-chat-square-text fs-1 d-block mb-2"></i><p class="mb-0">Belum ada pesan.</p></div></div>';
            document.getElementById('userInfoPanel').innerHTML = '<div class="info-row"><div class="fw-bold">-</div><small class="text-muted">-</small></div>';
            return;
        }
        document.getElementById('chatTitle').textContent = item.user;
        document.getElementById('chatSubtitle').textContent = `${item.role} - ${item.subject}`;
        document.getElementById('messageBox').innerHTML = (item.thread || []).map((message) => `
            <div class="mb-2 ${message.direction === 'out' ? 'text-end' : ''}">
                <span class="bubble ${message.direction === 'out' ? 'bg-warning text-dark' : 'bg-white border'}">${escapeHtml(message.text || '')}</span>
            </div>
        `).join('');
        document.getElementById('userInfoPanel').innerHTML = `
            <div class="info-row">
                <div class="fw-bold">${item.user}</div>
                <small class="text-muted">${item.role}</small>
                <div class="mt-2">${statusBadge(item.status)}</div>
            </div>
            <div class="info-row">
                <small class="text-muted">Jenis Komunikasi</small>
                <div class="fw-semibold">${item.type}</div>
                <small class="text-muted">Prioritas ${item.priority}</small>
            </div>
        `;
        renderConversations();
    };

    const applyFilters = () => {
        const keyword = document.getElementById('searchInput').value.toLowerCase();
        const type = document.getElementById('typeFilter').value;
        const recipient = document.getElementById('recipientFilter').value;
        const status = document.getElementById('statusFilter').value;
        const date = document.getElementById('dateFilter').value;
        filtered = conversations.filter(item => {
            const text = [item.user, item.role, item.type, item.subject, (item.thread || []).map((message) => message.text || '').join(' ')].join(' ').toLowerCase();
            return text.includes(keyword)
                && (type === 'semua' || item.type === type)
                && (recipient === 'semua' || item.role === recipient)
                && (status === 'semua' || item.status === status)
                && (!date || item.time === date);
        });
        updateStats();
        renderConversations();
    };

    document.getElementById('conversationList').addEventListener('click', event => {
        const button = event.target.closest('button[data-conversation]');
        if (!button) return;
        selectedId = Number(button.dataset.conversation);
        renderChat();
    });

    document.getElementById('replyButton').addEventListener('click', () => {
        const input = document.getElementById('replyInput');
        const item = selectedConversation();
        if (!item || !input.value.trim()) return;

        const formData = new FormData();
        formData.append('message', input.value.trim());
        formData.append('subject', item.subject || 'Percakapan');

        postForm(`${replyUrlBase}/${item.id}/balas`, formData)
            .then(() => {
                input.value = '';
                showToast('Pesan berhasil dikirim.');
                window.location.href = `${window.location.pathname}?conversation=${item.id}`;
            })
            .catch((error) => showToast(error.message, false));
    });

    document.getElementById('sendComposeButton').addEventListener('click', () => {
        const recipientInput = document.getElementById('recipientInput');
        const recipient = recipientInput.value;
        const subject = document.getElementById('subjectInput').value.trim() || 'Pesan Baru';
        const message = document.getElementById('messageInput').value.trim();
        const [recipientType, recipientId] = recipient.split(':');
        const recipientLabel = recipientInput.selectedOptions[0]?.textContent?.trim() || '-';

        if (!recipientType || !recipientId || !message) {
            showToast('Penerima dan isi pesan wajib diisi.', false);
            return;
        }

        pendingAction = async () => {
            const formData = new FormData();
            formData.append('recipient_type', recipientType);
            formData.append('recipient_id', recipientId);
            formData.append('subject', subject);
            formData.append('message', message);
            await postForm(sendUrl, formData);
            showToast(`Pesan untuk ${recipientLabel} berhasil dikirim.`);
            window.location.reload();
        };
        document.getElementById('confirmText').textContent = `Kirim pesan "${subject}" kepada ${recipientLabel}? ${message ? 'Isi pesan sudah terisi.' : ''}`;
        confirmModal.show();
    });

    document.getElementById('confirmActionButton').addEventListener('click', async () => {
        if (pendingAction) await pendingAction();
        pendingAction = null;
        confirmModal.hide();
        composeModal.hide();
        applyFilters();
        renderChat();
    });

    document.getElementById('composeTopButton').addEventListener('click', () => composeModal.show());
    document.getElementById('composePanelButton').addEventListener('click', () => composeModal.show());
    document.getElementById('markReadButton').addEventListener('click', async () => {
        const item = selectedConversation();
        if (!item) {
            showToast('Pilih percakapan terlebih dahulu.', false);
            return;
        }

        try {
            await fetch(`${readUrlBase}/${item.id}/baca`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });
            showToast('Percakapan aktif ditandai dibaca.');
            window.location.reload();
        } catch (error) {
            showToast('Gagal menandai percakapan sebagai dibaca.', false);
        }
    });
    document.getElementById('archiveButton').addEventListener('click', () => {
        const item = selectedConversation();
        if (!item) {
            showToast('Pilih percakapan terlebih dahulu.', false);
            return;
        }

        pendingAction = async () => {
            const formData = new FormData();
            formData.append('_method', 'PATCH');
            formData.append('status', item.status === 'arsip' ? 'aktif' : 'arsip');

            await postForm(`${archiveUrlBase}/${item.id}/status`, formData);
            showToast(item.status === 'arsip' ? 'Percakapan berhasil diaktifkan kembali.' : 'Percakapan aktif diarsipkan.');
            window.location.reload();
        };
        document.getElementById('confirmText').textContent = item.status === 'arsip'
            ? `Aktifkan kembali percakapan dengan ${item.user}?`
            : `Arsipkan percakapan dengan ${item.user}?`;
        confirmModal.show();
    });
    document.getElementById('exportPanelButton').addEventListener('click', () => {
        window.location.href = exportUrl;
    });

    document.getElementById('applyFilter').addEventListener('click', applyFilters);
    document.getElementById('searchInput').addEventListener('input', applyFilters);
    document.querySelectorAll('#typeFilter,#recipientFilter,#statusFilter,#dateFilter').forEach(input => input.addEventListener('change', applyFilters));
    document.getElementById('resetFilter').addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('typeFilter').value = 'semua';
        document.getElementById('recipientFilter').value = 'semua';
        document.getElementById('statusFilter').value = 'semua';
        document.getElementById('dateFilter').value = '';
        applyFilters();
    });

    document.querySelectorAll('.stat-card[data-status-card]').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.stat-card').forEach(item => item.classList.remove('active'));
            card.classList.add('active');
            document.getElementById('statusFilter').value = card.dataset.statusCard;
            applyFilters();
        });
    });
    document.querySelectorAll('.stat-card[data-recipient-card]').forEach(card => {
        card.addEventListener('click', () => {
            document.getElementById('recipientFilter').value = card.dataset.recipientCard;
            applyFilters();
        });
    });

    populateRecipients();
    updateStats();
    applyFilters();
    renderChat();
});
</script>
@endpush
