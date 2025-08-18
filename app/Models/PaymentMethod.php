<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_method';
    
    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    protected $casts = [
        'status' => 'string',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    


}
