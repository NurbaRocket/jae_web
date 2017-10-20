# README #

This README would normally document whatever steps are necessary to get your application up and running.

Шаблон по этой ссылке https://tangorn3x3.github.io/energy/home.html

### Installation

# Установите зависимости и задайте настройки базы данных
    composer update

# Создание таблиц
    php bin/console doctrine:schema:update --force

# Предзаполнение данными
    php bin/console doctrine:fixtures:load