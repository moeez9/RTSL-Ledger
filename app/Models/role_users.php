<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class role_users extends Model
{
    use HasFactory;

    protected $table = 'user_roles';

    protected $fillable = [
        'user_id',
        'role',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
