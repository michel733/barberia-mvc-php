FROM php:8.1-apache

# Instalación de dependencias del sistema y extensiones PHP
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        zip \
        curl \
        libzip-dev \
    && docker-php-ext-install pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Cambiar el DocumentRoot a la carpeta public
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf

# Copiar Composer desde la imagen oficial de Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Instalar Node.js (Node 18.x) para construir assets
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get update \
    && apt-get install -y nodejs \
    && npm --version || true

WORKDIR /var/www/html

# Copiar archivos de Composer y ejecutar instalación de dependencias PHP
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist || true

# Copiar package.json y gulpfile para construir assets
COPY package.json package-lock.json gulpfile.js ./
COPY src/ ./src/
RUN npm ci --silent && npx gulp build --silent || true

# Copiar el resto de la aplicación
COPY . .

# Script de inicio para ajustar el puerto que provee Render ($PORT)
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]
