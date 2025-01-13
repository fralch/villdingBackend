<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracking extends Model
{
    use HasFactory;

    /*
    Schema::create('trackings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('week_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->string('title'); 
            $table->string('description')->nullable();
            $table->date('date')->default(now());   
            $table->boolean('status')->default(true); // Estado del seguimiento
            $table->timestamps();

            $table->foreign('week_id')->references('id')->on('weeks')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

     */
    protected $fillable = [
        'week_id',
        'project_id',
        'user_id',
        'title',
        'description',
        'date',
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
