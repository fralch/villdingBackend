<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{
    use HasFactory;

    // Nombre de la tabla (si no sigue la convención en plural)
    protected $table = 'project_user';

    protected $fillable = [
        'user_id',
        'project_id',
    ];

    // Relación uno-a-muchos con User.
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación uno-a-muchos con Project.
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

}