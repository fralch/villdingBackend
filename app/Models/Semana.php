<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semana extends Model
{
    use HasFactory;

    protected $fillable = ['proyecto_id', 'numero_semana', 'fecha_inicio', 'fecha_fin', 'nombre', 'descripcion'];

    public function proyecto()
    {
        return $this->belongsTo(Project::class);
    }

    public function dias()
    {
        return $this->hasMany(Dia::class);
    }
}
