FROM yiisoftware/yii2-php:8.2-apache

COPY docker/apache/my-vhost.conf /etc/apache2/sites-enabled/000-default.conf

# Copy Yii2 project into /app
COPY . /app

# Permissions
RUN chown -R www-data:www-data /app