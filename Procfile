web: heroku-php-nginx -C docker/heroku-nginx.conf public/
release: php artisan migrate:fresh --seed --force && php artisan test --parallel && composer post-deployment
