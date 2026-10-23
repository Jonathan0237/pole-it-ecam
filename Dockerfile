FROM php:8.3-apache

# Extensions et modules Apache utiles
RUN a2enmod rewrite expires deflate headers \
    && sed -ri 's!/var/www/html!/var/www/app/public!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf \
    && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
    && echo 'ServerTokens Prod\nServerSignature Off' > /etc/apache2/conf-enabled/security-hardening.conf \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && printf 'expose_php = Off\nsession.use_strict_mode = 1\n' > "$PHP_INI_DIR/conf.d/app.ini"

WORKDIR /var/www/app
COPY src ./src
COPY public ./public
COPY bin ./bin
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN mkdir -p data && chown -R www-data:www-data data && chmod +x /usr/local/bin/entrypoint.sh

ENV DATA_DIR=/var/www/app/data \
    DEMO_MODE=true

# Render, Railway, etc. imposent le port via $PORT
ENV PORT=80
EXPOSE 80

HEALTHCHECK --interval=30s --timeout=3s CMD php -r 'exit(@file_get_contents("http://127.0.0.1:" . getenv("PORT") . "/") ? 0 : 1);'

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
