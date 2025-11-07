<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notifications extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'sender_id',
        'title',
        'message',
        'type',
        'action_type',
        'related_table',
        'related_id',
        'is_read',
        'status',
        'created_at',
        'updated_at',
        'expired_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public $timestamps = false;

    // ─── Relationships ────────────────────────────────────────────────

    public function user()
    {
        // The user who received the notification
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender()
    {
        // The user who triggered/sent the notification
        return $this->belongsTo(User::class, 'sender_id');
    }

    // ─── Helper Scopes / Accessors ─────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}
