# Configuración del Bucket S3 para Villding

## Problema Resuelto

El error "Unable to write file at location" ocurría porque estábamos intentando establecer ACL a nivel de objeto (`'public'` parameter), pero el bucket tiene las ACL deshabilitadas (configuración moderna recomendada por AWS).

## Solución Implementada

**Cambio realizado:**
- ❌ Antes: `Storage::disk('s3')->put($newPath, $imageContent, 'public');`
- ✅ Ahora: `Storage::disk('s3')->put($newPath, $imageContent);`

La visibilidad pública se maneja a nivel de **Bucket Policy**, no a nivel de objeto individual.

## Configuración del Bucket S3

Tu bucket `villding` debe tener la siguiente configuración:

### 1. Block Public Access Settings

Desactiva "Block all public access" o al menos:
- ✅ Block public access to buckets and objects granted through new access control lists (ACLs)
- ✅ Block public access to buckets and objects granted through any access control lists (ACLs)
- ❌ Block public access to buckets and objects granted through new public bucket or access point policies
- ❌ Block public and cross-account access to buckets and objects through any public bucket or access point policies

### 2. Bucket Policy (Recomendado)

Para que las imágenes sean accesibles públicamente, agrega esta política al bucket:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "PublicReadGetObject",
            "Effect": "Allow",
            "Principal": "*",
            "Action": "s3:GetObject",
            "Resource": "arn:aws:s3:::villding/*"
        }
    ]
}
```

**¿Dónde configurar?**
1. Ve a AWS Console → S3
2. Selecciona el bucket `villding`
3. Pestaña "Permissions"
4. Sección "Bucket policy" → Editar
5. Pega la política JSON arriba
6. Guardar cambios

### 3. CORS Configuration (Si tu app frontend accede directamente)

Si tu aplicación frontend carga imágenes directamente desde S3, necesitas CORS:

```json
[
    {
        "AllowedHeaders": ["*"],
        "AllowedMethods": ["GET", "HEAD"],
        "AllowedOrigins": ["*"],
        "ExposeHeaders": []
    }
]
```

**¿Dónde configurar?**
1. AWS Console → S3 → Bucket `villding`
2. Pestaña "Permissions"
3. Sección "Cross-origin resource sharing (CORS)" → Editar
4. Pega la configuración JSON arriba
5. Guardar cambios

### 4. Permisos IAM del Usuario

Tu usuario IAM (con las credenciales en .env) necesita estos permisos:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "s3:GetObject",
                "s3:PutObject",
                "s3:DeleteObject",
                "s3:ListBucket"
            ],
            "Resource": [
                "arn:aws:s3:::villding",
                "arn:aws:s3:::villding/*"
            ]
        }
    ]
}
```

## Verificación

Después de configurar el bucket, verifica:

### Test 1: Subir archivo
```bash
php test_duplicate.php
```

Debe mostrar:
```
✓ Conexión a S3 exitosa
✓ Subido exitosamente
✓ Verificado en S3
```

### Test 2: Acceso público
Copia una URL de imagen de los logs (ejemplo):
```
https://villding.s3.us-east-2.amazonaws.com/activities/abc-123.jpg
```

Pégala en el navegador. Deberías ver la imagen sin errores.

Si ves "Access Denied", la Bucket Policy no está configurada correctamente.

## Troubleshooting

| Error | Causa | Solución |
|-------|-------|----------|
| "Unable to write file" | Falta permiso PutObject | Revisar IAM policy del usuario |
| "Access Denied" al ver imagen | Bucket no es público | Agregar/revisar Bucket Policy |
| "403 Forbidden" | Block Public Access activo | Ajustar configuración de acceso público |
| CORS error en frontend | CORS no configurado | Agregar CORS configuration |

## Notas Importantes

1. **Seguridad**: La política actual permite acceso público de lectura (`GetObject`) a TODOS los archivos del bucket. Esto está bien para imágenes públicas.

2. **ACL vs Bucket Policy**: AWS recomienda usar Bucket Policies en lugar de ACL. Por eso removimos el parámetro `'public'`.

3. **URLs**: Las URLs generadas por `Storage::disk('s3')->url($path)` siempre funcionarán si la Bucket Policy está configurada correctamente.

4. **Alternativa**: Si no quieres hacer el bucket público, puedes usar **Signed URLs** (URLs temporales con expiración), pero eso requiere cambios adicionales en el código.

## URLs de Referencia

- [AWS S3 Bucket Policies](https://docs.aws.amazon.com/AmazonS3/latest/userguide/bucket-policies.html)
- [Laravel S3 Driver](https://laravel.com/docs/10.x/filesystem#amazon-s3-compatible-filesystems)
- [AWS Block Public Access](https://docs.aws.amazon.com/AmazonS3/latest/userguide/access-control-block-public-access.html)
