# Guía de Configuración de AWS S3

## 🔴 Problema Detectado

El usuario IAM `app-storage-uploader` no tiene permisos para acceder al bucket `villding`.

Error: `AccessDenied - HTTP 403`

## ✅ Solución: Configurar Permisos IAM

### Opción 1: Política IAM (Recomendado)

1. Ve a la consola de AWS IAM: https://console.aws.amazon.com/iam/
2. En el menú lateral, haz clic en **Users**
3. Busca y haz clic en el usuario: `app-storage-uploader`
4. Ve a la pestaña **Permissions**
5. Haz clic en **Add permissions** > **Create inline policy**
6. Selecciona la pestaña **JSON** y pega esto:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "VilldingBucketAccess",
            "Effect": "Allow",
            "Action": [
                "s3:PutObject",
                "s3:GetObject",
                "s3:DeleteObject",
                "s3:PutObjectAcl"
            ],
            "Resource": "arn:aws:s3:::villding/*"
        },
        {
            "Sid": "VilldingBucketList",
            "Effect": "Allow",
            "Action": "s3:ListBucket",
            "Resource": "arn:aws:s3:::villding"
        }
    ]
}
```

7. Haz clic en **Review policy**
8. Dale un nombre: `VilldingS3Access`
9. Haz clic en **Create policy**

### Opción 2: Política del Bucket

Si prefieres configurar desde el bucket:

1. Ve a https://s3.console.aws.amazon.com/
2. Haz clic en el bucket `villding`
3. Ve a la pestaña **Permissions**
4. En **Bucket policy**, haz clic en **Edit**
5. Pega esto:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Sid": "AppStorageUploaderAccess",
            "Effect": "Allow",
            "Principal": {
                "AWS": "arn:aws:iam::641482779069:user/app-storage-uploader"
            },
            "Action": [
                "s3:PutObject",
                "s3:GetObject",
                "s3:DeleteObject",
                "s3:PutObjectAcl",
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

6. Haz clic en **Save changes**

## 🔍 Verificar el Bucket

También verifica que el bucket exista:

1. Ve a https://s3.console.aws.amazon.com/
2. Busca el bucket llamado `villding`
3. Si no existe, créalo:
   - Haz clic en **Create bucket**
   - Nombre: `villding`
   - Región: `US East (N. Virginia) us-east-1` (o la que prefieras)
   - Desmarca **Block all public access** si necesitas imágenes públicas
   - Haz clic en **Create bucket**

## 🧪 Después de Configurar los Permisos

Ejecuta este comando para verificar:

```bash
/mnt/c/xampp/php/php.exe test_simple_upload.php
```

Si sale `✓ PRUEBA EXITOSA`, entonces S3 está funcionando correctamente.

## 📝 Permisos Mínimos Requeridos

Para que tu aplicación funcione necesitas:

- ✅ `s3:PutObject` - Subir archivos
- ✅ `s3:GetObject` - Leer archivos
- ✅ `s3:DeleteObject` - Eliminar archivos
- ✅ `s3:PutObjectAcl` - Configurar ACL (para imágenes públicas)
- ⚠️ `s3:ListBucket` - Listar objetos (opcional pero útil)

## 🌐 Configuración de Acceso Público

Si necesitas que las imágenes sean accesibles públicamente:

1. En el bucket `villding`, ve a **Permissions**
2. En **Block public access (bucket settings)**, haz clic en **Edit**
3. Desmarca **Block all public access**
4. Guarda los cambios
5. Confirma escribiendo "confirm"

## 🔄 Región del Bucket

Si el bucket está en una región diferente a `us-east-1`, actualiza tu `.env`:

```env
AWS_DEFAULT_REGION=us-west-2  # O la región correcta
```

Para encontrar la región del bucket:
1. Ve a https://s3.console.aws.amazon.com/
2. Haz clic en el bucket `villding`
3. La región aparece en "AWS Region"

## 📊 Información de tu Cuenta

- **ARN del usuario**: `arn:aws:iam::641482779069:user/app-storage-uploader`
- **Nombre del usuario**: `app-storage-uploader`
- **ID de cuenta**: `641482779069`
- **Bucket**: `villding`
- **Región configurada**: `us-east-1`
