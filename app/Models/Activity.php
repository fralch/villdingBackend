<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    /*
      Schema::create('activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('day_id');
            $table->unsignedBigInteger('project_id');  
            $table->unsignedBigInteger('user_id');
            $table->string('name'); // Nombre de la actividad
            $table->text('description')->nullable(); // Descripción de la actividad
            $table->time('hour_start');
            $table->time('hour_end');
            $table->string('status')->default('pendiente');    //pendiente, en progreso, finalizado
            $table->string('icon')->nullable();
            $table->timestamps();

            $table->foreign('day_id')->references('id')->on('days')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
     */

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
