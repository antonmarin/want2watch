###
# Sort goals by name asc
###
.DEFAULT_GOAL=help
CMD_DOCKER_EXEC=docker-compose exec app
CMD_DOCKER_RUN=docker run -e APP_ENV=dev -e APP_DEBUG=false -t --rm $(IMAGE_TAG)
COLOR_NONE="\\033[0m"
COLOR_BLUE="\\033[34m"
COLOR_CYAN="\\033[36m"
COLOR_GREEN="\\033[32m"
COLOR_YELLOW="\\033[33m"
COLOR_ORANGE="\\033[43m"
COLOR_RED="\\033[31m"
IMAGE_TAG="antonmarin/want2watch:dev"

build: #? build dev image
	docker build . -t $(IMAGE_TAG)

help: #? help me
	@printf "\e[34;01mAvailable targets\033[0m\n"
	@awk '/^@?[a-zA-Z\-_0-9]+:/ { \
		nb = sub( /^#\? /, "", helpMsg ); \
		if(nb == 0) { \
			helpMsg = $$0; \
			nb = sub( /^[^:]*:.* #\? /, "", helpMsg ); \
		} \
		if (nb) \
			printf "\033[1;31m%-" width "s\033[0m %s\n", $$1, helpMsg; \
	} \
	{ helpMsg = $$0 }' \
	$(MAKEFILE_LIST) | column -ts:

lint: lint-composer lint-cs lint-dockerfile lint-markdown lint-yaml #? prebuild validation
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
	docker run --rm -v $(PWD):/app -w /app sdesbure/yamllint sh -c "yamllint /app/*.yml /app/http/*.yml"

fix-cs: #? fix codestyle
	docker run --rm -v $(PWD):/data/ -w /data/ oskarstark/php-cs-fixer-ga:2.18.2 --diff --allow-risky=yes

validate-post-build: container composer-require sat phpunit #? postbuild validation
container:
	$(CMD_DOCKER_RUN) bin/console -n lint:container
composer-require:
	$(CMD_DOCKER_RUN) bin/composer-require-checker.phar check /app/composer.json
sat:
	$(CMD_DOCKER_RUN) vendor/bin/phpstan analyse

test: phpunit
phpunit:
	$(CMD_DOCKER_RUN) vendor/bin/phpunit --configuration phpunit.xml

spec-ui: #? UI to use specification
	docker run --name swaggerui-want2watch -d -p 8092:8080 -e URL=openapi.yml -v $(PWD)/http/specification.yml:/usr/share/nginx/html/openapi.yml swaggerapi/swagger-ui
	open http://localhost:8092
