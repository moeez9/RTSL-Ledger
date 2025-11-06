<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class banks extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'banks';

    protected $fillable = [
        'user_id',
        'bank_name',
        'account_title',
        'account_no',
        'branch_code',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /*--------------------------------------------
    | Relationships
    |--------------------------------------------*/

    // Each bank belongs to one user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
