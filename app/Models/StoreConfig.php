<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreConfig extends Model
{
    protected $table = 'store_configs';
    protected $fillable = [
        'store_id',
        'products',
        'categories',
        'cupons',
        'gifcards',
        'wishlist',
        'reviews',
        'notifications_emails',
        'notifications_telegram',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}

