# Gunakan image resmi PHP dengan Apache
FROM php:8.1-apache

# Copy semua file project ke dalam direktori web server
COPY . /var/www/html/

# Aktifkan modul Apache mod_rewrite (jika perlu routing)
RUN a2enmod rewrite

# Set permission agar file bisa diakses
RUN chown -R www-data:www-data /var/www/html

# Expose port default Apache
EXPOSE 80
