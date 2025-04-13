<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'project_id',
        'tracking_id', 
        'name',
        'description',
        'location',
        'horas',
        'status',
        'icon',
        'image',
        'comments', 
        'fecha_creacion',
    ];

    /**
     * Relación con el modelo Project.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relación con el modelo Tracking.
     */
    public function tracking()
    {
        return $this->belongsTo(Tracking::class);
    }
}
