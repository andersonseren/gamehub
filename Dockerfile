# ============================================================
# Dockerfile para GameHub (PHP + PostgreSQL)
# ============================================================

# Usamos una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias para PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql

# Habilitar el módulo rewrite de Apache (opcional, para URLs amigables)
RUN a2enmod rewrite

# Copiar todos los archivos del proyecto al contenedor
COPY . /var/www/html/

# Configurar el archivo php.ini para producción
RUN echo "display_errors = Off" > /usr/local/etc/php/conf.d/errors.ini \
    && echo "date.timezone = America/Bogota" >> /usr/local/etc/php/conf.d/timezone.ini

# Exponer el puerto 80 (Apache por defecto)
EXPOSE 80

# El comando por defecto de la imagen ya inicia Apache
# No necesitamos CMD ni ENTRYPOINT adicionales
