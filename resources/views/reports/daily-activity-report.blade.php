<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Diario - {{ $project->name }}</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #2c3e50;
            font-size: 10pt;
            margin: 0;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .page-break {
            page-break-after: always;
        }

        /* --- Páginas de Contenido --- */
        .content-page {
            /* Ajuste de padding para trabajar con margen de @page y evitar espacios extra */
            padding: 0.3cm 1.2cm 0.3cm 1.2cm;
            position: relative;
            box-sizing: border-box;
        }

        /* Header superior derecho */
        .page-header {
            text-align: right;
            font-size: 8.5pt;
            color: #95a5a6;
            margin-bottom: 0.3cm;
            font-weight: 300;
        }

        /* Título principal "REPORTE DIARIO" */
        .main-title {
            text-align: center;
            font-size: 24pt;
            color: #0f3854;
            font-weight: bold;
            margin-bottom: 0.3cm;
            letter-spacing: 1px;
        }

        /* Información del proyecto */
        .project-info {
            margin-bottom: 0.4cm;
        }
        .project-name {
            font-size: 13pt;
            font-weight: bold;
            color: #0f3854;
            margin-bottom: 5px;
        }
        .project-location,
        .project-company {
            font-size: 11pt;
            color: #2c3e50;
            margin-bottom: 3px;
            font-weight: 300;
        }

        /* Nombre del seguimiento con línea */
        .tracking-section {
            /* Reducido para eliminar espacio innecesario antes de la tarjeta */
            margin-bottom: 0.2cm;
        }
        .tracking-title {
            font-size: 12pt;
            font-weight: bold;
            color: #0f3854;
            padding-bottom: 6px;
            border-bottom: 2px solid #0f3854;
            margin-bottom: 0.3cm;
        }

        /* --- Tarjeta de Actividad --- */
        .activity-card {
            margin-bottom: 0.6cm;
        }

        /* Header de actividad con ícono */
        .activity-header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        /* Ícono a la izquierda */
        .activity-icon {
            flex-shrink: 0;
            width: 45px;
            height: 45px;
            margin-right: 15px;
            position: relative;
        }

        /* SVG del ícono de herramientas/construcción */
        .activity-icon svg {
            width: 100%;
            height: 100%;
        }

        /* Contenido de la actividad (título, badge, horas) */
        .activity-content {
            flex-grow: 1;
        }

        /* Badge de estado */
        .activity-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: capitalize;
            color: white;
            margin-bottom: 8px;
        }
        .activity-status.completado { background-color: #4ec291; }
        .activity-status.programado { background-color: transparent; border: 1.5px solid #f4c724; color: #f4c724; }
        .activity-status.pendiente { background-color: #f4c724; }

        /* Título de la actividad */
        .activity-name {
            font-size: 13pt;
            font-weight: bold;
            color: #0f3854;
            margin-bottom: 5px;
        }

        /* Horario */
        .activity-time {
            font-size: 10pt;
            color: #2c3e50;
            margin-bottom: 6px;
        }

        /* Descripción */
        .activity-description {
            font-size: 10.5pt;
            color: #2c3e50;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        /* Galería de Imágenes */
        .activity-gallery {
            /* Eliminar espacio no deseado antes de la primera imagen */
            margin-top: 6px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            width: 60%;
            margin-left: auto;
            margin-right: auto;
        }
        .gallery-image-container {
            text-align: center;
            /* Evita cortes dentro de cada imagen y mejora salto de página */
            page-break-inside: avoid;
        }
        .gallery-image {
            /* Mantener proporcionalidad sin distorsión y adaptarse al contenedor */
            max-width: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            display: inline-block;
        }

        .no-activities {
            text-align: center;
            padding: 3cm 1cm;
            font-size: 12pt;
            color: #95a5a6;
        }
    </style>
</head>
<body>
    @if($activities->count() > 0)
        @foreach($activities as $activityIndex => $activity)
            @php
                $imageUrls = $activity->image_urls ?? [];
                $statusClass = strtolower($activity->status);
                $isFirstPage = $activityIndex === 0;
            @endphp

            <div class="content-page">
                <!-- Header superior derecho -->
                <div class="page-header">
                    {{ $tracking->title }} | {{ $formattedDate }}
                </div>

                @if($isFirstPage)
                    <!-- Título principal solo en la primera página -->
                    <h1 class="main-title">REPORTE DIARIO</h1>

                    <!-- Información del proyecto -->
                    <div class="project-info">
                        <div class="project-name">{{ $project->name }}</div>
                        @if($project->location)
                            <div class="project-location">{{ $project->location }}</div>
                        @endif
                        @if($project->company)
                            <div class="project-company">{{ $project->company }}</div>
                        @endif
                    </div>

                    <!-- Nombre del seguimiento con línea -->
                    <div class="tracking-section">
                        <div class="tracking-title">{{ $tracking->title }}</div>
                    </div>
                @else
                    <!-- Solo el nombre del seguimiento en páginas subsecuentes -->
                    <div class="tracking-section">
                        <div class="tracking-title">{{ $tracking->title }}</div>
                    </div>
                @endif

                <!-- Tarjeta de actividad -->
                <div class="activity-card">
                    <div class="activity-header">

                        <!-- Contenido de la actividad -->
                        <div class="activity-content">
                            <!-- Badge de estado -->
                            <div class="activity-status {{ $statusClass }}">{{ ucfirst($activity->status) }}</div>

                            <!-- Título de la actividad -->
                            <div class="activity-name">{{ $activity->name }}</div>

                            <!-- Horario -->
                            @if($activity->horas != 0)
                                <div class="activity-time">{{ $activity->horas }}</div>
                            @endif

                            <!-- Descripción -->
                            <div class="activity-description">
                                {{ $activity->description ?? 'Sin descripción.' }}
                            </div>
                        </div>
                    </div>

                    <!-- Galería de imágenes -->
                    @if(count($imageUrls) > 0)
                        <div class="activity-gallery">
                            @foreach($imageUrls as $imageUrl)
                                <div class="gallery-image-container">
                                    <img src="{{ $imageUrl }}" alt="Imagen de Actividad" class="gallery-image" style="margin: 5px;">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            @if(!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @else
        <div class="content-page">
            <!-- Header superior derecho -->
            <div class="page-header">
                {{ $tracking->title }} | {{ $formattedDate }}
            </div>

            <!-- Título principal -->
            <h1 class="main-title">REPORTE DIARIO</h1>

            <!-- Información del proyecto -->
            <div class="project-info">
                <div class="project-name">{{ $project->name }}</div>
                @if($project->location)
                    <div class="project-location">{{ $project->location }}</div>
                @endif
                @if($project->company)
                    <div class="project-company">{{ $project->company }}</div>
                @endif
            </div>

            <!-- Nombre del seguimiento -->
            <div class="tracking-section">
                <div class="tracking-title">{{ $tracking->title }}</div>
            </div>

            <div class="no-activities">
                No hay actividades registradas para este día.
            </div>
        </div>
    @endif
</body>
</html>