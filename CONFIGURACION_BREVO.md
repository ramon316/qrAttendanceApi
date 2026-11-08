# Guía de Configuración de Correo con Brevo (Sendinblue) en Laravel

Esta guía detalla los pasos para configurar el envío de correos transaccionales (recuperación de contraseña, notificaciones y avisos) en **qrAttendanceApi** utilizando el servicio de **Brevo**, superando el límite básico de 100 correos diarios del hosting compartido y evitando que los mensajes caigan en SPAM.

---

## 📌 Contexto y Beneficios

- **Límite de hosting actual:** ~100 correos por día en el buzón cPanel (`contacto@seccion8.org`).
- **Beneficio con Brevo:** **300 correos por día completamente GRATIS** (9,000 correos/mes).
- **Entregabilidad:** La reputación de IP de Brevo evita que correos a Gmail/Hotmail se bloqueen o vayan a la bandeja de Spam.
- **Tu cuenta Brevo:** No importa que la hayas creado con tu cuenta de Google/Gmail; el remitente visible siempre será `contacto@seccion8.org`.

---

## 🔄 ¿Cómo funciona la Recuperación de Contraseña en tu sistema?

El proyecto utiliza **Laravel 12 + Laravel Jetstream / Fortify**:

1. **Petición (`/forgot-password`):** El usuario solicita restablecer contraseña ingresando su correo.
2. **Generación de Token:** Laravel verifica la existencia del usuario, genera un token criptográfico único, almacena su hash en la tabla `password_reset_tokens` (vigencia de 60 minutos) y dispara una notificación.
3. **Envío del Correo:** Laravel se conecta al SMTP de Brevo y envía el correo con el enlace seguro (`/reset-password/{token}?email=...`).
4. **Cambio de Contraseña (`/reset-password`):** El usuario abre el enlace, ingresa su nueva contraseña. Fortify valida el token, actualiza la contraseña encriptada (`Hash::make`) y elimina el token de la base de datos.

---

## 🛠️ Paso a Paso para la Configuración

### Paso 1: Obtener credenciales SMTP en Brevo
1. Inicia sesión en [Brevo](https://app.brevo.com/).
2. Haz clic en el menú de tu perfil (esquina superior derecha) y selecciona **SMTP y API** (o ve a *Transaccional* > *Configuración* > *SMTP y API*).
3. En la pestaña **SMTP**, encontrarás:
   - **Servidor SMTP:** `smtp-relay.brevo.com`
   - **Puerto:** `587`
   - **Nombre de usuario / Login:** Tu correo registrado en Brevo (ej. tu correo de Google).
   - **Contraseña:** Haz clic en **Generar una nueva clave SMTP** (*Generate a new SMTP key*). Asigna un nombre descriptivo (ej. `Laravel Produccion`) y **copia la clave generada inmediatamente** (solo se muestra una vez).
   - **Formato de Clave SMTP:** `xsmtpsib-tu-clave-smtp-generada-en-brevo` (guárdala únicamente en tu archivo `.env`).

---

### Paso 2: Autorizar el correo remitente (`contacto@seccion8.org`)
Para que los correos salgan a nombre de `contacto@seccion8.org` y no a nombre de tu cuenta personal de Google:

1. En el menú superior derecho de Brevo, ve a **Remitentes, dominios e IPs** (*Senders, Domains & IPs*).
2. Entra a la sección **Remitentes** (*Senders*) y presiona **Añadir un remitente**.
3. Rellena los datos:
   - **Nombre del remitente:** `Sección 8` (o el nombre que prefieras).
   - **Correo del remitente:** `contacto@seccion8.org`.
4. Brevo enviará un correo de verificación a `contacto@seccion8.org`. Abre ese buzón y haz clic en el enlace/botón de confirmación para activarlo.

---

### Paso 3: Configurar el archivo `.env` en Laravel
Abre el archivo `.env` en la raíz del proyecto y ajusta la sección de correo con los siguientes valores:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=tu_correo_de_inicio_sesion_en_brevo@gmail.com
MAIL_PASSWORD=xsmtpsib-tu-clave-smtp-generada-en-el-paso-1
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="contacto@seccion8.org"
MAIL_FROM_NAME="Sección 8"
```

> **Nota Importante:** El valor de `MAIL_FROM_ADDRESS` debe coincidir exactamente con el remitente que verificaste en el Paso 2.

---

### Paso 4: Limpiar la caché de configuración
En la terminal (dentro de la carpeta del proyecto), ejecuta:

```bash
php artisan config:clear
```

---

### Paso 5: Prueba de Envío
Para verificar que la conexión funciona sin necesidad de pasar por la pantalla de login:

1. Abre la consola interactiva de Laravel:
   ```bash
   php artisan tinker
   ```

2. Ejecuta el comando de prueba (reemplaza por tu correo personal para verificar):
   ```php
   Mail::raw('¡Prueba exitosa! Brevo configurado correctamente en qrAttendanceApi.', function ($msg) {
       $msg->to('82b7be001@smtp-brevo.com')->subject('Prueba de Correo Brevo');
   });
   ```

3. Si todo está correcto, responderá con una confirmación de envío y recibirás el correo en cuestión de segundos.
4. Escribe `exit` para salir de tinker.

---

## ❓ Preguntas Frecuentes

- **¿Puedo seguir usando mi cuenta de Google para administrar Brevo?**
  Sí, no afecta en nada. La cuenta de Google solo sirve para iniciar sesión en Brevo; el correo que verán los usuarios en el campo "De:" será siempre `contacto@seccion8.org`.
- **¿Qué pasa si supero los 300 correos en un solo día?**
  Brevo pausará los envíos excedentes hasta el día siguiente. Si el proyecto crece y requiere más volumen, Brevo ofrece planes muy económicos o se puede pasar a Amazon SES.
