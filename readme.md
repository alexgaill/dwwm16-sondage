### Pour faire fonctionner le site:
## Utiliser la commande composer install
    - Pour installer les dépendances
## Utiliser la commande php bin/console doctrine:migrations:migrate
    - Pour ajouter les talbes à la BDD Sqlite
    - Si une erreur apparaît, utiliser la commande php bin/console doctrine:database:create
## Lancer le serveur symfony server:start