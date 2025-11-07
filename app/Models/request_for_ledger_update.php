<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class request_for_ledger_update extends Model
{
    use SoftDeletes;

    protected $table = 'request_for_ledger_updates';

    protected $fillable = [
        'ledger_id',
        'rlr_id',
        'seller_id',
        'buyer_id',
        'requested_by',
        'approved_by',
        'message',
        'status',
    ];

    // 🔗 Relations
    public function ledger()
    {
        return $this->belongsTo(Ledgers::class, 'ledger_id');
    }

    public function relationLedgerRequest()
    {
        return $this->belongsTo(relation_ledger_request::class, 'rlr_id');
    }

    public function seller()
    {
        return $this->belongsTo(business_users::class, 'seller_id');
    }

    public function buyer()
    {
        return $this->belongsTo(business_users::class, 'buyer_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(business_users::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(business_users::class, 'approved_by');
    }
}
