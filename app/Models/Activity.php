<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

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

    protected $casts = [
        'image' => 'array',
    ];

    protected $appends = [
        'image_urls',
    ];

    /**
     * Relación con el modelo Project.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getImageUrlsAttribute(): array
    {
        $paths = $this->getAttributeValue('image') ?? [];
        if (!is_array($paths)) {
            $paths = json_decode((string) $paths, true) ?: [];
        }

        return array_values(array_filter(array_map(function ($path) {
            return $this->transformImagePathToUrl($path);
        }, $paths)));
    }

    protected function transformImagePathToUrl($path): ?string
    {
        if (!is_string($path)) {
            return null;
        }

        $path = trim($path);
        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, 'images/activities/')) {
            return asset($path);
        }

        if (Str::contains($path, '/')) {
            return Storage::disk('s3')->url($path);
        }

        return asset('images/activities/' . ltrim($path, '/'));
    }

    /**
     * Relación con el modelo Tracking.
     */
    public function tracking()
    {
        return $this->belongsTo(Tracking::class);
    }
}
