__NB__: Pour lancer le serveur symfony, utiliser la commande `symfony serve`.

# INTÉGRATION DE BOOTSTRAP:
 - Dans public on créé un dossier assets avec un dossier css et un dossier js contenant les éléments bootstrap nécessaires.

# CONTROLLER HOME:
 - C'est le controller de notre page d'accueil. On utilise la commande `symfony console make:controller`.
 - On renomme la route pour que ce soit notre page d'accueil à l'URL `"/"`.
 - On modifie la vue correspondante dans le dossier templates comme on le souhaite.

# L'ENTITÉ USER:
 - On utilise la commande `symfony console make:user`.

# CRÉATION DE LA DB:
 - Avec MySQL dans le fichier .env, on utilise la commande `symfony console doctrine:database:create`
 - Ensuite on fait les migrations avec `symfony console make:migration`, puis on les envoie à notre DB avec `symfony console doctrine:migrations:migrate`.
 > Commit et push sur notre repository GIT.

# CRÉATION D'UN FORMULAIRE D'INSCRIPTION:
 - On créé un autre controller avec `symfony console make:controller`. C'est le RegisterController.
 - On renomme la route pour que ce soit notre page de création de compte à l'URL `"/signup"`.
 - Ensuite on créé un formulaire à l'aide de la commande `symfony console make:form`. Celui-ci est lié à l'entité User. Dans src/Form/RegisterType.php, on supprime la ligne `->add('roles')` car on ne veut pas que l'utilisateur puisse choisir ses rôles lui-même.
 - On modifie le RegisterController afin de pouvoir créer le formulaire avec une nouvelle instance de l'entité User.
 - On modifie la vue correspondante dans le dossier templates comme on le souhaite.
 __P.S.__: On modifie le dossier `twig.yaml` afin d'intégrer le thème de Bootstrap 4 à nos formulaires.
 - On rajoute à l'entité User les propriétés `firstname` et `lastname` et on les rajoute aussi à notre builder dans RegisterType.
- On encode les mdp dans la DB à l'aide de `UserPasswordEncoderInterface` dans le builder.
 > Commit et push sur notre repository GIT.

# CRÉATION D'UN FORMULAIRE DE LOGIN:

