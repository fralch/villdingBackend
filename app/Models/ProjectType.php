<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Relación uno-a-muchos con ProjectSubtype.
     */
    public function subtypes()
    {
        return $this->hasMany(ProjectSubtype::class);
    }

    /**
     * Relación uno-a-muchos con Project.
     */
    public function projects()
    {
        return $this->hasMany(Project::class, 'project_type_id');
    }
}
