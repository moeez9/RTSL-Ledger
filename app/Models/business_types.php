<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class business_types extends Model
{
     use HasFactory;

    protected $table = 'business_types';

    public $timestamps = false; // because only created_at exists

    protected $fillable = [
        'user_id',
        'bus_name_id',
        'role',
        'created_at',
    ];

    /*--------------------------------------------
    | Relationships
    |--------------------------------------------*/

    // Each business type belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Each business type is associated with a business name
    public function businessName()
    {
        return $this->belongsTo(businesses::class, 'bus_name_id');
    }

    // Each business type has a specific role (buyer/seller etc.)
    public function roleType()
    {
        return $this->belongsTo(role_users::class, 'role');
    }
}
