<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'company',
        'code',
        'start_date',
        'end_date',
        'uri',
        'project_type_id',
        'project_subtype_id',
    ];

    /**
     * Relación con el modelo ProjectType (Tipo de Proyecto).
     */
    public function type()
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }

    /**
     * Relación con el modelo ProjectSubtype (Subtipo de Proyecto).
     */
    public function subtype()
    {
        return $this->belongsTo(ProjectSubtype::class, 'project_subtype_id');
    }

    /**
     * Relación de muchos a muchos con el modelo User.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user')
                    ->withPivot('is_admin')
                    ->withTimestamps();
    }
}
