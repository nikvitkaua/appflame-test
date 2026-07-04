<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'title',
        'description',
        'brand',
        'category',
        'sku',
        'price',
        'discount_percentage',
        'rating',
        'stock',
        'thumbnail',
        'images',
        'tags',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'rating' => 'decimal:2',
        'stock' => 'integer',
        'images' => 'array',
        'tags' => 'array',
    ];
}
