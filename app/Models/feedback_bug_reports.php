<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackBugReports extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'feedback_bug_reports';

    protected $fillable = [
        'user_id',
        'type',
        'subject',
        'message',
        'attachments',
        'status',
        'admin_comment',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /*--------------------------------------------
    | Relationships
    |--------------------------------------------*/

    // Each feedback/bug report belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
