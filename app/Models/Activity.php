<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_id',
        'project_id',
        'user_id',
        'name',
        'description',
        'hour_start',
        'hour_end',
        'status',
        'icon'
    ];

    /**
     * Relación con el modelo Day.
     */
    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    /**
     * Relación con el modelo Project.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relación con el modelo User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el modelo Tracking.
     */
    public function trackings()
    {
        return $this->hasMany(Tracking::class);
    }

    /**
     * Relación con el modelo Week.
     */
    public function weeks()
    {
        return $this->hasMany(Week::class);
    }
    

}
