<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarReturn extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'returns';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'booking_id',
        'actual_return_date',
        'condition_description',
        'late_days',
        'late_fee',
        'damage_fee',
        'total_fine',
        'fine_status',
        'fine_payment_method',
        'fine_proof_image',
        'fine_paid_at',
        'return_status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'actual_return_date' => 'datetime',
            'late_days' => 'integer',
            'late_fee' => 'decimal:2',
            'damage_fee' => 'decimal:2',
            'total_fine' => 'decimal:2',
            'fine_paid_at' => 'datetime',
        ];
    }

    /**
     * Masih ada denda yang harus dilunasi customer.
     */
    public function hasOutstandingFine(): bool
    {
        return (float) $this->total_fine > 0 && $this->fine_status !== 'paid';
    }

    public function hasFine(): bool
    {
        return (float) $this->total_fine > 0;
    }

    /**
     * Associated booking.
     *
     * @return BelongsTo<Booking, $this>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
