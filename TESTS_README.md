# Documentación de Tests

## Tests Creados

### Tests Unitarios - UserControllerTest

Ubicación: `tests/Unit/UserControllerTest.php`

#### Tests incluidos:

1. **test_user_code_generation_is_unique**
   - Verifica que cada usuario generado tenga un `user_code` único
   - Valida el formato del código: 1 letra mayúscula + 6 números (ej: A123456)

2. **test_user_can_be_created_with_all_fields**
   - Verifica la creación completa de un usuario con todos los campos
   - Valida que los datos se guarden correctamente en la base de datos
   - Verifica el hash de la contraseña

3. **test_user_email_must_be_unique**
   - Confirma que el campo email tiene restricción de unicidad
   - Espera una excepción al intentar crear usuarios con emails duplicados

4. **test_user_password_is_hashed**
   - Verifica que las contraseñas se almacenan hasheadas (no en texto plano)
   - Confirma que el hash puede ser verificado correctamente

5. **test_user_default_is_paid_user_is_zero**
   - Valida que el valor por defecto de `is_paid_user` es 0

6. **test_user_default_role_is_user**
   - Valida que el valor por defecto de `role` es 'user'

7. **test_user_can_have_projects**
   - Verifica la relación many-to-many con proyectos

### Tests de Feature - UserEndpointTest

Ubicación: `tests/Feature/UserEndpointTest.php`

#### Endpoints probados:

1. **POST /endpoint/user/create**
   - `test_user_create_endpoint_without_image`: Creación de usuario sin imagen
   - Verifica respuesta 201 y mensaje de éxito
   - Confirma que el usuario se guarda en la base de datos

2. **POST /endpoint/user/login**
   - `test_user_login_endpoint_successful`: Login exitoso con credenciales correctas
   - `test_user_login_endpoint_with_wrong_credentials`: Login fallido con contraseña incorrecta
   - `test_login_validation_without_email`: Validación cuando falta el email
   - `test_login_validation_without_password`: Validación cuando falta el password

3. **POST /endpoint/user/email_exists**
   - `test_email_exists_endpoint`: Verifica si un email existe
   - `test_email_does_not_exist_endpoint`: Verifica email no existente

4. **GET /endpoint/user/all**
   - `test_get_all_users_endpoint`: Obtiene todos los usuarios

5. **POST /endpoint/user/user_code**
   - `test_search_user_by_code_endpoint`: Busca usuario por código único

6. **POST /endpoint/user/update**
   - `test_update_user_endpoint`: Actualiza datos de usuario

## Factory Actualizado

Se actualizó `database/factories/UserFactory.php` para incluir todos los campos del modelo User:

```php
- name
- last_name
- email
- edad
- genero
- telefono
- password
- is_paid_user
- user_code (generado automáticamente)
- role
- uri
```

## Cómo Ejecutar los Tests

### Desde Windows (donde está la BD):

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar solo tests unitarios
php artisan test --testsuite=Unit

# Ejecutar solo tests de feature
php artisan test --testsuite=Feature

# Ejecutar un test específico
php artisan test --filter=UserControllerTest
php artisan test --filter=UserEndpointTest
```

### Requisitos:

- PHP debe tener las extensiones necesarias: dom, xml, mbstring, xmlwriter
- La base de datos debe estar configurada correctamente en el archivo `.env`
- Para tests, se recomienda usar una base de datos de prueba separada

### Configuración de Base de Datos de Prueba (Opcional):

En `phpunit.xml` puedes descomentar estas líneas para usar SQLite en memoria:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## Cobertura de Tests

### Funcionalidades Probadas ✓
- Creación de usuarios
- Login de usuarios
- Validación de emails únicos
- Verificación de contraseñas hasheadas
- Búsqueda de usuarios
- Actualización de usuarios
- Generación de códigos únicos
- Relación con proyectos

### Notas Importantes

1. Los tests usan `RefreshDatabase` para resetear la base de datos entre tests
2. Cada test es independiente y no afecta a los demás
3. Se utilizan factories para crear datos de prueba
4. Los tests de feature prueban endpoints completos (integración)
5. Los tests unitarios prueban lógica de negocio específica
