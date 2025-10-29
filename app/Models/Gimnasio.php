<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\DateFormat;
use Illuminate\Database\Eloquent\Model;

class Gimnasio extends Model
{
    use HasFactory, DateFormat;
    protected $table = 'gimnasios';
    protected $fillable = ['nombre', 'email', 'telefono', 'hora_apertura', 'hora_cierre', 'maxima_capacidad', 'estatus', 'phone', 'calle', 'colonia', 'cp', 'municipio', 'estado'];
    protected $appends = ['created_at_formatted'];

    public function getCreatedAtFormattedAttribute()
    {
        return $this->textFormatDate($this->created_at);
    }
}
