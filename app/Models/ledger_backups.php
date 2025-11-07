<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerBackup extends Model
{
    use HasFactory;

    protected $table = 'ledger_backups';

    protected $fillable = [
        'rlr_id',
        'ledger_id',
        'update_by',
        'delete_by',
        'old_data',
        'new_data',
        'action_type',
        'remarks',
        'created_at',
    ];

    // Automatically manage timestamps manually (since only created_at is used)
    public $timestamps = false;

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function relationLedgerRequest()
    {
        return $this->belongsTo(relation_ledger_request::class, 'rlr_id');
    }

    public function ledger()
    {
        return $this->belongsTo(ledgers::class, 'ledger_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'update_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'delete_by');
    }
}
