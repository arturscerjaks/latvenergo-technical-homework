<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'total_price',
        'total_amount'
    ];

    /**
     * An order is always made by 1 user.
     *
     * @return BelongsTo<User, Order>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * An order can contain multiple ordered items (products).
     *
     * @return HasMany<OrderItem, Order>
     */
    public function orderItem(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
