<?php

namespace App\Models;

use App\Traits\DateFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserMembership extends Model
{
    use HasFactory, DateFormat;

    protected $fillable = [
        'gym_id',
        'user_id',
        'membership_id',
        'payment_id',
        'start_date',
        'end_date',
        'amount_paid',
        'status',
        'created_by',
    ];
    protected $appends = [
        'created_at_formatted'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by'); // Admin
    }
    public function membership()
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }

    public function gym()
    {
        return $this->belongsTo(Gym::class, 'gym_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
    protected static function booted()
    {
        static::creating(function ($userMembership) {
            if (Auth::check() && empty($userMembership->created_by)) {
                $userMembership->created_by = Auth::id();
            }
        });
    }
}
