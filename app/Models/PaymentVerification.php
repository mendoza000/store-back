<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentVerification extends Model
{

    use HasFactory;

    protected $guarded = [];
    
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'actioned_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

}
