# NextEuropa Poetry

[![Build Status](https://travis-ci.org/ec-europa/nexteuropa_poetry.svg?branch=master)](https://travis-ci.org/ec-europa/nexteuropa_poetry)

## Development setup

Run:

```
$ composer install
```

This will download all development dependencies and build a Drupal 7 target site under `./build` and run
`./vendor/bin/robo project:setup` to setup proper symlink and produce necessary scaffolding files.

After that:

1. Copy `robo.yml.dist` into `robo.yml` and customise relevant parameters.
2. Run `./vendor/bin/robo project:install` to install the project having the Search API Europa Search module enabled.

To have a complete list of building options run:

```
$ ./vendor/bin/robo
```
