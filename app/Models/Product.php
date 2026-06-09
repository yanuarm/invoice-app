<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'unit',
        'price',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (! $product->sku) {
                $prefix = 'PRD';
                $year = now()->format('y');
                $month = now()->format('m');
                $random = Str::upper(Str::random(4));
                $product->sku = "{$prefix}{$year}{$month}-{$random}";
            }
            $product->created_by = auth()->id() ?? $product->created_by;
        });
    }
}
