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
 - On créé une nouvelle classe `Cart.php` dans notre dossier Classes. Celle-ci permettra à l'utilisateur de créer un panier de produits.
 - Afin de rediriger l'utilisateur vers son panier lors de l'ajout d'un nouveau produit, on crée un nouveau CartController.
 - On a donc une nouvelle vue index.html.twig dans laquelle on renvoie le récapitulatif du panier de l'utilisateur.
 - Afin de pouvoir avoir multiples articles dans le panier, on rajoute le code suivant dans `Cart.php`:
```php
public function getFull()
  {
    $cartComplete = [];

    if ($this->get()) {
        foreach ($this->get() as $id => $quantity) {
          $product_object = $this->em->getRepository(Products::class)->findOneBy(['id' => $id]);
          
          // Preventing users from adding fake product IDs to their carts via the URL.
          if(!$product_object) {
            $this->delete($id);
            continue;
          }
          
          $cartComplete[] = [
              'product' => $product_object,
              'quantity' => $quantity
          ];
        }
    }

    return $cartComplete;
  }
```

 puis dans CartController:
```php
    #[Route('/cart', name: 'cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getFull()
        ]);
    }
```
 > Commit et push sur notre repository GIT.

# GESTION DES ADRESSES DE LIVRAISON:
 - On créé une nouvelle entité Address et un nouveau AccountAddressController à l'aide du terminal.
 - On supprime account_address dans templates pour plutôt créer une vue `address.html.twig` dans le dossier templates/account. Dans le même dossier, on crée aussi un fichier `form_address.html.twig` pour permettre à l'utilisateur d'ajouter/modifier une nouvelle adresse.
 - Dans `form_address.html.twig` on a besoin d'un formulaire AddressType.php, que l'on créé à l'aide du terminal avec `symfony console make:form` relié à l'entité Address.

# GESTION DES COMMANDES:
 - On créé une nouvelle entité Carrier qui représentera notre transporteur, et un nouveau CarrierCrudController à l'aide du terminal.
 - On créé aussi deux nouvelles entités Order (infos de base de notre commande - utilisateur lié, transporteur, date de livraison...) et OrderDetails (détails de la commande - panier, produits, quantités, total...).
 - **Tunnel d'achat:**
    1. Nouveau OrderController: récapitulatif et validation de la commande.
    2. Nouveau OrderType: formulaire permettant à l'utilisateur d'insérer ses données (adresse, etc.), ainsi que de choisir son transporteur.
    3. Paiement: Prochaine section!

> Commit et push sur notre repository GIT.

# PAIEMENT:
 - On utilise Stripe pour gérer les paiement des utilisateurs (stripe.com/docs/checkout/integration-builder).
 1. On installe la librairie Stripe PHP avec `composer.phar require stripe/stripe-php`. 
 2. On créé un nouveau `StripeController` contenant notre 'checkout session'.
 3. On créé un nouveau `OrderValidateController` contenant la logique pour la page "success" dans le cas ou l'utilisateur complète le paiement avec succès, ainsi qu'un nouveau `OrderCancelController` dans le cas ou il y a un échec de paiement.

    **P.S.:** La redirection vers la page (externe) de paiement stripe ne marche pas (je ne sais pas pourquoi), mais si on regarde dans le dashboard, quand on clique sur le bouton "checkout", ça rajoute bien une nouvelle entrée dans 'Orders'.
 - On créé un nouveau `AccountOrderController` qui permettra à l'utilisateur de gérer ses commandes.

# E-MAILING:
 - On utilise l'API MailJet pour gérer les e-mails de notre boutique. On créé donc un compte et un modèle d'e-mail (ID: 3050622) sur leur site. `composer.phar require mailjet/mailjet-apiv3-php`.
 - Ensuite on créé une nouvelle classe `Mail.php` pour gérer l'envoi de nos mails.
 - E-mails:
    1. Confirmation d'inscription: On modifie donc le RegisterController.
    2. Confirmation d'achat: On modifie donc l'Order    ValidateController.

> Commit et push sur notre repository GIT.
 