<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    use HasFactory;

    protected $fillable = ['dia_id', 'titulo', 'descripcion'];

    public function dia()
    {
        return $this->belongsTo(Dia::class);
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }
}
