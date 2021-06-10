# INTÉGRATION DE BOOTSTRAP:
 - Dans public on créé un dossier assets avec un dossier css et un dossier js contenant les éléments bootstrap nécessaires.

# CONTROLLER HOME:
 - C'est le controller de notre page d'accueil. On utilise la commande `symfony console make:controller`.

# L'ENTITÉ USER:
 - On utilise la commande `symfony console make:user`.

# CRÉEATION DE LA DB:
 - Avec MySQL dans le fichier .env, on utilise la commande `symfony console doctrine:database:create`
 - Ensuite on fait les migrations avec `symfony console make:migration`, puis on les envoie à notre DB avec `symfony console doctrine:migrations:migrate`.
 > Commit et push sur notre repository GIT.
