# Projet N° 6 Développez de A à Z le site communautaire SnowTricks

Ce projet est un site communautaire dédié au partage de figures de snowboard.

## Prérequis

Projet créé avec le framework Symfony 6.2
PHP 8.1.10

## Installation

1 - Clonez ou telechargez le repository :
 <https://github.com/davidg-34/snowtricks.git>

2 - A la racine du répertoire, installer toutes les dépendances avec 'composer install'

3-1 - Modifiez le fichier .env avec vos parametres pour créer votre  base de données DATABASE_URL= "mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8&charset=utf8mb4"

3-2 - Créez la base de données et exécutez les migrations :

php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate

3-3 - Modifier le fichier .env pour configurer votre propre messagerie

## Utilisation

4 - Utilisation de bootstrap 5.

5 - Vous pouvez voir les premières tricks en lançant les fixtures :

php bin/console doctrine:fixtures:load

6 - Vous pouvez utiliser l'application soit en vous connectant sur le compte créé pour les fixtures : Identifiant : TestUser Mot de passe : pass_1234

 soit en créant votre propre compte.

6 - Lancer le serveur local : symfony serve ou symfony server:start -d

7 - Dans le navigateur entrez l'adresse localhost:8000(ou autre port libre) pour accéder à l'application.
