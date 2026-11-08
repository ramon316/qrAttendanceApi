# Guía de Pruebas y Despliegue en Hosting Compartido
**Proyecto:** QR Attendance API (Laravel 12 + Livewire 3 + MySQL)

---

## 📋 Resumen de Optimizaciones Realizadas

1. **Rendimiento de Base de Datos y Consultas (N+1 Resuelto):**
   - En `UserAttendanceTable`: Eager loading (`with('event')`) y uso de `$row->event`, eliminando hasta 75 consultas SQL por página.
   - En `AttendanceController@myStats`: Agregaciones calculadas directamente en SQL (`COUNT(*)`, `AVG(distance_meters)`, `COUNT(DISTINCT event_id)`) en lugar de procesar colecciones en memoria PHP.
   - En `ConfirmationReport`: Agregaciones nativas con `selectRaw` en MySQL en lugar de cargar todos los modelos.
   - En `MyAttendances`: Uso directo de `$attendances->total()` del paginador, eliminando una consulta `count()` redundante.
   - En `Dashboard (Usuario)`: Estadísticas del mes unificadas en una consulta por rango de fechas (`BETWEEN`) compatible con índices.
   - En `Event (Modelo)`: Los accessors comprueban si las relaciones o contadores ya vienen cargados antes de ejecutar consultas.
   - **Nueva Migración de Índices:** `database/migrations/2026_09_29_000001_add_performance_indexes_to_tables.php` aplicada con índices compuestos en `attendances`, `events`, `pending_attendances` y `event_confirmations`.

2. **Check-In Unificado y Prevención de Concurrencia:**
   - Centralizado en `App\Services\AttendanceService` y `App\Services\AttendanceCheckInResult`.
   - Eliminada la duplicación de código entre la API REST (`AttendanceController`) y la interfaz Web (`AttendanceScanner`).
   - Prevención de condiciones de carrera: Captura automática de `QueryException` (código `23000` / entrada duplicada) retornando estado controlado `409 Conflict`.
   - Eliminada consulta `exists:events,qr_code` redundante en validación.

3. **Estandarización y Paginación de API:**
   - Paginación implementada en `myAttendances` (15 por página, máx. 100) y `eventAttendances` (25 por página, máx. 100), conservando el array `attendances` para retrocompatibilidad móvil y agregando metadata `pagination`.
   - Corrección de typos (`message` estandarizado, manteniendo fallback de `messsage`).
   - Unificación de expiración de tokens Sanctum a 2 horas en el registro de usuarios.
   - Estandarización de coordenadas GPS (`latitude` / `longitude` con alias `latitud` / `longitud`).

4. **Procesos por Lotes y Exportaciones:**
   - En `AttendanceMigrationService`: precarga de asistencias existentes para evitar consultas N+1 y consultas mediante `JOIN` SQL nativo.
   - En `routes/admin.php`: exportaciones de CSV procesadas en bloques de 500 registros (`chunk(500)`) con uso constante de memoria $O(1)$.

---

## 🧪 PARTE 1: Pruebas Locales Previas al Despliegue

### 1. Validación de Caché y Sintaxis
Ejecuta en tu terminal local:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
*Si alguno de estos comandos arroja error, indica algún detalle en rutas o sintaxis Blade que debe corregirse antes de subir.*
*(Para volver al modo desarrollo local: `php artisan optimize:clear`)*.

### 2. Pruebas de Endpoints de la API (Postman / Thunder Client)
1. **Login:** `POST /api/login` → Verificar respuesta exitosa y que el token en `personal_access_tokens` expire en 2 horas.
2. **Paginación de Asistencias:** `GET /api/attendances/my` (Bearer Token) → Verificar array `attendances` y objeto `pagination`. Probar con `?per_page=5`.
3. **Estadísticas SQL:** `GET /api/attendances/my/stats` → Verificar métricas calculadas rápidamente.
4. **Condición de Carrera en Check-in:**
   - `POST /api/attendances` con coordenadas válidas de un evento activo → Debe devolver `201 Created`.
   - Reenviar la misma petición inmediatamente → Debe responder `409 Conflict` con mensaje controlado *"Ya te encuentras registrado en este evento"*.

### 3. Prueba de Interfaz Web y Livewire
- Entrar a `/admin/users/attendances`, buscar un empleado y paginar la tabla.
- Probar exportación CSV en `/admin/events/{id}/export`.

---

## 🚀 PARTE 2: Pasos para Subir a Hosting Compartido (cPanel / Hostinger / Plesk)

### Paso 1: Compilar Assets Localmente (¡Obligatorio!)
El hosting compartido no suele tener Node.js o tiene límites de CPU que cancelan la compilación.
En tu computadora:
```bash
npm run build
```
Asegúrate de subir la carpeta `public/build` generada.

### Paso 2: Estructura Segura de Archivos (Protección del `.env`)
**No coloques todo el proyecto dentro de `public_html`.**

1. Sube todo el proyecto a un directorio fuera de la vista web pública:
   `/home/tu_usuario/qrAttendanceApi/`
2. Coloca únicamente el contenido de la carpeta `/public` dentro de la carpeta pública del hosting:
   `/home/tu_usuario/public_html/`
3. Si el DocumentRoot de tu dominio apunta a `public_html`, edita `/home/tu_usuario/public_html/index.php`:
   ```php
   require __DIR__.'/../qrAttendanceApi/vendor/autoload.php';
   $app = require_once __DIR__.'/../qrAttendanceApi/bootstrap/app.php';
   ```
*(Alternativa: Si tu panel permite cambiar el DocumentRoot del dominio, apúntalo directamente a `/home/tu_usuario/qrAttendanceApi/public`)*.

### Paso 3: Configurar `.env` de Producción
En el servidor, configura el archivo `.env`:
```env
APP_NAME="Sección8"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tudominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_bd_en_hosting
DB_USERNAME=usuario_bd_en_hosting
DB_PASSWORD=password_bd_en_hosting

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Paso 4: Permisos y Enlace Simbólico de Storage
1. Permisos de escritura para el servidor web:
   - `storage/` y sus subcarpetas (`logs`, `framework/views`, `framework/sessions`, `framework/cache`) → Permisos `775` (o `777` según el proveedor).
   - `bootstrap/cache/` → Permisos `775`.
2. Crear enlace simbólico para imágenes de perfil:
   - Si tienes terminal SSH:
     ```bash
     php artisan storage:link
     ```
   - Si no tienes SSH, crea temporalmente en `routes/web.php`:
     ```php
     Route::get('/symlink-setup', function () {
         \Artisan::call('storage:link');
         return 'Enlace simbólico creado';
     });
     ```
     Visítalo una vez en el navegador y luego bórralo.

### Paso 5: Migraciones y Optimización Final en Servidor
Si cuentas con SSH:
```bash
php artisan migrate --force
php artisan optimize
```
*Si no cuentas con SSH:*
- Puedes importar las tablas / ejecutar la migración desde phpMyAdmin.
- Desde el panel de administración web que ya tiene tu sistema en `/admin/system`, haz clic en **"Optimizar Aplicación"** (este botón ya ejecuta internamente `artisan optimize`).
