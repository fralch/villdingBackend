<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Diario - {{ $project->name }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 10pt;
            margin: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .page-break {
            page-break-after: always;
        }

        /* Cover Page */
        .cover-page {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100vh;
            background-color: #f8f9fa;
            padding: 3cm;
            text-align: center;
        }
        .cover-header {
            text-align: right;
            height: 50px;
        }
        .cover-header img {
            max-height: 50px;
        }
        .cover-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .cover-title {
            font-size: 28pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .cover-project-name {
            font-size: 20pt;
            color: #34495e;
            margin-bottom: 30px;
        }
        .cover-image-container {
            width: 100%;
            max-width: 15cm;
            margin: 0 auto 30px;
            border: 1px solid #dee2e6;
            padding: 8px;
            background-color: #fff;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }
        .cover-image {
            max-width: 100%;
            max-height: 8cm;
            object-fit: contain;
        }
        .cover-footer {
            font-size: 11pt;
            color: #7f8c8d;
        }
        .cover-footer div {
            margin-bottom: 5px;
        }

        /* Content Pages */
        .content-page {
            padding: 2cm;
            position: relative;
            min-height: calc(100vh - 4cm); /* Full A4 height minus padding */
        }
        .page-header {
            position: absolute;
            top: 1cm;
            left: 2cm;
            right: 2cm;
            text-align: right;
            font-size: 9pt;
            color: #7f8c8d;
        }
        .page-footer {
            position: absolute;
            bottom: 1cm;
            left: 2cm;
            right: 2cm;
            text-align: center;
            font-size: 9pt;
            color: #7f8c8d;
            border-top: 1px solid #dee2e6;
            padding-top: 8px;
        }
        .page-title {
            text-align: center;
            font-size: 18pt;
            color: #2c3e50;
            margin-bottom: 1.5cm;
            font-weight: bold;
        }

        /* Activity Card */
        .activity-card {
            margin-bottom: 1cm;
            page-break-inside: avoid;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            background: #ffffff;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .activity-header {
            background-color: #34495e;
            color: white;
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .activity-name {
            font-size: 13pt;
            font-weight: bold;
        }
        .activity-status {
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .activity-status.completado { background-color: #27ae60; color: white; }
        .activity-status.programado { background-color: #f39c12; color: white; }
        .activity-status.pendiente { background-color: #c0392b; color: white; }

        .activity-body {
            padding: 15px;
        }
        .activity-grid {
            display: flex;
            gap: 15px;
        }
        .activity-details {
            width: 55%;
        }
        .activity-images {
            width: 45%;
        }
        .detail-item {
            margin-bottom: 12px;
        }
        .detail-item strong {
            display: block;
            font-size: 9pt;
            color: #7f8c8d;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        .detail-item p {
            margin: 0;
            font-size: 10pt;
            line-height: 1.4;
        }
        .activity-images-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .activity-image-container {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background-color: #f8f9fa;
            height: 120px;
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
        .no-images-placeholder {
            grid-column: span 2;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            color: #bdc3c7;
            border-radius: 4px;
            border: 1px dashed #dee2e6;
        }
        .no-activities {
            text-align: center;
            padding: 5cm 1cm;
            font-size: 14pt;
            color: #95a5a6;
        }
    </style>
</head>
<body>
    <!-- COVER PAGE -->
    <div class="cover-page">
        <div class="cover-header">
            <!-- You can place a logo here -->
            <!-- <img src="URL_DEL_LOGO" alt="Logo"> -->
        </div>
        <div class="cover-body">
            <div class="cover-title">REPORTE DE SEGUIMIENTO DE ACTIVIDADES</div>
            @if($project->uri)
                <div class="cover-image-container">
                    <img src="{{ $project->uri }}" alt="Imagen del Proyecto" class="cover-image">
                </div>
            @endif
            <div class="cover-project-name">{{ strtoupper($project->name) }}</div>
        </div>
        <div class="cover-footer">
            <div><strong>Seguimiento:</strong> {{ $tracking->title }}</div>
            <div><strong>Fecha:</strong> {{ $formattedDate }}</div>
            <div><strong>Semana:</strong> {{ $weekNumber }}</div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- ACTIVITY PAGES -->
    @if($activities->count() > 0)
        @php
            $activitiesPerPage = 2; // Adjusted for more detail and better spacing
            $chunks = $activities->chunk($activitiesPerPage);
        @endphp

        @foreach($chunks as $chunkIndex => $chunk)
            <div class="content-page">
                <div class="page-header">
                    <span>{{ $project->name }} | {{ $formattedDate }}</span>
                </div>

                <h1 class="page-title">{{ strtoupper($tracking->title) }}</h1>

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
                            <div class="activity-grid">
                                <div class="activity-details">
                                    <div class="detail-item">
                                        <strong>Descripción</strong>
                                        <p>{{ $activity->description ?? 'Sin descripción.' }}</p>
                                    </div>
                                    @if($activity->location)
                                    <div class="detail-item">
                                        <strong>Ubicación</strong>
                                        <p>{{ $activity->location }}</p>
                                    </div>
                                    @endif
                                    @if($activity->comments)
                                    <div class="detail-item">
                                        <strong>Comentarios</strong>
                                        <p>{{ $activity->comments }}</p>
                                    </div>
                                    @endif
                                    <div class="detail-item">
                                        <strong>Horas</strong>
                                        <p>{{ $activity->horas ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="activity-images">
                                    <div class="activity-images-grid">
                                        @if(count($imageUrls) > 0)
                                            @foreach(array_slice($imageUrls, 0, 4) as $imageUrl)
                                                <div class="activity-image-container">
                                                    <img src="{{ $imageUrl }}" alt="Imagen de Actividad" class="activity-image">
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="no-images-placeholder">
                                                <span>Sin Imágenes</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="page-footer">
                    <span>Página {{ $loop->iteration }} de {{ $chunks->count() }}</span>
                </div>
            </div>

            @if(!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @else
        <div class="content-page">
            <div class="page-header">
                <span>{{ $project->name }} | {{ $formattedDate }}</span>
            </div>
            <h1 class="page-title">{{ strtoupper($tracking->title) }}</h1>
            <div class="no-activities">
                No hay actividades registradas para este día.
            </div>
            <div class="page-footer">
                <span>Página 1 de 1</span>
            </div>
        </div>
    @endif
</body>
</html>
