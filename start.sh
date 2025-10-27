#!/bin/bash
set -e

# Si Render provee $PORT, actualizar la configuración de Apache para escuchar en ese puerto
if [ -n "$PORT" ]; then
  echo "Configuring Apache to listen on port ${PORT}"
  sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf || true
  sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/g" /etc/apache2/sites-available/000-default.conf || true
fi

# Lanzar Apache en primer plano (imagen base php:*-apache incluye apache2-foreground)
exec apache2-foreground
