<?php

namespace App\Services\Communication;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Mentor;
use App\Models\Notification;
use App\Models\Pembimbing;
use App\Models\Peserta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CommunicationService
{
    public function forUser(User $user, ?Conversation $selectedConversation = null): array
    {
        $user->loadMissing(['peserta.internship.mentor.user', 'peserta.internship.pembimbing.user', 'mentor.user', 'pembimbing.user']);

        $payload = match ($user->role) {
            'peserta' => $this->buildPesertaPayload($user, $selectedConversation),
            'mentor' => $this->buildMentorPayload($user, $selectedConversation),
            'pembimbing' => $this->buildPembimbingPayload($user, $selectedConversation),
            default => $this->buildAdminPayload($user, $selectedConversation),
        };

        return array_merge([
            'current_user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'avatar' => $user->avatar_url,
            ],
            'role_label' => $this->roleLabel($user->role),
            'selected_conversation_id' => $selectedConversation?->id,
        ], $payload);
    }

    public function send(User $user, array $validated): Conversation
    {
        return DB::transaction(function () use ($user, $validated) {
            $conversation = $this->resolveConversationForSend($user, $validated);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'pesan' => $validated['message'],
                'lampiran' => $validated['attachment'] ?? null,
            ]);

            $conversation->update([
                'topik' => $validated['subject'] ?: $conversation->topik,
                'status' => 'aktif',
                'last_message_at' => now(),
            ]);

            $this->sendNotification($conversation, $user, $validated['message']);

            return $conversation->load(['peserta.user', 'mentor.user', 'pembimbing.user', 'admin', 'messages.sender']);
        });
    }

    public function reply(User $user, Conversation $conversation, array $validated): Message
    {
        return DB::transaction(function () use ($user, $conversation, $validated) {
            $this->authorizeConversation($user, $conversation);

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'pesan' => $validated['message'],
                'lampiran' => $validated['attachment'] ?? null,
            ]);

            $conversation->update([
                'topik' => $validated['subject'] ?: $conversation->topik,
                'status' => 'aktif',
                'last_message_at' => now(),
            ]);

            $this->sendNotification($conversation, $user, $validated['message']);

            return $message;
        });
    }

    public function markRead(User $user, Conversation $conversation): void
    {
        $this->authorizeConversation($user, $conversation);

        if (! Schema::hasTable('messages')) {
            return;
        }

        Message::query()
            ->where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('dibaca_pada')
            ->update(['dibaca_pada' => now()]);
    }

    private function buildPesertaPayload(User $user, ?Conversation $selectedConversation): array
    {
        $peserta = $user->peserta;
        $conversations = $peserta
            ? Conversation::query()
                ->with(['mentor.user', 'pembimbing.user', 'admin', 'messages.sender'])
                ->where('peserta_id', $peserta->id)
                ->latest('last_message_at')
                ->latest('id')
                ->get()
            : collect();

        $contacts = collect();
        $internship = $peserta?->internship;

        if ($internship?->mentor?->user) {
            $contacts->push($this->contactFromMentor($internship->mentor, 'mentor'));
        }

        if ($internship?->pembimbing?->user) {
            $contacts->push($this->contactFromPembimbing($internship->pembimbing, 'pembimbing'));
        }

        $supportAdmin = User::query()
            ->whereIn('role', ['admin', 'super_admin'])
            ->orderByRaw("CASE WHEN role = 'super_admin' THEN 0 ELSE 1 END")
            ->orderBy('id')
            ->first();

        if ($supportAdmin) {
            $contacts->push($this->contactFromUser($supportAdmin, 'admin', $supportAdmin->name, 'Administrator'));
        }

        return $this->payloadFromConversations($user, $conversations, $contacts, $selectedConversation);
    }

    private function buildMentorPayload(User $user, ?Conversation $selectedConversation): array
    {
        $mentor = $user->mentor;
        $mentorId = $mentor?->id;

        $conversations = $mentor
            ? Conversation::query()
                ->with(['peserta.user', 'admin', 'messages.sender'])
                ->where('mentor_id', $mentorId)
                ->latest('last_message_at')
                ->latest('id')
                ->get()
            : collect();

        $contacts = collect();

        if ($mentor) {
            $internships = $mentor->internships()->with(['peserta.user'])->latest('created_at')->latest('id')->get();
            foreach ($internships as $internship) {
                if ($internship->peserta?->user) {
                    $contacts->push($this->contactFromPeserta($internship->peserta, 'peserta'));
                }
            }
        }

        $supportAdmin = User::query()
            ->whereIn('role', ['admin', 'super_admin'])
            ->orderByRaw("CASE WHEN role = 'super_admin' THEN 0 ELSE 1 END")
            ->orderBy('id')
            ->first();

        if ($supportAdmin) {
            $contacts->push($this->contactFromUser($supportAdmin, 'admin', $supportAdmin->name, 'Administrator'));
        }

        return $this->payloadFromConversations($user, $conversations, $contacts, $selectedConversation);
    }

    private function buildPembimbingPayload(User $user, ?Conversation $selectedConversation): array
    {
        $pembimbing = $user->pembimbing;
        $pembimbingId = $pembimbing?->id;

        $conversations = $pembimbing
            ? Conversation::query()
                ->with(['peserta.user', 'admin', 'messages.sender'])
                ->where('pembimbing_id', $pembimbingId)
                ->latest('last_message_at')
                ->latest('id')
                ->get()
            : collect();

        $contacts = collect();

        if ($pembimbing) {
            $internships = $pembimbing->internships()->with(['peserta.user'])->latest('created_at')->latest('id')->get();
            foreach ($internships as $internship) {
                if ($internship->peserta?->user) {
                    $contacts->push($this->contactFromPeserta($internship->peserta, 'peserta'));
                }
            }
        }

        $supportAdmin = User::query()
            ->whereIn('role', ['admin', 'super_admin'])
            ->orderByRaw("CASE WHEN role = 'super_admin' THEN 0 ELSE 1 END")
            ->orderBy('id')
            ->first();

        if ($supportAdmin) {
            $contacts->push($this->contactFromUser($supportAdmin, 'admin', $supportAdmin->name, 'Administrator'));
        }

        return $this->payloadFromConversations($user, $conversations, $contacts, $selectedConversation);
    }

    private function buildAdminPayload(User $user, ?Conversation $selectedConversation): array
    {
        $conversations = Conversation::query()
            ->with(['peserta.user', 'mentor.user', 'pembimbing.user', 'messages.sender'])
            ->where('admin_id', $user->id)
            ->latest('last_message_at')
            ->latest('id')
            ->get();

        $contacts = collect();

        $pesertas = Peserta::query()->with('user')->latest('id')->get();
        foreach ($pesertas as $peserta) {
            if ($peserta->user) {
                $contacts->push($this->contactFromPeserta($peserta, 'peserta'));
            }
        }

        $mentors = Mentor::query()->with('user')->latest('id')->get();
        foreach ($mentors as $mentor) {
            if ($mentor->user) {
                $contacts->push($this->contactFromMentor($mentor, 'mentor'));
            }
        }

        $pembimbings = Pembimbing::query()->with('user')->latest('id')->get();
        foreach ($pembimbings as $pembimbing) {
            if ($pembimbing->user) {
                $contacts->push($this->contactFromPembimbing($pembimbing, 'pembimbing'));
            }
        }

        return $this->payloadFromConversations($user, $conversations, $contacts->unique('key')->values(), $selectedConversation);
    }

    private function payloadFromConversations(User $user, EloquentCollection|Collection $conversations, Collection $contacts, ?Conversation $selectedConversation): array
    {
        $normalized = $conversations->map(function (Conversation $conversation) use ($user) {
            return $this->normalizeConversation($conversation, $user);
        })->values();

        $selected = $selectedConversation
            ? $normalized->firstWhere('id', $selectedConversation->id)
            : ($normalized->first() ?? null);

        if (! $selected && $normalized->isNotEmpty()) {
            $selected = $normalized->first();
        }

        return [
            'contacts' => $contacts->map(fn ($contact) => $this->normalizeContact($contact))->values(),
            'conversations' => $normalized,
            'selected_conversation' => $selected,
            'stats' => [
                'total' => $normalized->count(),
                'unread' => $normalized->sum('unread_count'),
                'active' => $normalized->where('status', 'aktif')->count(),
                'today' => $normalized->filter(fn (array $item) => Str::contains($item['last_message_label'] ?? '', 'Hari Ini'))->count(),
            ],
        ];
    }

    private function normalizeConversation(Conversation $conversation, User $currentUser): array
    {
        $thread = $conversation->messages->sortBy('created_at')->values();
        $latestMessage = $thread->last();
        $incomingCount = $thread->where('sender_id', '!=', $currentUser->id)->count();
        $sentCount = $thread->where('sender_id', $currentUser->id)->count();
        $unreadCount = $thread->where('sender_id', '!=', $currentUser->id)->whereNull('dibaca_pada')->count();
        $lastMessageAt = $conversation->last_message_at ?? $latestMessage?->created_at ?? $conversation->updated_at;

        $contact = $this->conversationContact($conversation, $currentUser);

        return [
            'id' => $conversation->id,
            'subject' => $conversation->topik ?: 'Percakapan',
            'status' => $conversation->status ?: 'aktif',
            'type' => $this->conversationType($conversation),
            'recipient_type' => $contact['recipient_type'],
            'recipient_id' => $contact['recipient_id'],
            'contact_name' => $contact['name'],
            'contact_role' => $contact['role'],
            'contact_avatar' => $contact['avatar'],
            'contact_meta' => $contact['meta'],
            'last_message' => Str::limit($latestMessage?->pesan ?? 'Belum ada pesan', 140),
            'last_message_at_iso' => optional($lastMessageAt)->toIso8601String(),
            'last_message_at' => optional($lastMessageAt)->translatedFormat('d M Y H:i') ?? '-',
            'last_message_label' => optional($lastMessageAt)->diffForHumans() ?? '-',
            'unread_count' => $unreadCount,
            'incoming_count' => $incomingCount,
            'sent_count' => $sentCount,
            'messages' => $thread->map(function (Message $message) use ($currentUser) {
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'sender_name' => $message->sender?->name ?? 'Sistem',
                    'direction' => $message->sender_id === $currentUser->id ? 'out' : 'in',
                    'text' => $message->pesan,
                    'attachment' => $message->lampiran,
                    'created_at_iso' => optional($message->created_at)->toIso8601String(),
                    'time' => $message->created_at?->format('H:i') ?? '-',
                    'date' => $message->created_at?->translatedFormat('d M Y') ?? '-',
                    'read' => filled($message->dibaca_pada),
                ];
            })->values(),
            'last_active' => optional($lastMessageAt)->diffForHumans() ?? '-',
        ];
    }

    private function normalizeContact(array $contact): array
    {
        return [
            'id' => $contact['key'],
            'recipient_type' => $contact['recipient_type'],
            'recipient_id' => $contact['recipient_id'],
            'name' => $contact['name'],
            'role' => $contact['role'],
            'avatar' => $contact['avatar'],
            'meta' => $contact['meta'],
        ];
    }

    private function conversationContact(Conversation $conversation, User $currentUser): array
    {
        $role = $currentUser->role;

        if ($role === 'peserta') {
            if ($conversation->mentor_id && $conversation->mentor?->user) {
                return $this->contactFromUser($conversation->mentor->user, 'mentor', $conversation->mentor->user->name, 'Mentor Lapangan');
            }

            if ($conversation->pembimbing_id && $conversation->pembimbing?->user) {
                return $this->contactFromUser($conversation->pembimbing->user, 'pembimbing', $conversation->pembimbing->user->name, 'Pembimbing Akademik');
            }

            if ($conversation->admin_id && $conversation->admin) {
                return $this->contactFromUser($conversation->admin, 'admin', $conversation->admin->name, 'Administrator');
            }
        }

        if ($role === 'admin' || $role === 'super_admin') {
            if ($conversation->mentor_id && $conversation->mentor?->user) {
                return $this->contactFromUser($conversation->mentor->user, 'mentor', $conversation->mentor->user->name, 'Mentor Lapangan');
            }

            if ($conversation->pembimbing_id && $conversation->pembimbing?->user) {
                return $this->contactFromUser($conversation->pembimbing->user, 'pembimbing', $conversation->pembimbing->user->name, 'Pembimbing Akademik');
            }

            if ($conversation->peserta?->user) {
                return $this->contactFromUser($conversation->peserta->user, 'peserta', $conversation->peserta->user->name, 'Peserta Magang');
            }
        }

        if ($role === 'mentor' && $conversation->peserta?->user) {
            if ($conversation->admin_id && $conversation->admin) {
                return $this->contactFromUser($conversation->admin, 'admin', $conversation->admin->name, 'Administrator');
            }

            return $this->contactFromUser($conversation->peserta->user, 'peserta', $conversation->peserta->user->name, 'Peserta Magang');
        }

        if ($role === 'pembimbing' && $conversation->peserta?->user) {
            if ($conversation->admin_id && $conversation->admin) {
                return $this->contactFromUser($conversation->admin, 'admin', $conversation->admin->name, 'Administrator');
            }

            return $this->contactFromUser($conversation->peserta->user, 'peserta', $conversation->peserta->user->name, 'Peserta Magang');
        }

        if ($conversation->peserta?->user) {
            return $this->contactFromUser($conversation->peserta->user, 'peserta', $conversation->peserta->user->name, 'Peserta Magang');
        }

        if ($conversation->mentor?->user) {
            return $this->contactFromUser($conversation->mentor->user, 'mentor', $conversation->mentor->user->name, 'Mentor Lapangan');
        }

        if ($conversation->pembimbing?->user) {
            return $this->contactFromUser($conversation->pembimbing->user, 'pembimbing', $conversation->pembimbing->user->name, 'Pembimbing Akademik');
        }

        if ($conversation->admin) {
            return $this->contactFromUser($conversation->admin, 'admin', $conversation->admin->name, 'Administrator');
        }

        return $this->contactFromUser(new User(['id' => 0, 'name' => 'Tidak Dikenal', 'role' => 'user']), 'user', 'Tidak Dikenal', 'Kontak');
    }

    private function contactFromPeserta(Peserta $peserta, string $recipientType): array
    {
        $user = $peserta->user ?? new User([
            'id' => $peserta->user_id,
            'name' => 'Peserta',
            'role' => 'peserta',
        ]);

        return [
            'key' => $recipientType.':'.$peserta->id,
            'recipient_type' => $recipientType,
            'recipient_id' => $peserta->id,
            'name' => $peserta->user?->name ?? 'Peserta',
            'role' => 'Peserta Magang',
            'avatar' => $user->avatar_url,
            'meta' => $peserta->nim ?? $peserta->user?->email ?? '-',
        ];
    }

    private function contactFromMentor(Mentor $mentor, string $recipientType): array
    {
        return [
            'key' => $recipientType.':'.$mentor->id,
            'recipient_type' => $recipientType,
            'recipient_id' => $mentor->id,
            'name' => $mentor->user?->name ?? 'Mentor',
            'role' => 'Mentor Lapangan',
            'avatar' => $mentor->user?->avatar_url,
            'meta' => $mentor->nip ?? $mentor->user?->email ?? '-',
        ];
    }

    private function contactFromPembimbing(Pembimbing $pembimbing, string $recipientType): array
    {
        return [
            'key' => $recipientType.':'.$pembimbing->id,
            'recipient_type' => $recipientType,
            'recipient_id' => $pembimbing->id,
            'name' => $pembimbing->user?->name ?? 'Pembimbing',
            'role' => 'Pembimbing Akademik',
            'avatar' => $pembimbing->user?->avatar_url,
            'meta' => $pembimbing->nidn_nip ?? $pembimbing->user?->email ?? '-',
        ];
    }

    private function contactFromUser(User $user, string $recipientType, string $name, string $roleLabel): array
    {
        return [
            'key' => $recipientType.':'.$user->id,
            'recipient_type' => $recipientType,
            'recipient_id' => $user->id,
            'name' => $name,
            'role' => $roleLabel,
            'avatar' => $user->avatar_url,
            'meta' => $user->email ?? $user->username ?? '-',
        ];
    }

    private function conversationType(Conversation $conversation): string
    {
        if ($conversation->mentor_id) {
            return 'Pesan Mentor';
        }

        if ($conversation->pembimbing_id) {
            return 'Pesan Pembimbing';
        }

        if ($conversation->admin_id) {
            return 'Pesan Admin';
        }

        return 'Pesan';
    }

    private function roleLabel(string $role): string
    {
        return match ($role) {
            'peserta' => 'Peserta Magang',
            'mentor' => 'Mentor Lapangan',
            'pembimbing' => 'Pembimbing Akademik',
            'admin', 'super_admin' => 'Administrator',
            default => ucfirst($role),
        };
    }

    private function resolveConversationForSend(User $user, array $validated): Conversation
    {
        $recipientType = $validated['recipient_type'];
        $recipientId = (int) $validated['recipient_id'];

        if ($user->role === 'peserta') {
            $peserta = $user->peserta;
            abort_unless($peserta, 403);

            return match ($recipientType) {
                'mentor' => $this->findOrCreateConversation($peserta->id, mentorId: $recipientId, topik: $validated['subject']),
                'pembimbing' => $this->findOrCreateConversation($peserta->id, pembimbingId: $recipientId, topik: $validated['subject']),
                'admin' => $this->findOrCreateConversation($peserta->id, adminId: $recipientId, topik: $validated['subject']),
                default => abort(422, 'Tujuan pesan tidak valid.'),
            };
        }

        if ($user->role === 'mentor') {
            $mentor = $user->mentor;
            abort_unless($mentor, 403);

            return match ($recipientType) {
                'peserta' => $this->findOrCreateConversation($recipientId, mentorId: $mentor->id, topik: $validated['subject']),
                default => abort(422, 'Tujuan pesan tidak valid.'),
            };
        }

        if ($user->role === 'pembimbing') {
            $pembimbing = $user->pembimbing;
            abort_unless($pembimbing, 403);

            return match ($recipientType) {
                'peserta' => $this->findOrCreateConversation($recipientId, pembimbingId: $pembimbing->id, topik: $validated['subject']),
                default => abort(422, 'Tujuan pesan tidak valid.'),
            };
        }

        return match ($recipientType) {
            'peserta' => $this->findOrCreateConversation($recipientId, adminId: $user->id, topik: $validated['subject']),
            'mentor' => $this->conversationForAdminToMentor($user, $recipientId, $validated['subject']),
            'pembimbing' => $this->conversationForAdminToPembimbing($user, $recipientId, $validated['subject']),
            'admin' => abort(422, 'Pesan ke admin lain belum didukung pada menu ini.'),
            default => abort(422, 'Tujuan pesan tidak valid.'),
        };
    }

    private function conversationForAdminToMentor(User $user, int $mentorId, ?string $topik): Conversation
    {
        $mentor = Mentor::query()->with(['internships' => fn ($query) => $query->latest('id')])->findOrFail($mentorId);
        $pesertaId = $mentor->internships->first()?->peserta_id;
        abort_unless($pesertaId, 422, 'Mentor belum memiliki peserta yang dapat dijadikan konteks percakapan.');

        return $this->findOrCreateConversation($pesertaId, mentorId: $mentor->id, adminId: $user->id, topik: $topik);
    }

    private function conversationForAdminToPembimbing(User $user, int $pembimbingId, ?string $topik): Conversation
    {
        $pembimbing = Pembimbing::query()->with(['internships' => fn ($query) => $query->latest('id')])->findOrFail($pembimbingId);
        $pesertaId = $pembimbing->internships->first()?->peserta_id;
        abort_unless($pesertaId, 422, 'PA / pembimbing belum memiliki peserta yang dapat dijadikan konteks percakapan.');

        return $this->findOrCreateConversation($pesertaId, pembimbingId: $pembimbing->id, adminId: $user->id, topik: $topik);
    }

    private function findOrCreateConversation(
        int $pesertaId,
        ?int $mentorId = null,
        ?int $pembimbingId = null,
        ?int $adminId = null,
        ?string $topik = null
    ): Conversation {
        return Conversation::firstOrCreate(
            [
                'peserta_id' => $pesertaId,
                'mentor_id' => $mentorId,
                'pembimbing_id' => $pembimbingId,
                'admin_id' => $adminId,
            ],
            [
                'topik' => $topik ?: 'Percakapan',
                'status' => 'aktif',
                'last_message_at' => now(),
            ]
        );
    }

    private function authorizeConversation(User $user, Conversation $conversation): void
    {
        $allowed = match ($user->role) {
            'peserta' => $conversation->peserta?->user_id === $user->id,
            'mentor' => $conversation->mentor?->user_id === $user->id,
            'pembimbing' => $conversation->pembimbing?->user_id === $user->id,
            default => $conversation->admin_id === $user->id || $user->role === 'super_admin',
        };

        abort_unless($allowed, 403);
    }

    private function sendNotification(Conversation $conversation, User $sender, string $message): void
    {
        $recipient = match (true) {
            $conversation->mentor_id && $conversation->mentor?->user => $conversation->mentor->user,
            $conversation->pembimbing_id && $conversation->pembimbing?->user => $conversation->pembimbing->user,
            $conversation->admin_id && $conversation->admin && $conversation->admin->id !== $sender->id => $conversation->admin,
            $conversation->peserta?->user && $conversation->peserta->user->id !== $sender->id => $conversation->peserta->user,
            default => null,
        };

        if (! $recipient) {
            return;
        }

        $preferences = method_exists($recipient, 'notificationPreference')
            ? $recipient->notificationPreference()->first()
            : null;

        if ($preferences && ! $preferences->pesan) {
            return;
        }

        if (! Schema::hasTable('notifications')) {
            return;
        }

        Notification::create([
            'user_id' => $recipient->id,
            'judul' => 'Pesan baru dari '.$sender->name,
            'pesan' => Str::limit($message, 160),
            'dibaca' => false,
        ]);
    }
}
