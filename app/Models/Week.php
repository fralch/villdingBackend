<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Week extends Model
{
    use HasFactory;
    
    protected $table = 'weeks';
    protected $fillable = [
        'project_id',
        'start_date',
        'end_date'
    ];
    
    /**
     * Relación con el modelo Project.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relación con el modelo Day.
     */
    public function days()
    {
        return $this->hasMany(Day::class);
    }

    /**
     * Relación con el modelo Tracking.
     */
    public function trackings()
    {
        return $this->hasMany(Tracking::class);
    }

    /**
     * Relación con el modelo User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el modelo Activity.
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    
}
