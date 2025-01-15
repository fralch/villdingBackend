<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    use HasFactory;

  
    protected $fillable = [
        'week_id',
        'project_id',
        'user_id',
        'title',
        'description',
        'date_start',
        'date_end',
        'status'
    ];

    /**
     * Relación con el modelo Week.
     */
    public function week()
    {
        return $this->belongsTo(Week::class);
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
     * Relación con el modelo Day.
     */
    public function days()
    {
        return $this->hasMany(Day::class);
    }

    /**
     * Relación con el modelo Activity.
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    
    
}
