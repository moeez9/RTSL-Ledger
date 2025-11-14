<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\relation_ledger_request;
use App\Models\User;
use Illuminate\Controllers\LedgerController;
use Illuminate\Http\Ledger;

class Ledgers extends Model
{
    use HasFactory;

    protected $table = 'ledgers';

    protected $fillable = [
        'rlr_id',
        'date',
        'description',
        'inserted_by',
        'requested_by',
        'updated_by',
        'approved_by',
        'updated_time',
        'Qty',
        'Set',
        'Rate',
        'Total',
        'Debit',
        'Credit',
        'Balance',
    ];

    public $timestamps = false;

    protected $casts = [
        'date' => 'datetime',
        'updated_time' => 'datetime',
        'Qty' => 'integer',
        'Set' => 'integer',
        'Rate' => 'decimal:2',
        'Total' => 'decimal:2',
        'Debit' => 'decimal:2',
        'Credit' => 'decimal:2',
        'Balance' => 'decimal:2',
    ];

    // ----Relationships----

    public function relationLedgerRequest()
    {
        return $this->belongsTo(relation_ledger_request::class, 'rlr_id');
    }

    public function insertedBy()
    {
        return $this->belongsTo(User::class, 'inserted_by');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

}
