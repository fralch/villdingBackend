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

        /* Header superior derecho: Empresa | Ubicación */
        .top-right-header {
            text-align: right;
            font-size: 10pt;
            color: #7f8c8d;
            margin-bottom: 0.5cm;
        }

        /* Título: PROYECTO | SEGUIMIENTO */
        .project-tracking-title {
            text-align: left;
            font-size: 14pt;
            color: #0f3854;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0.3cm;
        }

        /* Fecha con línea divisoria */
        .report-date {
            text-align: left;
            font-size: 12pt;
            color: #0f3854;
            font-weight: bold;
            padding-bottom: 10px;
            border-bottom: 1px solid #95a5a6;
            margin-bottom: 0.8cm;
        }

        /* --- Tarjeta de Actividad --- */
        .activity-card {
            margin-bottom: 0.5cm;
            page-break-inside: auto;
        }

        /* Header de actividad con ícono */
        .activity-header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
            page-break-inside: avoid;
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
            margin-bottom: 4px;
        }
        .activity-status.completado { background-color: #4ec291; }
        .activity-status.programado { background-color: transparent; border: 1.5px solid #f4c724; color: #f4c724; }
        .activity-status.pendiente { background-color: #f4c724; }

        /* Título de la actividad */
        .activity-name {
            font-size: 13pt;
            font-weight: bold;
            color: #0f3854;
            margin-bottom: 10px;
        }

        /* Horario */
        .activity-time {
            font-size: 10pt;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        /* Descripción */
        .activity-description {
            font-size: 10.5pt;
            color: #2c3e50;
            line-height: 1.4;
            margin-bottom: 35px;
        }

        /* Galería de Imágenes */
        .activity-gallery {
            margin: 10px auto; /* Centrar el contenedor horizontalmente */
            width: 80%;
        }
        .gallery-row {
            width: 100%;
            margin-bottom: 15px;
            font-size: 0; /* Eliminar espacio en blanco entre elementos inline-block */
            text-align: left;
            page-break-inside: avoid;
        }
        .gallery-image-container {
            display: inline-block;
            width: 48%;
            vertical-align: top;
            font-size: 10pt; /* Restablecer tamaño de fuente */
        }
        .gallery-image-container:first-child {
            margin-right: 4%;
        }
        .gallery-image {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 4px;
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
    <div class="content-page">
        <!-- Header superior derecho: Empresa | Ubicación -->
        <div class="top-right-header">
            @if($project->company)
                {{ $project->company }}
                @if($project->location) | @endif
            @endif
            {{ $project->location ?? '' }}
        </div>

        <!-- Título principal: PROYECTO | SEGUIMIENTO -->
        <div class="project-tracking-title">
            {{ $project->name }} <span style="font-weight: normal;">| {{ $tracking->title }}</span>
        </div>

        <!-- Fecha con línea -->
        <div class="report-date">
            {{ $formattedDate }}
        </div>

        @if($activities->count() > 0)
            @foreach($activities as $activity)
                @php
                    $imageUrls = $activity->image_urls ?? [];
                    $statusClass = strtolower($activity->status);
                    $imageChunks = collect($imageUrls)->chunk(2);
                    $firstRow = $imageChunks->shift();
                @endphp

                <!-- Tarjeta de actividad -->
                <div class="activity-card">
                    <!-- Bloque inicial (Header + Primera Fila) - Indivisible -->
                    <div style="page-break-inside: avoid;">
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
                                @if(!empty($activity->description))
                                    <div class="activity-description">
                                        {!! nl2br(e($activity->description)) !!}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($firstRow)
                            <div class="activity-gallery" style="margin-bottom: 0;">
                                <div class="gallery-row">
                                    @foreach($firstRow as $imageUrl)
                                        <div class="gallery-image-container">
                                            <img src="{{ $imageUrl }}" alt="Imagen de Actividad" class="gallery-image">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Resto de filas -->
                    @if($imageChunks->isNotEmpty())
                        <div class="activity-gallery" style="margin-top: 0;">
                            @foreach($imageChunks as $rowImages)
                                <div class="gallery-row">
                                    @foreach($rowImages as $imageUrl)
                                        <div class="gallery-image-container">
                                            <img src="{{ $imageUrl }}" alt="Imagen de Actividad" class="gallery-image">
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="no-activities">
                No hay actividades registradas para este día.
            </div>
        @endif
    </div>
</body>
</html>