<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\DateFormat;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model 
{
    use HasFactory, DateFormat;
    protected $table = 'memberships';
    protected $fillable = ['gym_id', 'name', 'description', 'duration_days', 'sessions', 'price'];
    protected $appends = ['created_at_formatted'];

    public function gyms()
    {
        return $this->belongsTo(Gym::class, 'gym_id');
    }
    
    public function getCreatedAtFormattedAttribute()
    {
        return $this->textFormatDate($this->created_at);
    }
}
