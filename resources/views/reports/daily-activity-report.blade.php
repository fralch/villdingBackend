<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Seguimiento - {{ $project->name }}</title>
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
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .page-break {
            page-break-after: always;
        }

        /* --- Portada --- */
        .cover-page {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100vh;
            background-color: #f8f9fa;
            padding: 2.5cm;
            box-sizing: border-box; /* Asegura que el padding esté incluido en el height */
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
            text-align: center;
        }
        .cover-title {
            font-size: 26pt; /* Ligeramente ajustado */
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .cover-project-name {
            font-size: 18pt; /* Ligeramente ajustado */
            color: #555;
            font-weight: 300;
            margin-bottom: 30px;
        }
        .cover-image-container {
            width: 100%;
            max-width: 16cm;
            margin: 0 auto 30px;
            border: 1px solid #dee2e6;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            border-radius: 8px;
        }
        .cover-image {
            max-width: 100%;
            max-height: 9cm;
            object-fit: contain;
        }
        .cover-footer {
            font-size: 11pt;
            color: #555;
            text-align: left;
            border-top: 2px solid #34495e;
            padding-top: 20px;
            margin-top: 30px;
        }
        .cover-footer div {
            margin-bottom: 8px;
        }

        /* --- Páginas de Contenido --- */
        .content-page {
            padding: 2.5cm 2cm; /* Más espacio arriba/abajo */
            position: relative;
            min-height: calc(100vh - 5cm); /* A4 height - padding */
            box-sizing: border-box;
        }
        .page-header {
            position: absolute;
            top: 1.5cm;
            left: 2cm;
            right: 2cm;
            text-align: right;
            font-size: 9pt;
            color: #7f8c8d;
        }
        .page-footer {
            position: absolute;
            bottom: 1.5cm;
            left: 2cm;
            right: 2cm;
            text-align: center;
            font-size: 9pt;
            color: #7f8c8d;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        .page-title {
            text-align: center;
            font-size: 16pt; /* Más cohesivo con el resto */
            color: #2c3e50;
            margin-bottom: 1.5cm;
            font-weight: bold;
            border-bottom: 3px solid #34495e;
            padding-bottom: 10px;
        }

        /* --- Tarjeta de Actividad (Rediseñada) --- */
        .activity-card {
            margin-bottom: 1cm;
            page-break-inside: avoid;
            border: none;
            border-radius: 8px;
            background: #ffffff;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .activity-header {
            background-color: #34495e;
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px 8px 0 0;
        }
        .activity-name {
            font-size: 14pt;
            font-weight: bold;
        }
        .activity-status {
            padding: 5px 15px;
            border-radius: 12px; /* Estilo "Pill" */
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            color: white;
        }
        /* Colores de estado (igual que el original) */
        .activity-status.completado { background-color: #27ae60; }
        .activity-status.programado { background-color: #f39c12; }
        .activity-status.pendiente { background-color: #c0392b; }

        .activity-body {
            padding: 20px;
        }
        
        /* Detalles de la actividad */
        .activity-details {
            width: 100%;
            margin-bottom: 20px; /* Espacio antes de la galería */
        }
        .detail-item {
            margin-bottom: 14px;
        }
        .detail-item strong {
            display: block;
            font-size: 9pt;
            color: #555; /* Más legible */
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-item p {
            margin: 0;
            font-size: 10pt;
            line-height: 1.5;
        }

        /* Galería de Imágenes (Destacada) */
        .activity-gallery {
            width: 100%;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .gallery-image-container {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background-color: #f8f9fa;
            height: 320px; /* Imágenes más grandes */
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .gallery-image {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Rellena el contenedor */
        }
        .no-images-placeholder {
            grid-column: span 2;
            height: 320px; /* Coincide con el contenedor */
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            color: #bdc3c7;
            border-radius: 8px;
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
    <div class="cover-page">
        <div class="cover-header">
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

    @if($activities->count() > 0)
        @php
            // Ajusta este número según cuánto espacio ocupe cada tarjeta.
            // Con imágenes más grandes, 2 por página es seguro.
            $activitiesPerPage = 2; 
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
                            
                            <div class="activity-gallery">
                                <div class="gallery-grid">
                                    @if(count($imageUrls) > 0)
                                        @foreach(array_slice($imageUrls, 0, 4) as $imageUrl)
                                            <div class="gallery-image-container">
                                                <img src="{{ $imageUrl }}" alt="Imagen de Actividad" class="gallery-image">
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