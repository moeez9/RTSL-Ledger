<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class role_users extends Model
{
    use HasFactory;

    protected $table = 'role_users';

    protected $fillable = [
        'user_id',
        'role',
    ];
    public $timestamps = false;

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
