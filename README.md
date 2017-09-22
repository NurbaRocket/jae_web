# README #

This README would normally document whatever steps are necessary to get your application up and running.

Шаблон по этой ссылке https://tangorn3x3.github.io/energy/home.html

### What is this repository for? ###

* Quick summary
* Version
* [Learn Markdown](https://bitbucket.org/tutorials/markdowndemo)

### How do I get set up? ###

* Summary of set up
* Configuration
* Installation
* Database configuration
* How to run tests
* Deployment instructions

### Contribution guidelines ###

* Writing tests
* Code review
* Other guidelines

### Who do I talk to? ###

* Repo owner or admin
* Other community or team contact

### Installation

composer update

# Установка бандлов для пространства имен Application
php bin/console sonata:easy-extends:generate SonataPageBundle --dest=src
php bin/console sonata:easy-extends:generate SonataMediaBundle --dest=src
php bin/console sonata:easy-extends:generate SonataNotificationBundle --dest=src

# Создание таблиц
php bin/console doctrine:schema:update

Предзаполнение данными
php bin/console sonata:page:create-site --enabled=true --name=localhost --locale=- --host=localhost --relativePath=/ --enabledFrom=now --enabledTo="+10 years" --default=true
php bin/console sonata:page:update-core-routes --site=all
php bin/console sonata:page:create-snapshots --site=all

php bin/console doctrine:fixtures:load