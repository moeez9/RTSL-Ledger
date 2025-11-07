<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class relation_ledger_request extends Model
{
    protected $fillable = [
        'seller_business_user_id',
        'buyer_business_user_id',
        'status'
    ];

    // Relations
    public function seller()
    {
        return $this->belongsTo(business_users::class, 'seller_business_user_id');
    }

    public function buyer()
    {
        return $this->belongsTo(business_users::class, 'buyer_business_user_id');
    }
}
