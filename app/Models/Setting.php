<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = ['key', 'value'];

    /**
     * Nilai bawaan jika admin belum pernah menyimpan pengaturan.
     */
    public const DEFAULTS = [
        'payment_deadline_minutes' => 60,
        'driver_fee_per_day' => 150000,
        'late_fee_mode' => 'hour',          // hour | day
        'late_fee_per_hour' => 25000,
        'late_fee_per_day' => 100000,
        'bank_info' => "BCA: 1234-5678-90 a.n. RentCar Indonesia\nMandiri: 0987-6543-21 a.n. RentCar Indonesia",
        'qris_image' => null,               // path di disk public; null = images/qris.png
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::allCached();

        if (array_key_exists($key, $all) && $all[$key] !== null) {
            return $all[$key];
        }

        return $default ?? (static::DEFAULTS[$key] ?? null);
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value === null ? null : (string) $value]);
        Cache::forget('app_settings');
    }

    /** @return array<string, string|null> */
    public static function allCached(): array
    {
        return Cache::remember('app_settings', 300, fn () => static::query()->pluck('value', 'key')->all());
    }

    public static function qrisUrl(): string
    {
        $path = static::get('qris_image');

        return $path ? asset('storage/' . ltrim($path, '/')) : asset('images/qris.png');
    }
}
