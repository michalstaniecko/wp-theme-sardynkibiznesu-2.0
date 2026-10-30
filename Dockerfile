# Platform of theme 3.0 (spec #36): WP 7.1, PHP 8.4. An existing ./wordpress bind mount keeps
# its old core; empty it (or run `wp core update --version=7.1.3`) after changing this tag.
FROM wordpress:7.1.3-php8.4-apache

WORKDIR /var/www/html

# Install wp-cli
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp
