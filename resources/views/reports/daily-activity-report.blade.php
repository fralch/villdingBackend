<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Diario - {{ $project->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            color: #333;
        }

        .page-break {
            page-break-after: always;
        }

        /* Estilos de la portada */
        .cover-page {
            text-align: center;
            padding: 40px 20px;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .cover-header {
            margin-top: 60px;
        }

        .cover-title {
            font-size: 48pt;
            font-weight: bold;
            margin-bottom: 40px;
            letter-spacing: 2px;
        }

        .cover-project-name {
            font-size: 32pt;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .cover-tracking-name {
            font-size: 24pt;
            margin-bottom: 10px;
        }

        .cover-week {
            font-size: 18pt;
            margin-bottom: 40px;
        }

        .cover-image-container {
            width: 80%;
            margin: 0 auto;
            border: 3px solid #000;
            padding: 20px;
            background-color: #f5f5f5;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cover-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .cover-image-placeholder {
            font-size: 18pt;
            color: #999;
        }

        .cover-footer {
            margin-top: 40px;
        }

        .cover-info {
            font-size: 18pt;
            margin: 10px 0;
        }

        /* Estilos de las páginas de actividades */
        .activities-page {
            padding: 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 24pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .page-header h2 {
            font-size: 16pt;
            margin-bottom: 5px;
        }

        .page-header h3 {
            font-size: 14pt;
            margin-bottom: 10px;
        }

        .page-header hr {
            border: none;
            border-top: 2px solid #000;
            margin: 10px 0;
        }

        /* Tarjeta de actividad */
        .activity-card {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .activity-header {
            background-color: #1a4d5c;
            color: white;
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 5px 5px 0 0;
        }

        .activity-status {
            background-color: #4ade80;
            color: #1a4d5c;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 10pt;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 5px;
        }

        .activity-status.programado {
            background-color: #fbbf24;
        }

        .activity-status.pendiente {
            background-color: #ef4444;
            color: white;
        }

        .activity-name-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .activity-name {
            font-size: 14pt;
            font-weight: bold;
        }

        .activity-icon {
            font-size: 18pt;
        }

        .activity-time {
            font-size: 11pt;
        }

        .activity-description {
            background-color: #1a4d5c;
            color: white;
            padding: 10px 15px;
            font-size: 10pt;
            line-height: 1.5;
        }

        .activity-images {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .activity-image-container {
            border: 2px solid #ddd;
            padding: 5px;
            background-color: #f9f9f9;
            text-align: center;
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .activity-image {
            max-width: 100%;
            max-height: 180px;
            object-fit: contain;
        }

        .image-placeholder {
            color: #999;
            font-size: 14pt;
        }

        /* Layout de 2 columnas para actividades */
        .two-column-layout {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }

        .column {
            width: 48%;
        }

        .column-left {
            width: 40%;
        }

        .column-right {
            width: 58%;
        }

        .full-width-image {
            border: 2px solid #ddd;
            padding: 5px;
            background-color: #f9f9f9;
            text-align: center;
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .full-width-image img {
            max-width: 100%;
            max-height: 280px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <!-- PORTADA -->
    <div class="cover-page">
        <div class="cover-header">
            <div class="cover-title">REPORTE DIARIO</div>
            <div class="cover-project-name">{{ strtoupper($project->name) }}</div>
            <div class="cover-tracking-name">{{ strtoupper($tracking->title) }}</div>
            <div class="cover-week">SEMANA {{ $weekNumber }}</div>
        </div>

        <div class="cover-image-container">
            @if($project->uri)
                <img src="{{ $project->uri }}" alt="Proyecto" class="cover-image">
            @else
                <div class="cover-image-placeholder">PORTADA DEL PROYECTO (FOTO)</div>
            @endif
        </div>

        <div class="cover-footer">
            <div class="cover-info">{{ strtoupper($project->location ?? 'UBICACIÓN') }}</div>
            <div class="cover-info">{{ strtoupper($project->company ?? 'EMPRESA EJECUTORA') }}</div>
            <div class="cover-info">{{ strtoupper($formattedDate) }}</div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- PÁGINAS DE ACTIVIDADES -->
    @if($activities->count() > 0)
        @php
            $activitiesPerPage = 2; // 2 actividades por página
            $chunks = $activities->chunk($activitiesPerPage);
        @endphp

        @foreach($chunks as $chunkIndex => $chunk)
            <div class="activities-page">
                <div class="page-header">
                    <h1>{{ strtoupper($tracking->title) }}</h1>
                    <h2>SEMANA {{ $weekNumber }}</h2>
                    <h3>{{ ucfirst($formattedDate) }}</h3>
                    <hr>
                </div>

                @foreach($chunk as $activity)
                    @php
                        $images = is_array($activity->image) ? $activity->image : (json_decode($activity->image, true) ?? []);
                        $imageUrls = $activity->image_urls ?? [];

                        // Determinar el estado traducido
                        $statusText = $activity->status;
                        $statusClass = strtolower($activity->status);
                        if ($statusClass === 'completado') {
                            $statusText = 'Completado';
                        } elseif ($statusClass === 'pendiente') {
                            $statusText = 'Pendiente';
                        } elseif ($statusClass === 'programado') {
                            $statusText = 'Programado';
                        }
                    @endphp

                    <div class="activity-card">
                        @if(count($imageUrls) === 0)
                            <!-- Sin imágenes: solo tarjeta de actividad -->
                            <div class="activity-header">
                                <div>
                                    <div class="activity-status {{ $statusClass }}">{{ ucfirst($statusText) }}</div>
                                    <div class="activity-name-row">
                                        <div class="activity-name">{{ $activity->name }}</div>
                                        @if($activity->icon)
                                            <div class="activity-icon">{{ $activity->icon }}</div>
                                        @else
                                            <div class="activity-icon">🏗️</div>
                                        @endif
                                    </div>
                                    <div class="activity-time">{{ $activity->horas ?? '00:00' }} horas</div>
                                </div>
                            </div>
                            @if($activity->description)
                                <div class="activity-description">
                                    {{ $activity->description }}
                                    @if($activity->location)
                                        <br><strong>Ubicación:</strong> {{ $activity->location }}
                                    @endif
                                    @if($activity->comments)
                                        <br><strong>Comentarios:</strong> {{ $activity->comments }}
                                    @endif
                                </div>
                            @endif

                        @elseif(count($imageUrls) <= 4)
                            <!-- Actividad con 1-4 imágenes: layout en 2 columnas -->
                            <div class="two-column-layout">
                                <div class="column-left">
                                    <div class="activity-header">
                                        <div>
                                            <div class="activity-status {{ $statusClass }}">{{ ucfirst($statusText) }}</div>
                                            <div class="activity-name-row">
                                                <div class="activity-name">{{ $activity->name }}</div>
                                                @if($activity->icon)
                                                    <div class="activity-icon">{{ $activity->icon }}</div>
                                                @else
                                                    <div class="activity-icon">🏗️</div>
                                                @endif
                                            </div>
                                            <div class="activity-time">{{ $activity->horas ?? '00:00' }} horas</div>
                                        </div>
                                    </div>
                                    @if($activity->description)
                                        <div class="activity-description">
                                            {{ $activity->description }}
                                            @if($activity->location)
                                                <br><strong>Ubicación:</strong> {{ $activity->location }}
                                            @endif
                                            @if($activity->comments)
                                                <br><strong>Comentarios:</strong> {{ $activity->comments }}
                                            @endif
                                        </div>
                                    @endif

                                    @foreach(array_slice($imageUrls, 0, 3) as $imageUrl)
                                        <div class="activity-image-container">
                                            <img src="{{ $imageUrl }}" alt="Actividad" class="activity-image">
                                        </div>
                                    @endforeach
                                </div>

                                <div class="column-right">
                                    @if(isset($imageUrls[3]))
                                        <div class="full-width-image">
                                            <img src="{{ $imageUrls[3] }}" alt="Actividad">
                                        </div>
                                    @endif

                                    @foreach(array_slice($imageUrls, 4, 2) as $imageUrl)
                                        <div class="activity-image-container">
                                            <img src="{{ $imageUrl }}" alt="Actividad" class="activity-image">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        @else
                            <!-- Actividad con 5 imágenes: layout especial -->
                            <div class="two-column-layout">
                                <div class="column-left">
                                    <div class="activity-header">
                                        <div>
                                            <div class="activity-status {{ $statusClass }}">{{ ucfirst($statusText) }}</div>
                                            <div class="activity-name-row">
                                                <div class="activity-name">{{ $activity->name }}</div>
                                                @if($activity->icon)
                                                    <div class="activity-icon">{{ $activity->icon }}</div>
                                                @else
                                                    <div class="activity-icon">🏗️</div>
                                                @endif
                                            </div>
                                            <div class="activity-time">{{ $activity->horas ?? '00:00' }} horas</div>
                                        </div>
                                    </div>
                                    @if($activity->description)
                                        <div class="activity-description">
                                            {{ $activity->description }}
                                            @if($activity->location)
                                                <br><strong>Ubicación:</strong> {{ $activity->location }}
                                            @endif
                                            @if($activity->comments)
                                                <br><strong>Comentarios:</strong> {{ $activity->comments }}
                                            @endif
                                        </div>
                                    @endif

                                    @foreach(array_slice($imageUrls, 0, 3) as $imageUrl)
                                        <div class="activity-image-container">
                                            <img src="{{ $imageUrl }}" alt="Actividad" class="activity-image">
                                        </div>
                                    @endforeach
                                </div>

                                <div class="column-right">
                                    <div class="full-width-image">
                                        <img src="{{ $imageUrls[3] }}" alt="Actividad">
                                    </div>
                                    <div class="activity-image-container">
                                        <img src="{{ $imageUrls[4] }}" alt="Actividad" class="activity-image">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if(!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @else
        <!-- Si no hay actividades para este día -->
        <div class="activities-page">
            <div class="page-header">
                <h1>{{ strtoupper($tracking->title) }}</h1>
                <h2>SEMANA {{ $weekNumber }}</h2>
                <h3>{{ ucfirst($formattedDate) }}</h3>
                <hr>
            </div>

            <div style="text-align: center; padding: 100px 20px; font-size: 18pt; color: #999;">
                No hay actividades registradas para este día
            </div>
        </div>
    @endif
</body>
</html>
