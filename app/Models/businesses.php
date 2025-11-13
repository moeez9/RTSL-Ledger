<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class businesses extends Model
{
    use HasFactory;

    protected $table = 'businesses';

    protected $fillable = [
        'business_name',
        'cate_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    //Hide cate_id from JSON responses
    protected $hidden = ['cate_id'];

    //Automatically append readable category_name to JSON responses
    protected $appends = ['category_name'];

    /*--------------------------------------------
    | Relationships
    |--------------------------------------------*/

    // Each business name belongs to a category
    public function categories()
    {
        return $this->belongsTo(categories::class, 'cate_id');
    }

    // Accessor to get category name
    public function getCategoryNameAttribute()
    {
        return $this->categories ? $this->categories->category : null;
    }

    //prevent deletion (both soft and hard)
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            //stop deletion
            return false;
        });
    }

    // A business name can have multiple business types
    public function BusinessUsers()
    {
        return $this->hasMany(business_users::class, 'bus_name_id');
    }
}
