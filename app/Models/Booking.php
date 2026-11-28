<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bookings';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'car_id',
        'borrower_name',
        'borrower_phone',
        'start_date',
        'end_date',
        'total_days',
        'with_driver',
        'driver_fee',
        'total_price',
        'booking_status',
        'payment_deadline',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'total_days' => 'integer',
            'with_driver' => 'boolean',
            'driver_fee' => 'decimal:2',
            'total_price' => 'decimal:2',
            'payment_deadline' => 'datetime',
        ];
    }

    /**
     * Customer who made the booking.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Booked car.
     *
     * @return BelongsTo<Car, $this>
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Payment record for this booking.
     *
     * @return HasOne<Payment, $this>
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Return record for this booking.
     *
     * @return HasOne<CarReturn, $this>
     */
    public function carReturn(): HasOne
    {
        return $this->hasOne(CarReturn::class, 'booking_id');
    }

    /**
     * Review for this booking.
     *
     * @return HasOne<Review, $this>
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Hitung jumlah hari sewa dari tanggal+jam (dibulatkan ke atas per 24 jam, minimal 1 hari).
     */
    public static function calculateDays(string $startDate, string $endDate): int
    {
        $minutes = abs(Carbon::parse($startDate)->diffInMinutes(Carbon::parse($endDate)));

        return max(1, (int) ceil($minutes / 1440));
    }

    /**
     * Rincian biaya yang dihitung server (sumber kebenaran, bukan dari input klien).
     *
     * @return array{days:int, car_total:float, driver_fee:float, total:float}
     */
    public static function calculatePricing(Car $car, string $startDate, string $endDate, bool $withDriver): array
    {
        $days = static::calculateDays($startDate, $endDate);
        $carTotal = $days * (float) $car->price_per_day;
        $driverFee = $withDriver ? $days * (float) Setting::get('driver_fee_per_day') : 0.0;

        return [
            'days' => $days,
            'car_total' => $carTotal,
            'driver_fee' => $driverFee,
            'total' => $carTotal + $driverFee,
        ];
    }

    /**
     * Periksa apakah mobil sudah dipesan pada rentang waktu yang bertabrakan.
     */
    public static function hasDateConflict(int $carId, string $startDate, string $endDate, ?int $ignoreBookingId = null): bool
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        return static::query()
            ->where('car_id', $carId)
            ->whereIn('booking_status', ['pending', 'confirmed', 'ongoing'])
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->where('start_date', '<', $end)
            ->where('end_date', '>', $start)
            ->exists();
    }

    /**
     * Apakah tenggat pembayaran sudah lewat dan booking belum punya pembayaran yang layak diproses?
     * Booking yang bukti bayarnya sedang menunggu verifikasi petugas tidak dibatalkan otomatis.
     */
    public function isPaymentExpired(): bool
    {
        if ($this->booking_status !== 'pending' || ! $this->payment_deadline || $this->payment_deadline->isFuture()) {
            return false;
        }

        $payment = $this->payment;

        if (! $payment) {
            return true;
        }

        if ($payment->payment_status === 'verified') {
            return false;
        }

        // bukti sudah diunggah & belum ditolak -> biarkan petugas memverifikasi
        return ! ($payment->payment_status === 'pending' && $payment->proof_image);
    }

    /**
     * Batalkan otomatis semua booking pending yang melewati tenggat bayar.
     * Dipanggil oleh scheduler (bookings:expire) dan secara "lazy" di halaman terkait.
     *
     * @return int jumlah booking yang dibatalkan
     */
    public static function expireOverdue(): int
    {
        $count = 0;

        static::query()
            ->with('payment')
            ->where('booking_status', 'pending')
            ->whereNotNull('payment_deadline')
            ->where('payment_deadline', '<', now())
            ->get()
            ->each(function (Booking $booking) use (&$count): void {
                if (! $booking->isPaymentExpired()) {
                    return;
                }

                $booking->update(['booking_status' => 'cancelled']);

                if ($booking->payment && $booking->payment->payment_status !== 'verified') {
                    $booking->payment->update(['payment_status' => 'rejected']);
                }

                $count++;
            });

        return $count;
    }
}
