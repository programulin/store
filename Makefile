up:
	docker compose up -d

down:
	docker compose down

connect-php:
	docker compose exec php-fpm sh

test:
	docker compose exec php-fpm php bin/phpunit
