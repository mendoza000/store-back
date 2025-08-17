<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToStore;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Product extends Model
{

    use HasFactory, BelongsToStore, HasUuids;

    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
        'category_id',
        'image'
    ];

    protected $casts = [
        'metadata' => 'array',
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    protected $primaryKey = 'id';
    
    public $incrementing = false;
    protected $keyType = 'string';

    public function getRouteKeyName(): string
    {
        return 'id';
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
