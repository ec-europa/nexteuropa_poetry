# NextEuropa Poetry

[![Build Status](https://travis-ci.org/ec-europa/nexteuropa_poetry.svg?branch=master)](https://travis-ci.org/ec-europa/nexteuropa_poetry)

## Development setup

1. Copy `robo.yml.dist` into `robo.yml` and customise relevant parameters.
2. Copy `behat.yml.dist` into `behat.yml` and customise relevant parameters.
3. Run `composer install` 
4. Run `./vendor/bin/robo project:setup`
5. Run `./vendor/bin/robo project:install`

Project will be available at `./build`.

## Tests

Run Behat tests with `./vendor/bin/behat`.
