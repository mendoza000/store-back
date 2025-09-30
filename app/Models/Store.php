<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'store';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];

    public function config()
    {
        return $this->hasOne(StoreConfig::class, 'store_id');
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class, 'store_id', 'id');
    }
}

