.DEFAULT_GOAL=help
CMD_DOCKER_RUN=docker-compose exec app
COLOR_NONE="\\033[0m"
COLOR_BLUE="\\033[34m"
COLOR_CYAN="\\033[36m"
COLOR_GREEN="\\033[32m"
COLOR_YELLOW="\\033[33m"
COLOR_ORANGE="\\033[43m"
COLOR_RED="\\033[31m"

lint: lint-composer lint-cs lint-dockerfile lint-markdown lint-yaml
lint-composer:
	docker run --rm -iv $(PWD):/app/ composer:1.9 validate --no-interaction --no-check-publish --with-dependencies
	docker run --rm -v $(PWD):/app pplotka/local-php-security-checker-github-actions:v1.0.0
lint-cs:
	docker run --rm -v $(PWD):/data/ -w /data/ oskarstark/php-cs-fixer-ga:2.18.2 --dry-run --diff --allow-risky=yes
lint-dockerfile:
	docker run --rm -tv ${PWD}:/app hadolint/hadolint:v1.8.0 \
		hadolint \
		/app/Dockerfile
lint-markdown:
	docker run --rm -v "$(PWD):/app" -w /app markdownlint/markdownlint -i -g /app
lint-yaml:
	docker run --rm -v $(PWD):/app -w /app sdesbure/yamllint sh -c "yamllint /app/*.yml /app/resources/*.yml"

fix-cs:
	docker run --rm -v $(PWD):/data/ -w /data/ oskarstark/php-cs-fixer-ga:2.18.2 --diff --allow-risky=yes

test: container composer-require sat
container:
	$(CMD_DOCKER_RUN) bin/console -n lint:container
composer-require:
	$(CMD_DOCKER_RUN) bin/composer-require-checker.phar check /app/composer.json
sat:
	$(CMD_DOCKER_RUN) vendor/bin/phpstan analyse

spec-ui:
	docker run -p 8092:8080 -e URL=openapi.yml -v $(PWD)/http/specification.yml:/usr/share/nginx/html/openapi.yml swaggerapi/swagger-ui
