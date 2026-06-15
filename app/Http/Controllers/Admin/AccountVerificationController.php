<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VerificationHistory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountVerificationController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()
            ->with(['peserta.perguruanTinggi', 'mentor', 'pembimbing'])
            ->whereIn('role', ['peserta', 'mentor', 'pembimbing'])
            ->latest();

        $status = $request->input('status', 'menunggu');

        if ($status !== 'semua') {
            $query->where('account_status', $status);
        }

        if ($request->filled('role') && $request->role !== 'semua') {
            $query->where('role', $request->role);
        }

        if ($request->filled('tanggal') && $request->tanggal !== 'semua') {
            $query->when($request->tanggal === 'hari ini', fn ($q) => $q->whereDate('created_at', today()))
                ->when($request->tanggal === 'minggu ini', fn ($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                ->when($request->tanggal === 'bulan ini', fn ($q) => $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year));
        }

        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhereHas('peserta.perguruanTinggi', fn ($sub) => $sub->where('nama_pt', 'like', "%{$keyword}%"))
                    ->orWhereHas('mentor', fn ($sub) => $sub->where('divisi', 'like', "%{$keyword}%"))
                    ->orWhereHas('pembimbing', fn ($sub) => $sub->where('perguruan_tinggi', 'like', "%{$keyword}%")->orWhere('instansi', 'like', "%{$keyword}%"));
            });
        }

        $accounts = $query->paginate(10)->withQueryString();

        $stats = [
            'menunggu' => User::whereIn('role', ['peserta', 'mentor', 'pembimbing'])->where('account_status', 'menunggu')->count(),
            'disetujui' => User::whereIn('role', ['peserta', 'mentor', 'pembimbing'])->where('account_status', 'disetujui')->count(),
            'ditolak' => User::whereIn('role', ['peserta', 'mentor', 'pembimbing'])->where('account_status', 'ditolak')->count(),
            'total' => User::whereIn('role', ['peserta', 'mentor', 'pembimbing'])->count(),
        ];

        return view('admin.verifikasi.akun', compact('accounts', 'stats'));
    }

    public function approve(User $user): RedirectResponse
    {
        $this->ensureVerifiable($user);

        $user->update([
            'account_status' => 'disetujui',
            'rejection_reason' => null,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        VerificationHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'jenis' => 'akun',
            ],
            [
                'admin_id' => Auth::id(),
                'status' => 'disetujui',
                'keterangan' => 'Akun '.$user->name.' disetujui melalui menu verifikasi akun.',
                'verified_at' => now(),
            ]
        );

        return back()->with('success', "Akun {$user->name} berhasil disetujui.");
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        $this->ensureVerifiable($user);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $user->update([
            'account_status' => 'ditolak',
            'rejection_reason' => $validated['rejection_reason'],
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        VerificationHistory::updateOrCreate(
            [
                'user_id' => $user->id,
                'jenis' => 'akun',
            ],
            [
                'admin_id' => Auth::id(),
                'status' => 'ditolak',
                'keterangan' => $validated['rejection_reason'],
                'verified_at' => now(),
            ]
        );

        return back()->with('success', "Akun {$user->name} berhasil ditolak.");
    }

    private function ensureVerifiable(User $user): void
    {
        abort_unless(in_array($user->role, ['peserta', 'mentor', 'pembimbing'], true), 404);
    }
}
