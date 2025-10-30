<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\DateFormat;
use Illuminate\Database\Eloquent\Model;

class Gym extends Model
{
    use HasFactory, DateFormat;
    protected $table = 'gyms';
    protected $fillable = ['name', 'address', 'phone'];
    protected $appends = ['created_at_formatted'];

    public function getCreatedAtFormattedAttribute()
    {
        return $this->textFormatDate($this->created_at);
    }
}
