<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\PublicStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $settings = [];
        foreach (array_keys(Setting::DEFAULTS) as $key) {
            $settings[$key] = Setting::get($key);
        }
        $settings['qris_url'] = Setting::qrisUrl();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_deadline_minutes' => ['required', 'integer', 'min:5', 'max:10080'],
            'driver_fee_per_day' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'late_fee_mode' => ['required', 'in:hour,day'],
            'late_fee_per_hour' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'late_fee_per_day' => ['required', 'numeric', 'min:0', 'max:100000000'],
            'bank_info' => ['nullable', 'string', 'max:1000'],
            'qris_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'payment_deadline_minutes.min' => 'Tenggat pembayaran minimal 5 menit.',
        ]);

        foreach (['payment_deadline_minutes', 'driver_fee_per_day', 'late_fee_mode', 'late_fee_per_hour', 'late_fee_per_day'] as $key) {
            Setting::set($key, $validated[$key]);
        }
        Setting::set('bank_info', $validated['bank_info'] ?? '');

        if ($request->hasFile('qris_image')) {
            PublicStorageService::delete(Setting::get('qris_image'));
            Setting::set('qris_image', PublicStorageService::store($request->file('qris_image'), 'settings'));
        }

        return back()->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}
