# 🏗️ ARCHITECTURE MARKETFLOW PRO

## 📁 Structure MVC

```
workspace/
├── app/
│   ├── controllers/     # Logique métier (PHP uniquement)
│   ├── models/          # Accès base de données
│   ├── views/           # Templates HTML/PHP
│   │   ├── layouts/     # Header, Footer
│   │   ├── home/        # Pages principales
│   │   ├── products/    # Catalogue produits
│   │   └── ...
│   └── helpers/         # Fonctions utilitaires
├── core/
│   ├── Controller.php   # Classe parente des contrôleurs
│   ├── Model.php        # Classe parente des modèles
│   ├── Router.php       # Système de routing
│   └── Database.php     # Connexion PostgreSQL
├── config/
│   ├── config.php       # Configuration générale
│   └── routes.php       # Définition des routes
├── public/
│   ├── css/             # Feuilles de style
│   ├── js/              # Scripts JavaScript
│   └── uploads/         # Fichiers uploadés
└── index.php            # Point d'entrée
```

## 🔄 Flux d'exécution

### 1. Point d'entrée : `index.php`
```php
// 1. Configuration PHP (erreurs, timezone, session)
// 2. Autoloader (chargement automatique des classes)
// 3. Chargement config.php
// 4. Chargement routes.php
```

### 2. Routing : `config/routes.php`
```php
$router->get('/', 'HomeController@index');
$router->dispatch(); // Analyse l'URL et appelle le contrôleur
```

### 3. Contrôleur : `app/controllers/HomeController.php`
```php
class HomeController extends Controller {
    public function index() {
        $products = $productModel->getPopular(4);
        return $this->render('home/index', ['products' => $products]);
    }
}
```

**⚠️ RÈGLE IMPORTANTE :**
- Les contrôleurs contiennent **UNIQUEMENT du code PHP**
- **JAMAIS de HTML** après la balise `?>`
- Le HTML est dans les vues (`app/views/`)

### 4. Méthode render() : `core/Controller.php`
```php
protected function render($view, $data = []) {
    extract($data); // Transforme ['products' => []] en $products
    
    require 'layouts/header.php';  // Inclut le header
    require $view . '.php';         // Inclut la vue demandée
    require 'layouts/footer.php';  // Inclut le footer
}
```

### 5. Vue : `app/views/home/index.php`
```php
<?php /* Vue uniquement HTML/PHP pour affichage */ ?>
<section class="hero">
    <h1><?= e($title) ?></h1>
    <?php foreach ($products as $product): ?>
        <div><?= e($product['title']) ?></div>
    <?php endforeach; ?>
</section>
```

## ✅ Bonnes Pratiques

### Contrôleurs
```php
// ✅ BON : Uniquement logique PHP
class HomeController extends Controller {
    public function index() {
        $data = $this->model->getData();
        return $this->render('home/index', $data);
    }
}

// ❌ MAUVAIS : HTML dans le contrôleur
class HomeController extends Controller {
    public function index() {
        return $this->render('home/index', $data);
    }
}
?>
<section>HTML ICI = BUG !</section>
```

### Vues
```php
// ✅ BON : Utiliser les fonctions helper pour sécurité
<?= e($product['title']) ?>  // Échappe le HTML
<?= formatPrice($price) ?>   // Formate le prix

// ❌ MAUVAIS : Affichage direct sans sécurité
<?= $product['title'] ?>  // Risque XSS !
```

### Modèles
```php
// ✅ BON : Requêtes préparées (protection SQL injection)
$stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);

// ❌ MAUVAIS : Concaténation SQL
$sql = "SELECT * FROM products WHERE id = $id"; // SQL injection !
```

## 🐛 Problèmes courants

### 1. Contenu affiché en double
**Cause :** HTML dans le contrôleur après `?>`
**Solution :** Supprimer tout HTML du contrôleur

### 2. Erreur "Headers already sent"
**Cause :** Espace ou HTML avant `<?php` ou après `?>`
**Solution :** Pas d'espace/HTML en dehors des balises PHP

### 3. Variables non définies dans les vues
**Cause :** Oubli de passer les données dans render()
**Solution :** `$this->render('vue', ['data' => $value])`

## 🔧 Maintenance

### Ajouter une nouvelle page

1. **Créer la route** dans `config/routes.php` :
```php
$router->get('/ma-page', 'MonController@maMethode');
```

2. **Créer le contrôleur** dans `app/controllers/MonController.php` :
```php
<?php
namespace App\Controllers;
use Core\Controller;

class MonController extends Controller {
    public function maMethode() {
        return $this->render('dossier/vue', [
            'title' => 'Titre de ma page'
        ]);
    }
}
```

3. **Créer la vue** dans `app/views/dossier/vue.php` :
```php
<?php /* Ma vue HTML */ ?>
<section>
    <h1><?= e($title) ?></h1>
</section>
```

### Déboguer une page

1. Vérifier que la route existe dans `config/routes.php`
2. Vérifier que le contrôleur existe et a le bon namespace
3. Vérifier que la vue existe à l'emplacement correct
4. Activer les erreurs : `ini_set('display_errors', 1)`

## 📚 Ressources

- **PostgreSQL** : Base de données relationnelle
- **PDO** : Extension PHP pour accès base de données
- **MVC** : Model-View-Controller (architecture)
- **Routing** : Système d'URL propres

## 🔒 Sécurité

### Protection XSS
```php
// Toujours échapper les données utilisateur
<?= e($user_input) ?>  // Utilise htmlspecialchars()
```

### Protection SQL Injection
```php
// Toujours utiliser des requêtes préparées
$stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
```

### Protection CSRF
```php
// Générer un token dans le formulaire
<input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

// Vérifier le token lors du traitement
if (!verifyCsrfToken($_POST['csrf_token'])) {
    die('Token CSRF invalide');
}
```

---

**Version :** 1.0  
**Dernière mise à jour :** 17 janvier 2025  
**Auteur :** Équipe MarketFlow Pro
