<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\DateFormat;
use Illuminate\Database\Eloquent\Model;

class Cuenta extends Model
{
    use HasFactory, DateFormat;
    protected $table = 'cuentas';
    protected $fillable = ['nombre_cuenta', 'phone_empleados'];
    protected $appends = ['created_at_formatted'];

    public function getCreatedAtFormattedAttribute()
    {
        return $this->textFormatDate($this->created_at);
    }
}