FROM php:8.2-apache

# Installiere benötigte Erweiterungen
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Aktiviere mod_rewrite (falls nötig)
RUN a2enmod rewrite

# Setze den Arbeitsordner
WORKDIR /var/www/html
