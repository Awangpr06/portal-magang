<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Services\Communication\CommunicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunicationController extends Controller
{
    public function __construct(private readonly CommunicationService $communicationService)
    {
    }

    public function peserta(Request $request): View
    {
        $selectedConversation = $this->selectedConversation($request, 'peserta');

        if ($selectedConversation) {
            $this->communicationService->markRead($request->user(), $selectedConversation);
        }

        return view('peserta.komunikasi.pesan', [
            'activeCommunication' => 'Pesan',
            'communicationData' => $this->communicationService->forUser($request->user(), $selectedConversation),
        ]);
    }

    public function mentor(Request $request): View
    {
        $selectedConversation = $this->selectedConversation($request, 'mentor');

        if ($selectedConversation) {
            $this->communicationService->markRead($request->user(), $selectedConversation);
        }

        return view('mentor.komunikasi.pesan', [
            'communicationData' => $this->communicationService->forUser($request->user(), $selectedConversation),
        ]);
    }

    public function pembimbing(Request $request): View
    {
        $selectedConversation = $this->selectedConversation($request, 'pembimbing');

        if ($selectedConversation) {
            $this->communicationService->markRead($request->user(), $selectedConversation);
        }

        return view('pembimbing.komunikasi.pesan', [
            'communicationData' => $this->communicationService->forUser($request->user(), $selectedConversation),
        ]);
    }

    public function admin(Request $request): View
    {
        $selectedConversation = $this->selectedConversation($request, 'admin');

        if ($selectedConversation) {
            $this->communicationService->markRead($request->user(), $selectedConversation);
        }

        return view('admin.komunikasi.pesan', [
            'activeTab' => 'pesan',
            'communicationData' => $this->communicationService->forUser($request->user(), $selectedConversation),
        ]);
    }

    public function send(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'recipient_type' => ['required', 'in:peserta,mentor,pembimbing,admin'],
            'recipient_id' => ['required', 'integer'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'string', 'max:255'],
            'redirect_to' => ['nullable', 'string', 'max:255'],
        ]);

        $conversation = $this->communicationService->send($request->user(), $validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Pesan berhasil dikirim.',
                'conversation_id' => $conversation->id,
            ]);
        }

        return redirect($validated['redirect_to'] ?? url()->previous() ?? '/')
            ->with('success', 'Pesan berhasil dikirim.');
    }

    public function reply(Request $request, Conversation $conversation): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'string', 'max:255'],
            'redirect_to' => ['nullable', 'string', 'max:255'],
        ]);

        $this->communicationService->reply($request->user(), $conversation, $validated);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Balasan berhasil dikirim.',
                'conversation_id' => $conversation->id,
            ]);
        }

        return redirect($validated['redirect_to'] ?? url()->previous() ?? '/')
            ->with('success', 'Balasan berhasil dikirim.');
    }

    public function markRead(Request $request, Conversation $conversation): JsonResponse|RedirectResponse
    {
        $this->communicationService->markRead($request->user(), $conversation);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Percakapan ditandai sudah dibaca.']);
        }

        return back()->with('success', 'Percakapan ditandai sudah dibaca.');
    }

    private function selectedConversation(Request $request, string $role): ?Conversation
    {
        $conversationId = (int) $request->query('conversation');

        if (! $conversationId) {
            return null;
        }

        $query = Conversation::query()
            ->with(['peserta.user', 'mentor.user', 'pembimbing.user', 'admin', 'messages.sender'])
            ->whereKey($conversationId);

        $conversation = $query->first();

        if (! $conversation) {
            return null;
        }

        $user = $request->user();
        $allowed = match ($role) {
            'peserta' => $conversation->peserta?->user_id === $user->id,
            'mentor' => $conversation->mentor?->user_id === $user->id,
            'pembimbing' => $conversation->pembimbing?->user_id === $user->id,
            default => $conversation->admin_id === $user->id || $user->role === 'super_admin',
        };

        return $allowed ? $conversation : null;
    }
}
