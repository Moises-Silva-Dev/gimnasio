<?php

namespace App\Models;

use App\Traits\DateFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        'remaining_sessions',
        'payment_method',
        'payment_amount',
        'notes',
    ];

    protected $appends = [
        'created_at_formatted',
        'is_active',
        'days_remaining',
        'days_until_start',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount_paid' => 'decimal:2',
        'payment_amount' => 'decimal:2',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    // Atributos calculados
    public function getIsActiveAttribute()
    {
        $today = Carbon::today();
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        return $this->status === 'active'
            && $today->greaterThanOrEqualTo($startDate)
            && $today->lessThanOrEqualTo($endDate)
            && ($this->remaining_sessions > 0 || is_null($this->remaining_sessions));
    }

    public function getDaysRemainingAttribute()
    {
        $today = Carbon::today();
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        // Si aún no ha comenzado, retornar null
        if ($today->lessThan($startDate)) {
            return null;
        }

        // Si ya expiró, retornar 0
        if ($today->greaterThan($endDate)) {
            return 0;
        }

        // Calcular días restantes
        return $today->diffInDays($endDate, false);
    }

    public function getDaysUntilStartAttribute()
    {
        $today = Carbon::today();
        $startDate = Carbon::parse($this->start_date);

        if ($today->greaterThanOrEqualTo($startDate)) {
            return 0;
        }

        return $today->diffInDays($startDate);
    }

    // Eventos del modelo
    protected static function booted()
    {
        static::creating(function ($userMembership) {
            if (Auth::check() && empty($userMembership->created_by)) {
                $userMembership->created_by = Auth::id();
            }

            // Inicializar remaining_sessions si no está definido
            if (is_null($userMembership->remaining_sessions) && $userMembership->membership) {
                $userMembership->remaining_sessions = $userMembership->membership->sessions ?? 0;
            }

            // Inicializar payment_amount si no está definido
            if (is_null($userMembership->payment_amount)) {
                $userMembership->payment_amount = $userMembership->amount_paid;
            }
        });

        static::updating(function ($userMembership) {
            // Actualizar estado automáticamente basado en fechas
            $today = Carbon::today();
            $endDate = Carbon::parse($userMembership->end_date);

            if ($today->greaterThan($endDate) && $userMembership->status === 'active') {
                $userMembership->status = 'expired';
            }
        });
    }

    // Métodos auxiliares
    public function canDecrementSession()
    {
        return $this->is_active && $this->remaining_sessions > 0;
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['active', 'pending']);
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->textFormatDate($this->created_at);
    }
}
