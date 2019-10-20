FROM php:5-apache

RUN a2enmod rewrite

COPY ./ /var/www/html
