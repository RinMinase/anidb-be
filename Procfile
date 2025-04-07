web: heroku-php-nginx -C docker/heroku-nginx.conf public/
release: php artisan migrate:fresh --seed && composer post-deployment
scheduler: php artisan schedule:work
queue: php artisan queue:work
