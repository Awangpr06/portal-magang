<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccountSettingController extends Controller
{
    private function deviceLabel(Request $request): string
    {
        $agent = strtolower((string) $request->userAgent());

        return str_contains($agent, 'mobile') ? 'Mobile' : (str_contains($agent, 'tablet') ? 'Tablet' : 'Desktop');
    }

    private function exportActivities(Request $request, string $filename, ?string $keyword = null): StreamedResponse
    {
        $user = $request->user();
        $query = $user->securityActivities()->latest();

        if ($keyword) {
            $query->where('aktivitas', 'like', '%'.$keyword.'%');
        }

        $activities = $query->get();

        return response()->streamDownload(function () use ($activities) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($output, ['Waktu', 'Aktivitas', 'Perangkat', 'Browser', 'Lokasi/IP', 'Status', 'Catatan']);

            foreach ($activities as $activity) {
                fputcsv($output, [
                    optional($activity->created_at)->translatedFormat('d M Y H:i') ?? '-',
                    $activity->aktivitas ?? '-',
                    $activity->perangkat ?? '-',
                    $activity->browser ?? '-',
                    $activity->ip_address ?? '-',
                    $activity->status ?? '-',
                    $activity->catatan ?? '-',
                ]);
            }

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function downloadProfile(Request $request): StreamedResponse
    {
        $user = $request->user();

        return response()->streamDownload(function () use ($user) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($output, ['Nama', 'Username', 'Email', 'Role', 'Telepon', 'Alamat', 'Status Akun', '2FA Aktif', 'Terakhir Password Diubah']);
            fputcsv($output, [
                $user->name ?? '-',
                $user->username ?? '-',
                $user->email ?? '-',
                $user->role ?? '-',
                $user->phone ?? '-',
                $user->address ?? '-',
                $user->account_status ?? '-',
                $user->two_factor_enabled ? 'Ya' : 'Tidak',
                optional($user->password_changed_at)->translatedFormat('d M Y H:i') ?? '-',
            ]);
            fclose($output);
        }, 'profil-admin.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportProfileHistory(Request $request): StreamedResponse
    {
        return $this->exportActivities($request, 'histori-profil-admin.csv');
    }

    public function exportPasswordHistory(Request $request): StreamedResponse
    {
        return $this->exportActivities($request, 'histori-password-admin.csv', 'password');
    }

    public function resetProfile(Request $request): JsonResponse
    {
        $user = $request->user()->fresh();

        return response()->json([
            'message' => 'Data profil berhasil dimuat ulang dari database.',
            'profile' => [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'avatar_url' => $user->avatar_url,
            ],
        ]);
    }

    public function requestProfileReview(Request $request): JsonResponse
    {
        $user = $request->user();

        SecurityActivity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Mengajukan verifikasi ulang profil admin.',
            'perangkat' => $this->deviceLabel($request),
            'browser' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'status' => 'berhasil',
            'catatan' => 'Permintaan verifikasi ulang dicatat pada histori keamanan.',
        ]);

        return response()->json([
            'message' => 'Permintaan verifikasi ulang profil berhasil disimpan ke database.',
        ]);
    }

    public function toggleTwoFactor(Request $request): JsonResponse
    {
        $user = $request->user();
        $enabled = ! (bool) $user->two_factor_enabled;

        $user->forceFill([
            'two_factor_enabled' => $enabled,
        ])->save();

        SecurityActivity::create([
            'user_id' => $user->id,
            'aktivitas' => $enabled ? 'Mengaktifkan 2FA admin.' : 'Menonaktifkan 2FA admin.',
            'perangkat' => $this->deviceLabel($request),
            'browser' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'status' => 'berhasil',
            'catatan' => $enabled ? 'Autentikasi 2FA diaktifkan.' : 'Autentikasi 2FA dinonaktifkan.',
        ]);

        return response()->json([
            'message' => $enabled ? 'Autentikasi 2FA berhasil diaktifkan.' : 'Autentikasi 2FA berhasil dinonaktifkan.',
            'enabled' => $enabled,
        ]);
    }

    public function logoutDevices(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->forceFill([
            'remember_token' => Str::random(60),
            'is_online' => false,
        ])->save();

        SecurityActivity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Logout semua perangkat admin.',
            'perangkat' => $this->deviceLabel($request),
            'browser' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'status' => 'berhasil',
            'catatan' => 'Semua sesi login lama dinyatakan tidak berlaku.',
        ]);

        return response()->json([
            'message' => 'Semua perangkat berhasil dilogout.',
        ]);
    }

    public function verifySecurity(Request $request): JsonResponse
    {
        $user = $request->user();

        SecurityActivity::create([
            'user_id' => $user->id,
            'aktivitas' => 'Verifikasi keamanan akun admin.',
            'perangkat' => $this->deviceLabel($request),
            'browser' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'status' => 'berhasil',
            'catatan' => 'Pengecekan keamanan akun dicatat ke histori.',
        ]);

        return response()->json([
            'message' => 'Verifikasi keamanan berhasil diproses.',
        ]);
    }
}
