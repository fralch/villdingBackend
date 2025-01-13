<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_id',
        'project_id',
        'week_id',
        'date'
    ];

    /**
     * Relación con el modelo Tracking.
     */
    public function tracking()
    {
        return $this->belongsTo(Tracking::class);
    }

    /**
     * Relación con el modelo Project.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relación con el modelo Week.
     */
    public function week()
    {
        return $this->belongsTo(Week::class);
    }

    /**
     * Relación con el modelo Activity.
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * Relación con el modelo User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

   


}
