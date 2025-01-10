<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividade extends Model
{
    use HasFactory;

    protected $fillable = ['seguimiento_id', 'titulo', 'descripcion', 'hora_inicio', 'hora_fin', 'estado', 'es_activo'];

    public function seguimiento()
    {
        return $this->belongsTo(Seguimiento::class);
    }
}
