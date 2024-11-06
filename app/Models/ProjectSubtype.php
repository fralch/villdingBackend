<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSubtype extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'project_type_id'];

    /**
     * Relación con ProjectType.
     */
    public function type()
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }

    /**
     * Relación uno-a-muchos con Project.
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'project_subtype_id');
    }
}
