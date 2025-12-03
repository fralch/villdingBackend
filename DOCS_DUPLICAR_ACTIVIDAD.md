# Documentación: Endpoint para Duplicar Actividad

## Descripción General
Este endpoint permite duplicar una actividad existente, creando una nueva entrada en la base de datos con toda la información de la actividad original pero asignada a una nueva fecha. 

El sistema maneja automáticamente:
1. **Duplicación de datos**: Copia todos los campos relevantes (nombre, descripción, ubicación, horas, etc.).
2. **Clonación de imágenes**: Si la actividad tiene imágenes en S3 o almacenamiento local, se generan copias físicas independientes para la nueva actividad.
3. **Determinación de estado**: Calcula si el estado debe ser `pendiente` o `programado` basándose en la nueva fecha proporcionada.

## Especificación del Endpoint

**URL**: `/endpoint/activities/duplicate`
**Método**: `POST`
**Autenticación**: Requerida (Token Bearer o sesión activa, dependiendo del contexto)

### Headers
```http
Content-Type: application/json
Accept: application/json
```

### Parámetros del Body (JSON)

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| `activity_id` | Integer | Sí | El ID de la actividad original que se desea duplicar. |
| `new_date` | Date (Y-m-d) | Sí | La nueva fecha para la actividad duplicada. |

**Ejemplo de Body:**
```json
{
    "activity_id": 123,
    "new_date": "2025-06-15"
}
```

## Comportamiento del Sistema

### 1. Validación
- Se verifica que el `activity_id` exista en la tabla `activities`.
- Se valida que `new_date` sea una fecha válida.

### 2. Lógica de Estado
El estado de la nueva actividad se determina comparando `new_date` con la fecha actual (Zona horaria: America/Lima):
- **Si `new_date` > Hoy**: Estado = `programado`
- **Si `new_date` <= Hoy**: Estado = `pendiente`

### 3. Procesamiento de Imágenes
- El sistema recupera las referencias de imágenes de la actividad original.
- Para cada imagen:
  - Genera un nuevo nombre de archivo único (UUID).
  - Copia el archivo físico en el bucket S3 (o almacenamiento local).
  - Asocia la nueva ruta a la actividad duplicada.
- Esto asegura que eliminar una imagen en la copia no afecte a la original (y viceversa).

### 4. Respuesta

#### Éxito (200 OK)
Retorna la actividad creada y las rutas de las nuevas imágenes.

```json
{
    "message": "Actividad duplicada exitosamente.",
    "activity": {
        "id": 456,
        "project_id": 10,
        "tracking_id": 5,
        "name": "Instalación Eléctrica",
        "description": "Cableado fase 1",
        "location": "Piso 2",
        "horas": "4",
        "status": "programado",
        "icon": "electrical_services",
        "image": [
            "activities/550e8400-e29b-41d4-a716-446655440000.jpg"
        ],
        "comments": null,
        "fecha_creacion": "2025-06-15",
        "created_at": "2024-01-20T10:00:00.000000Z",
        "updated_at": "2024-01-20T10:00:00.000000Z"
    },
    "image_paths": [
        "https://bucket-url.s3.amazonaws.com/activities/550e8400-e29b-41d4-a716-446655440000.jpg"
    ]
}
```

#### Errores Comunes

**422 Unprocessable Entity** (Validación fallida)
```json
{
    "message": "The new date field is required.",
    "errors": {
        "new_date": ["The new date field is required."]
    }
}
```

**404 Not Found** (Actividad no encontrada)
```json
{
    "message": "No query results for model [App\\Models\\Activity] 999"
}
```

**500 Internal Server Error** (Error en servidor/S3)
```json
{
    "message": "Error al duplicar actividad",
    "error": "Error message details..."
}
```

## Implementación en Frontend (Sugerencia)

1. **Botón Duplicar**: Agregar una opción "Duplicar" en el menú de la tarjeta de actividad.
2. **Modal de Fecha**: Al hacer clic, mostrar un selector de fecha (Datepicker).
3. **Confirmación**: Al seleccionar la fecha y confirmar, llamar a este endpoint.
4. **Actualización UI**: 
   - Si la fecha seleccionada es la misma que se está viendo, recargar la lista o agregar la nueva actividad al DOM.
   - Si es otra fecha, mostrar un mensaje de éxito indicando que se creó para tal fecha.
