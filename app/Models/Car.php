<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cars';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'brand',
        'plate_number',
        'year',
        'transmission',
        'seats',
        'price_per_day',
        'image',
        'description',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'seats' => 'integer',
            'price_per_day' => 'decimal:2',
        ];
    }

    /**
     * Get the URL for the car's primary image.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes): string {
                $img = $attributes['image'] ?? null;
                if (! $img) {
                    return asset('storage/cars/avanza.jpg');
                }
                if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                    return $img;
                }
                return asset('storage/' . ltrim($img, '/'));
            }
        );
    }

    /**
     * Rata-rata rating ulasan (null jika belum ada ulasan).
     * Memakai hasil withAvg() bila tersedia agar tidak menambah query.
     */
    public function averageRating(): ?float
    {
        $avg = $this->reviews_avg_rating ?? $this->reviews()->avg('rating');

        return $avg === null ? null : round((float) $avg, 1);
    }

    /**
     * Category of this car.
     *
     * @return BelongsTo<CarCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CarCategory::class, 'category_id');
    }

    /**
     * Additional gallery images of this car.
     *
     * @return HasMany<CarImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class, 'car_id');
    }

    /**
     * Bookings for this car.
     *
     * @return HasMany<Booking, $this>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Reviews for this car.
     *
     * @return HasMany<Review, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
