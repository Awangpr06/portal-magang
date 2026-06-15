<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationPreferenceController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pesan' => ['nullable', 'boolean'],
            'laporan' => ['nullable', 'boolean'],
            'penugasan' => ['nullable', 'boolean'],
            'absensi' => ['nullable', 'boolean'],
            'email' => ['nullable', 'boolean'],
        ]);

        $request->user()->notificationPreference()->updateOrCreate(
            ['user_id' => $request->user()->id],
            collect(['pesan', 'laporan', 'penugasan', 'absensi', 'email'])
                ->mapWithKeys(fn ($field) => [$field => (bool) ($validated[$field] ?? false)])
                ->all()
        );

        return back()->with('notification_preference_success', 'Pengaturan notifikasi berhasil disimpan.');
    }
}
