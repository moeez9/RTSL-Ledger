<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'f_name',
        'date_of_birth',
        'gender',
        'phone_no',
        'profile_pic',
    ];
    protected $casts = [
        'date_of_birth' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*--------------------------------------------
    | Relationships
    |--------------------------------------------*/

    // Ledgers linked to user through inserted_by
    public function insertedLedgers()
    {
        return $this->hasMany(ledgers::class, 'inserted_by');
    }

    // Ledgers requested by user
    public function requestedLedgers()
    {
        return $this->hasMany(ledgers::class, 'requested_by');
    }

    // Ledgers updated by user
    public function updatedLedgers()
    {
        return $this->hasMany(ledgers::class, 'updated_by');
    }

    // Ledgers approved by user
    public function approvedLedgers()
    {
        return $this->hasMany(ledgers::class, 'approved_by');
    }

    // As buyer in relation_ledger_request
    public function buyerRequests()
    {
        return $this->hasMany(relation_ledger_request::class, 'buyer_id');
    }

    // As seller in relation_ledger_request
    public function sellerRequests()
    {
        return $this->hasMany(relation_ledger_request::class, 'seller_id');
    }

    // Requests for ledger updates
    public function ledgerUpdateRequests()
    {
        return $this->hasMany(request_for_ledger_update::class, 'requested_by');
    }

    // Feedback / Bug Reports
    public function feedbacks()
    {
        return $this->hasMany(feedback_bug_reports::class, 'user_id');
    }

    // Notifications
    public function notifications()
    {
        return $this->hasMany(notifications::class, 'user_id');
    }
}
