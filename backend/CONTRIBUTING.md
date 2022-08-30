# Contributing

## Getting started

How to begin contributing?

requirements:

- [docker](https://docs.docker.com/get-docker/)

- clone project using `git clone ...`
- `make sh`
- `composer install`

Optional:

- Disable `Symfony.Service.Missing service` inspection to turn off useless highlights
  as [services autoload](https://symfony.com/doc/5.4/service_container.html#service-container-services-load-example)
  used. DI container validation used in CI pipeline to ensure it is valid.

## Documenting

Are there any not automated documenting conventions?

- Follow [Keywords for use](https://www.ietf.org/rfc/rfc2119.txt) in conventions

### Specification

- Update specification before editing HTTP or message API

### PhpDoc

- Describe shape of arrays in form of used StaticAnalysisTool

## Code

Are there any not automated code conventions?

- Structure based on [Paul M. Jones research](https://github.com/php-pds/skeleton)
- You may run `make lint` before commit to run quick validations without building
- [PHPStan](https://phpstan.org/) used for Static Analysis Tool. You can use `make sat` to verify while dev.
- CodeStyle is controlled by PHP-CS-Fixer. Run `make lint-cs` to use.
  Also included in `make lint`
- Describe why @noinspection or @phpstan-ignore-next-line used
- Describe what to use instead @deprecated, prefer #Deprecated with replacement filled
- Tests naming MUST follow "should_{expected result}_when_{state under test}"
- Using [PHP Inspections plugin](https://plugins.jetbrains.com/plugin/7622-php-inspections-ea-extended-) is mandatory

### Pull request

How to integrate?

- After creating PullRequest approvers receive notification and review
- After approve successful PR author merge and deploy yourself or
  requesting competent persons

## Tools

What tools and utilities we use and what for?

- [Composer scripts](https://getcomposer.org/doc/articles/scripts.md) to
  build application code. Composer may require 4G and docker 5+ G of RAM
- [docker-compose](https://docs.docker.com/compose/) to deploy while dev

## Test

How to verify application is working properly?

- `make pipe` simulates full CI pipeline
- Visit [article in Martin Fowler blog](https://martinfowler.com/articles/microservice-testing/#conclusion-summary) to
  understand naming

## Releasing

What versioning schema used? How to package application with configs?

- We use [CalVer](https://calver.org/) ![CalVer](https://img.shields.io/badge/calver-YYYY.0M.MICRO-22bfda.svg)
- Application package in docker images and distribute through [Docker Hub](https://hub.docker.com/search?type=image)

## Deploy

How to add secrets to pipeline? How to deploy application?

- No production deployment now
- No secrets currently available

## Runtime

What servers run application?

- Visit `Dockerfile`s for info about running apps

## Monitoring

Where can you identify application failures? How to add monitoring of state index?

- We follow <https://prometheus.io/docs/practices/naming/>
- No monitoring now
- Logs begin by uppercase. Try to avoid using parameters in log message, place them in context
