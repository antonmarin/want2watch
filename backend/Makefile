###
# Default variables
###
.DEFAULT_GOAL=help
COLOR_NONE="\\033[0m"
COLOR_BLUE="\\033[34m"
COLOR_CYAN="\\033[36m"
COLOR_GREEN="\\033[32m"
COLOR_YELLOW="\\033[33m"
COLOR_ORANGE="\\033[43m"
COLOR_RED="\\033[31m"
###
# Goals variables. Sort by name asc
###
CMD_DOCKER_EXEC=docker-compose exec app
CMD_DOCKER_RUN?=docker run -e APP_ENV=dev -e APP_DEBUG=false -e PHP_INI_MEMORY_LIMIT=256M -t --rm
IMAGE_TAG?="antonmarin/want2watch:dev"

###
# Goals shortcuts
###
lint: lint-composer lint-cs lint-dockerfile lint-markdown lint-yaml lint-spec #? prebuild validation
pipe: lint build validate-post-build #? behavior similar to CI pipeline
validate-post-build: container composer-require sat phpunit #? postbuild validation

###
# Goals. Sort by name asc
###
build: #? build dev image
	docker build . -t $(IMAGE_TAG)
composer-require:
	$(CMD_DOCKER_RUN) $(IMAGE_TAG) bin/composer-require-checker.phar check /app/composer.json
container:
	$(CMD_DOCKER_RUN) $(IMAGE_TAG) bin/console -n lint:container
fix-cs: #? fix codestyle
	docker run --rm -v $(PWD):/data/ -w /data/ oskarstark/php-cs-fixer-ga:2.18.2 --diff --allow-risky=yes
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
lint-spec:
	docker run --rm -v $(PWD):/app jeanberu/swagger-cli swagger-cli validate /app/http/specification.yml
lint-yaml:
	docker run --rm -v $(PWD):/app -w /app sdesbure/yamllint sh -c "yamllint /app/*.yml /app/http/*.yml"
phpunit:
	$(CMD_DOCKER_RUN) $(IMAGE_TAG) vendor/bin/phpunit --configuration phpunit.xml
sat:
	$(CMD_DOCKER_RUN) $(IMAGE_TAG) vendor/bin/phpstan analyse
spec-ui: #? UI to use specification
	docker run --name swaggerui-want2watch -d -p 8092:8080 -e URL=openapi.yml -v $(PWD)/http/specification.yml:/usr/share/nginx/html/openapi.yml swaggerapi/swagger-ui
	open http://localhost:8092
