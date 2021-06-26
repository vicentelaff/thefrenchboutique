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
 - À l'aide de la commande `symfony console make:auth` on créé la classe LoginFormAuthenticator et le SecurityController.
 - On modifie la vue correspondante dans le dossier templates comme on le souhaite.

# CRÉATION D'UN ACCOUNT CONTROLLER:
 - On veut gérer l'accès à certaines pages et le limiter seulement aux membres connectés.
### Fonctionnalité modifier mot de passe:
 - On créé un PasswordController et un nouveau formulaire ChangePasswordType (relié à User), que l'on intègre à la vue `password.html.twig`.
 > Commit et push sur notre repository GIT.

# INSTALLATION DE EASYADMIN:
 - On installe le bundle easyadmin de symfony avec la commande `composer require easycorp/easyadmin-bundle`
 - On génère notre dashboard avec `symfony console make:admin:dashboard`, puis ensuite on utilise la commande `symfony console make:admin:crud` pour mapper l'entité user dans easyadmin.
 > Commit et push sur notre repository GIT.

# NOUVELLE ENTITÉ CATEGORY:
 - Organiser les produits.
 - Après avoir créé cette nouvelle entité, on la mappe dans easyadmin avec `symfony console make:admin:crud` puis on modifie DashboardController pour la rajouter à notre back office.

# NOUVELLE ENTITÉ PRODUCTS:
 - Après avoir créé cette nouvelle entité, on la mappe dans easyadmin avec `symfony console make:admin:crud` puis on modifie DashboardController pour la rajouter à notre back office.
 - Dans ProductsCrudController, on rajoute le code suivant afin de pouvoir rajouter de nouveaux produits via le back office:
```php
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            SlugField::new('slug')
                ->setTargetFieldName('name'),
            ImageField::new('image')
                ->setBasePath('uploads/') // On créé dans public un dossier uploads.
                ->setUploadDir('public/uploads/')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            TextField::new('subtitle'),
            TextareaField::new('description'),
            MoneyField::new('price')
                ->setCurrency('EUR'),
            AssociationField::new('category')

        ];
    }
```
 - On rajoute quelques produits directement via le back office.
  
# NOUVEAU PRODUCTSCONTROLLER:
 - Après l'avoir créé, on rajoute à ProductsController le code suivant:
```php
class ProductsController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    #[Route('/products', name: 'products')]
    public function index(): Response
    {
        // On récupère les produits dans notre repository
        $products = $this->em->getRepository(Products::class)->findAll();

        return $this->render('products/index.html.twig', [
            'products' => $products,
        ]);
    }
}
```
 - Puis on les affiche dans la vue correspondante avec une boucle.
 - On veut une page unique pour chaque produit, donc on rajoute aussi la fonction suivante:
```php
    #[Route('/product/{slug}', name: 'product')]
    public function show($slug): Response
    {

        // On récupère le produit demandé par son slug
        $product = $this->em->getRepository(Products::class)->findOneBy(['slug' => $slug]);

        // Condition de redirectionnement
        if (!$product){
            return $this->redirectToRoute('products');
        }

        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }
```
 - Puis on créé une nouvelle vue dans templates/products appelée `show.html.twig` ou l'on affichera chaque produit individuellement quand l'utilisateur clique sur un produit.


# OPTION DE FILTRER LES PRODUITS:
 - On créé un nouveau dossier Classes dans src, dans lequel on créé `Search.php` pour gérer notre recherche.
 - Il nous faut donc créer dans le dossier Form un nouveau fichier `SearchType.php`.
 - Dans `SearchType.php`, on instancie donc la classe Search afin d'afficher à l'utilisateur (sur la vue index.html.twig dans template/products) un menu à gauche de l'écran lui permettant de réaliser une recherche ou de filtrer les produits selon leurs catégories.
 __P.S.:__ On a besoin de créer une nouvelle fonction dans ProductsRepository qui va nous permettre de gérer notre recherche (`findWithSearch()`).
 > Commit et push sur notre repository GIT.

# CRÉATION DU PANIER:


