<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';
    
    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    protected $casts = [
        'status' => 'string',
        'account_info' => 'array',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }


}
