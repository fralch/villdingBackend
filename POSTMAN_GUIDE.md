# Guía de Pruebas con Postman - VilldingBackend

## Configuración Inicial

### 1. Servidor de Desarrollo
Asegúrate de que el servidor Laravel esté ejecutándose:
```bash
php artisan serve
```
El servidor estará disponible en: `http://localhost:8000`

### 2. Base de Datos con Datos de Prueba
Ejecuta las migraciones y seeders para tener datos de prueba:
```bash
php artisan migrate:fresh --seed
```

## Endpoints Disponibles para Pruebas

### 1. Generar Reporte Diario

**Endpoint:** `POST /endpoint/tracking/report/daily/{tracking_id}`

**URL Completa:** `http://localhost:8000/endpoint/tracking/report/daily/1`

**Método:** POST

**Headers:**
```
Accept: application/json
Content-Type: application/json
```

**Body (JSON):**
```json
{
    "date": "2025-10-20"
}
```

**Respuesta Esperada:**
- Status Code: 200 OK
- Content-Type: application/pdf
- Content-Disposition: attachment; filename="reporte_diario_Edificio Los Pinos_2025-10-20.pdf"
- El archivo PDF se descargará automáticamente

### 2. Datos de Prueba Disponibles

Después de ejecutar `php artisan migrate:fresh --seed`, tendrás:

**Proyectos:**
- ID: 1, Nombre: "Edificio Los Pinos"
- Tipo: "Edificación Urbana"
- Subtipo: "Edificio Multifamiliar"

**Tracking:**
- ID: 1, Proyecto: "Edificio Los Pinos"
- Status: activo (true)

**Actividades (para 2025-10-20):**
- "Revisión de planos estructurales" - 3 horas
- "Supervisión de cimentación" - 4 horas  
- "Control de calidad de materiales" - 2 horas

## Configuración en Postman

### Paso 1: Crear Nueva Request
1. Abre Postman
2. Crea una nueva request
3. Selecciona método **POST**
4. Ingresa la URL: `http://localhost:8000/endpoint/tracking/report/daily/1`

### Paso 2: Configurar Headers
En la pestaña "Headers", agrega:
- Key: `Accept`, Value: `application/json`
- Key: `Content-Type`, Value: `application/json`

### Paso 3: Configurar Body
1. Ve a la pestaña "Body"
2. Selecciona "raw"
3. Selecciona "JSON" en el dropdown
4. Ingresa el JSON:
```json
{
    "date": "2025-10-20"
}
```

### Paso 4: Enviar Request
1. Haz clic en "Send"
2. Deberías recibir una respuesta 200 OK
3. El PDF se descargará automáticamente

## Pruebas Adicionales

### Probar con Diferentes Fechas
Puedes cambiar la fecha en el body para probar diferentes días:
```json
{
    "date": "2025-10-21"
}
```

### Probar con Tracking ID Inexistente
Cambia el ID en la URL a uno que no existe (ej: `/daily/999`) para ver el manejo de errores.

### Verificar Estructura de Respuesta
Si cambias el header `Accept` a `text/html`, podrás ver la respuesta HTML en lugar de descargar el PDF.

## Solución de Problemas

### Error 404 - Not Found
- Verifica que la URL sea correcta: `/endpoint/tracking/report/daily/{id}`
- Asegúrate de usar método POST, no GET

### Error 500 - Internal Server Error
- Verifica que las migraciones y seeders se hayan ejecutado correctamente
- Revisa los logs de Laravel en `storage/logs/laravel.log`

### Error de Conexión
- Asegúrate de que `php artisan serve` esté ejecutándose
- Verifica que el puerto 8000 esté disponible

## Comandos Útiles

### Verificar Datos en Base de Datos
```bash
php artisan tinker
```
Luego ejecuta:
```php
\App\Models\Project::count();
\App\Models\Tracking::count();
\App\Models\Activity::count();
```

### Recrear Datos de Prueba
```bash
php artisan migrate:fresh --seed
```

### Ver Logs de Errores
```bash
tail -f storage/logs/laravel.log
```

## Notas Importantes

1. **Formato de Fecha:** Usa el formato `YYYY-MM-DD` (ej: "2025-10-20")
2. **Tracking ID:** Debe existir en la base de datos (usa ID 1 después del seeder)
3. **Método HTTP:** Debe ser POST, no GET
4. **Headers:** Son obligatorios para una respuesta correcta
5. **Descarga PDF:** El archivo se descarga automáticamente con el nombre generado por el sistema

## Estructura del PDF Generado

El reporte incluye:
- Información del proyecto
- Fecha del reporte
- Lista de actividades del día
- Total de horas trabajadas
- Detalles de cada actividad (nombre, horas, descripción)