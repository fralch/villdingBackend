<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Diario - {{ $project->name }}</title>
    <style>
        @page {
            size: A4;
            margin: 1.2cm 1.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #2c3e50;
            font-size: 10pt;
            margin: 0;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .page-break {
            page-break-after: always;
        }

        /* --- Páginas de Contenido --- */
        .content-page {
            padding: 0.2cm 0 0.4cm 0;
            position: relative;
            box-sizing: border-box;
        }

        /* Header superior derecho */
        .page-header {
            text-align: right;
            font-size: 8.5pt;
            color: #95a5a6;
            margin-bottom: 0.5cm;
            font-weight: 300;
        }

        /* Título principal "REPORTE DIARIO" */
        .main-title {
            text-align: center;
            font-size: 24pt;
            color: #0f3854;
            font-weight: bold;
            margin-bottom: 0.6cm;
            letter-spacing: 1px;
        }

        /* Información del proyecto */
        .project-info {
            margin-bottom: 0.7cm;
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
            margin-bottom: 1.5cm;
        }
        .tracking-title {
            font-size: 12pt;
            font-weight: bold;
            color: #0f3854;
            padding-bottom: 8px;
            border-bottom: 2px solid #0f3854;
            margin-bottom: 0.3cm;
        }

        /* --- Tarjeta de Actividad --- */
        .activity-card {
            margin-bottom: 1cm;
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
            font-size: 8pt;
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
            font-size: 12pt;
            font-weight: bold;
            color: #0f3854;
            margin-bottom: 5px;
        }

        /* Horario */
        .activity-time {
            font-size: 9pt;
            color: #2c3e50;
            margin-bottom: 12px;
        }

        /* Descripción */
        .activity-description {
            font-size: 9.5pt;
            color: #2c3e50;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        /* Galería de Imágenes */
        .activity-gallery {
            margin-top: 16px;
        }
        .gallery-image-container {
            margin-bottom: 12px;
            text-align: center;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .gallery-image {
            display: block;
            width: 100%;
            max-width: 100%;
            height: auto;
            max-height: 20cm;
            object-fit: contain;
            page-break-inside: avoid;
            break-inside: avoid;
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
                        <!-- Ícono de herramientas/construcción -->
                        <div class="activity-icon">
                            <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="32" cy="32" r="30" fill="#0f3854" opacity="0.15"/>
                                <path d="M24 20L28 24L24 28M32 20L36 24L32 28M40 20L44 24L40 28M20 32H44M20 40H44" stroke="#0f3854" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <!-- Contenido de la actividad -->
                        <div class="activity-content">
                            <!-- Badge de estado -->
                            <div class="activity-status {{ $statusClass }}">{{ ucfirst($activity->status) }}</div>

                            <!-- Título de la actividad -->
                            <div class="activity-name">{{ $activity->name }}</div>

                            <!-- Horario -->
                            <div class="activity-time">{{ $activity->horas ?? 'N/A' }}</div>

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
                                    <img src="{{ $imageUrl }}" alt="Imagen de Actividad" class="gallery-image">
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