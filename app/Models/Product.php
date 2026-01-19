<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
     * Attributes that should be sent to frontend when requesting specific product
     *
     * @var array<string>
     */
    public const FRONTFACING_SHOW_ATTRIBUTES = [
        'name',
        'sku',
        'description',
        'price',
        'qty'
    ]; // Currently the same as list, but with time product tables usually bloat up with new columns
}
