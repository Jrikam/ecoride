FROM php:8.2-apache

# Installe les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Active mod_rewrite
RUN a2enmod rewrite

# Copie les réglages personnalisés
COPY php.ini /usr/local/etc/php/
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf
# Change le répertoire par défaut si nécessaire
WORKDIR /var/www/html
