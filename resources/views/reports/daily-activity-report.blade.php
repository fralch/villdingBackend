<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Diario - {{ $project->name }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 10pt;
            margin: 0;
        }
        .page-break {
            page-break-after: always;
        }
        .cover-page {
            background-color: #f0f4f8;
            height: 100vh;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .cover-header {
            text-align: right;
        }
        .cover-header img {
            max-width: 150px;
        }
        .cover-body {
            text-align: center;
        }
        .cover-title {
            font-size: 32pt;
            font-weight: bold;
            color: #1a4d5c;
            margin-bottom: 20px;
        }
        .cover-project-name {
            font-size: 24pt;
            color: #2c7a7b;
            margin-bottom: 40px;
        }
        .cover-image-container {
            width: 70%;
            margin: 0 auto 40px;
            border: 4px solid #2c7a7b;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .cover-image {
            max-width: 100%;
            max-height: 350px;
            object-fit: cover;
        }
        .cover-footer {
            text-align: center;
            font-size: 12pt;
            color: #555;
        }
        .activities-page {
            padding: 40px 50px;
            position: relative;
            min-height: 95vh;
        }
        .page-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1a4d5c;
            padding-bottom: 10px;
        }
        .page-header h1 {
            font-size: 20pt;
            color: #1a4d5c;
            margin: 0;
        }
        .page-header h2 {
            font-size: 14pt;
            color: #2c7a7b;
            margin: 5px 0;
        }
        .page-footer {
            position: absolute;
            bottom: 10px;
            left: 50px;
            right: 50px;
            text-align: center;
            font-size: 9pt;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        .activity-card {
            margin-bottom: 20px;
            page-break-inside: avoid;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }
        .activity-header {
            background-color: #1a4d5c;
            color: white;
            padding: 12px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .activity-name {
            font-size: 14pt;
            font-weight: bold;
        }
        .activity-status {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .activity-status.completado { background-color: #4ade80; color: #1a4d5c; }
        .activity-status.programado { background-color: #fbbf24; color: #1a4d5c; }
        .activity-status.pendiente { background-color: #ef4444; color: white; }
        .activity-body {
            display: flex;
            padding: 15px;
            gap: 15px;
        }
        .activity-details {
            width: 50%;
        }
        .activity-description {
            font-size: 10pt;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        .activity-meta {
            font-size: 9pt;
            color: #555;
        }
        .activity-images {
            width: 50%;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .activity-image-container {
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #f9f9f9;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .activity-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        .no-activities {
            text-align: center;
            padding: 100px 20px;
            font-size: 16pt;
            color: #999;
        }
    </style>
</head>
<body>
    <!-- PORTADA -->
    <div class="cover-page">
        <div class="cover-header">
            <!-- Puedes poner un logo aquí -->
            <!-- <img src="URL_DEL_LOGO" alt="Logo"> -->
        </div>
        <div class="cover-body">
            <div class="cover-title">REPORTE DE SEGUIMIENTO</div>
            <div class="cover-project-name">{{ strtoupper($project->name) }}</div>
            @if($project->uri)
                <div class="cover-image-container">
                    <img src="{{ $project->uri }}" alt="Proyecto" class="cover-image">
                </div>
            @endif
        </div>
        <div class="cover-footer">
            <div><strong>Seguimiento:</strong> {{ $tracking->title }}</div>
            <div><strong>Fecha:</strong> {{ $formattedDate }}</div>
            <div><strong>Semana:</strong> {{ $weekNumber }}</div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- PÁGINAS DE ACTIVIDADES -->
    @if($activities->count() > 0)
        @php
            $activitiesPerPage = 3;
            $chunks = $activities->chunk($activitiesPerPage);
        @endphp

        @foreach($chunks as $chunkIndex => $chunk)
            <div class="activities-page">
                <div class="page-header">
                    <h1>{{ strtoupper($tracking->title) }}</h1>
                    <h2>{{ ucfirst($formattedDate) }}</h2>
                </div>

                @foreach($chunk as $activity)
                    @php
                        $imageUrls = $activity->image_urls ?? [];
                        $statusClass = strtolower($activity->status);
                    @endphp

                    <div class="activity-card">
                        <div class="activity-header">
                            <span class="activity-name">{{ $activity->name }}</span>
                            <span class="activity-status {{ $statusClass }}">{{ $activity->status }}</span>
                        </div>
                        <div class="activity-body">
                            <div class="activity-details">
                                <div class="activity-description">
                                    {{ $activity->description ?? 'Sin descripción.' }}
                                </div>
                                <div class="activity-meta">
                                    @if($activity->location)
                                        <div><strong>Ubicación:</strong> {{ $activity->location }}</div>
                                    @endif
                                    @if($activity->comments)
                                        <div><strong>Comentarios:</strong> {{ $activity->comments }}</div>
                                    @endif
                                    <div><strong>Horas:</strong> {{ $activity->horas ?? 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="activity-images">
                                @if(count($imageUrls) > 0)
                                    @foreach(array_slice($imageUrls, 0, 4) as $imageUrl)
                                        <div class="activity-image-container">
                                            <img src="{{ $imageUrl }}" alt="Actividad" class="activity-image">
                                        </div>
                                    @endforeach
                                @else
                                    <div class="activity-image-container" style="grid-column: span 2;">
                                        <span>Sin imágenes</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="page-footer">
                    <span>Reporte de Seguimiento - {{ $project->name }}</span> | Página {{ $loop->iteration }} de {{ $chunks->count() }}
                </div>
            </div>

            @if(!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @else
        <div class="activities-page">
            <div class="page-header">
                <h1>{{ strtoupper($tracking->title) }}</h1>
                <h2>{{ ucfirst($formattedDate) }}</h2>
            </div>
            <div class="no-activities">
                No hay actividades registradas para este día.
            </div>
            <div class="page-footer">
                <span>Reporte de Seguimiento - {{ $project->name }}</span>
            </div>
        </div>
    @endif
</body>
</html>