<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    /**
     * Mass-assignable product attributes
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'qty'
    ];

    /**
     * Attributes that should be sent to frontend when requested in list format
     *
     * @var array<string>
     */
    public const FRONTFACING_LIST_ATTRIBUTES = [
        'name',
        'sku',
        'description',
        'price',
        'qty'
    ];

    /**
     * A product may have multiple order items
     *
     * @return HasMany
     */
    public function orderItem(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
