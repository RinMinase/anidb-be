web: heroku-php-nginx -C docker/heroku-nginx.conf public/
release: php artisan migrate:fresh --seed --force && php artisan test --parallel --processes=4 && composer post-deployment
