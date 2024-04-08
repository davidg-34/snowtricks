# Snow Tricks

## Installation

1. git clone `git clone https://www.github.com/.......`
2. cd snowtricks
3. composer install
4. cp .env.example .env
5. Edit database information in .env
6. Edit mailer information in .env
7. Create the database `php bin/console doctrine:database:create`
8. Init the db schema `php bin/console doctrine:migrations:migrate`
	Note that the migrations were created with `php bin/console make:migration`
9. Init the db data `php bin/console doctrine:fixtures:load`

## Project evolutions

- User avatars ? 