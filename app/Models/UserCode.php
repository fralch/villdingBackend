<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCode extends Model
{
    use HasFactory;

    // Nombre de la tabla (si no sigue la convención en plural)
    protected $table = 'user_codes';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'user_id',
        'code',
        'type',
        'expires_at',
    ];

    // Configura los tipos de datos de los campos
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}