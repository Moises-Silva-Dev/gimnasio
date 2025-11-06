<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserMembership extends Model
{
    use HasFactory;

    protected $table = 'user_memberships';

    protected $fillable = [
        'user_id',
        'membership_id',
        'start_date',
        'end_date',
        'remaining_sessions',
        'last_session_reset',
        'status', // active, expired, cancelled
        'payment_amount',
        'payment_method',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'last_session_reset' => 'date',
        'payment_amount' => 'decimal:2',
    ];

    protected $appends = ['is_active', 'days_remaining', 'days_until_start', 'status_label'];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    // Accesor para verificar si está activa
    public function getIsActiveAttribute()
    {
        $today = Carbon::today();
        return $this->status === 'active' &&
            $this->start_date <= $today &&
            $this->end_date >= $today;
    }

    // Accesor para días restantes
    public function getDaysRemainingAttribute()
    {
        $today = Carbon::today();

        // Si aún no ha comenzado
        if ($this->start_date > $today) {
            return null; // Retorna null para membresías futuras
        }

        // Si ya expiró
        if ($this->end_date < $today) {
            return 0;
        }

        // Calcular días desde hoy hasta el fin (incluyendo hoy)
        return $today->diffInDays($this->end_date) + 1;
    }

    // Nuevo accesor para días hasta el inicio
    public function getDaysUntilStartAttribute()
    {
        $today = Carbon::today();

        if ($this->start_date <= $today) {
            return 0;
        }

        return $today->diffInDays($this->start_date);
    }

    // Accesor para el estado de la membresía
    public function getStatusLabelAttribute()
    {
        $today = Carbon::today();

        if ($this->start_date > $today) {
            return 'pending'; // Membresía programada
        }

        return $this->status;
    }

    // Resetear sesiones del día
    public function resetDailySessions()
    {
        $this->remaining_sessions = $this->membership->sessions;
        $this->save();
    }

    // Métodos útiles
    public function decrementSession()
    {
        if ($this->remaining_sessions > 0) {
            $this->remaining_sessions--;
            $this->save();
        }
    }

    public function cancel()
    {
        $this->status = 'cancelled';
        $this->save();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('end_date', '>=', Carbon::now());
    }

    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('status', 'expired')
                ->orWhere('end_date', '<', Carbon::now());
        });
    }
}
