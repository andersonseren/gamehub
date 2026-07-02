# ============================================================
# Dockerfile para GameHub (PHP + PostgreSQL)
# ============================================================

# Usamos una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias para PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql

# Habilitar el módulo rewrite de Apache
RUN a2enmod rewrite

# Copiar todos los archivos del proyecto al contenedor
COPY . /var/www/html/

# --- Configuración de PHP para producción ---
# Creamos un archivo php.ini personalizado dentro del contenedor
RUN echo "display_errors = Off" > /usr/local/etc/php/conf.d/gamehub.ini \
    && echo "date.timezone = America/Bogota" >> /usr/local/etc/php/conf.d/gamehub.ini \
    && echo "extension=pdo_pgsql" >> /usr/local/etc/php/conf.d/gamehub.ini \
    && echo "extension=pgsql" >> /usr/local/etc/php/conf.d/gamehub.ini

# Exponer el puerto 80 (Apache por defecto)
EXPOSE 80
