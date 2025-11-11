<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class businesses extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'businesses';

    protected $fillable = [
        'business_name',
        'cate_id',
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

    // Each business name belongs to a category
    public function categories()
    {
        return $this->belongsTo(categories::class, 'cate_id');
    }

    // A business name can have multiple business types
    public function businessTypes()
    {
        return $this->hasMany(business_users::class, 'bus_name_id');
    }
}
