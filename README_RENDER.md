# Desplegar en Render.com (Docker)

Breve guía para desplegar esta aplicación PHP en Render.com usando Docker.

1) Subir el repositorio a GitHub (si no está ya)

2) Crear un servicio Web en Render
   - En Render, crea un nuevo "Web Service" y conecta el repositorio GitHub.
   - En "Environment", selecciona "Docker".
   - Render detectará `render.yaml` y usará el `Dockerfile` en la raíz.

3) Variables de entorno recomendadas
   - Añade las variables necesarias: DB_HOST, DB_USER, DB_PASS, DB_NAME, APP_ENV, MAIL_* (según uses).
   - `COMPOSER_ALLOW_SUPERUSER=1` ya está incluido en `render.yaml`.

4) Qué hace el Dockerfile
   - Usa la imagen `php:8.1-apache`.
   - Instala extensiones necesarias (pdo_mysql, zip) y Composer.
   - Instala Node.js y ejecuta `npx gulp build` para compilar assets en `public/build`.
   - Ajusta el DocumentRoot a `public/` y expone Apache usando un script `start.sh` que adapta el puerto con la variable `$PORT` que Render proporciona.

5) Notas y recomendaciones
   - Asegúrate de añadir las credenciales de la base de datos como Environment Variables en Render.
   - Si usas servicios de correo (PHPMailer), configura variables SMTP en el panel de Environment.
   - Si la construcción de assets falla por falta de dependencias nativas, ajusta el Dockerfile para instalar las librerías requeridas.
   - Por seguridad, configura `APP_ENV=production` y revisa `display_errors` en tu bootstrap (desactivado en producción).

6) Despliegue manual local (opcional)
   - Construir imagen: `docker build -t appsalon-php .`
   - Ejecutar localmente (ejemplo puerto 8080):
     ```bash
     docker run -e PORT=8080 -p 8080:8080 appsalon-php
     ```

Si quieres, puedo:
- adaptar el Dockerfile para instalar versiones exactas de Node/Composer que prefieras,
- añadir un `.dockerignore`,
- o crear una variante basada en Alpine si prefieres imágenes más pequeñas.
