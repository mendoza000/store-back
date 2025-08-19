<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Traits\BelongsToStore;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Category extends Model
{
    use HasFactory, BelongsToStore, HasUuids;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $guarded = [];

    protected $keyType = 'string';

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
