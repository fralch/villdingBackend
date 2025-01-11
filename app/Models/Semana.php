<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semana extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'numero_semana', 'fecha_inicio', 'fecha_fin', 'nombre', 'descripcion'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function dias()
    {
        return $this->hasMany(Dia::class);
    }
}
