<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class business_users extends Model
{
     use HasFactory;

    protected $table = 'business_users';

    public $timestamps = false; // because only created_at exists

    protected $fillable = ['user_id', 'bus_name_id', 'role_id'];

    /*--------------------------------------------
    | Relationships
    |--------------------------------------------*/


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function business()
    {
        return $this->belongsTo(businesses::class, 'bus_name_id');
    }

    public function role()
    {
        return $this->belongsTo(role_users::class, 'role_id');
    }

    public function sellerRelations()
    {
        return $this->hasMany(relation_ledger_request::class, 'seller_business_user_id');
    }

    public function buyerRelations()
    {
        return $this->hasMany(relation_ledger_request::class, 'buyer_business_user_id');
    }
}
