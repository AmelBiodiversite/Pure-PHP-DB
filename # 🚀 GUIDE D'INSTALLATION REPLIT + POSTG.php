# 🚀 GUIDE D'INSTALLATION REPLIT + POSTGRESQL

> Installation complète pas à pas pour tester MarketFlow Pro sur Replit

---

## ✅ ÉTAPE 1 : CRÉER LE REPL (5 min)

### Actions :

1. **Allez sur [replit.com](https://replit.com)**
2. **Connectez-vous** (ou créez un compte)
3. **Cliquez "Create Repl"**
4. **Configurez :**
   - Template : **"PHP Web Server"**
   - Title : `marketflow-pro`
   - Public ou Private : **Private** (recommandé)
5. **Cliquez "Create Repl"**

✅ **Attendez que Replit charge l'environnement**

---

## ✅ ÉTAPE 2 : ACTIVER LA BASE DE DONNÉES (2 min)

### Actions :

1. **Dans la sidebar gauche, cherchez l'icône** 🗄️ **"Database"**
2. **Cliquez dessus**
3. **Replit va initialiser PostgreSQL automatiquement**
4. **Attendez le message "Database created"**

✅ **Vous devriez voir "PostgreSQL is ready"**

---

## ✅ ÉTAPE 3 : CRÉER L'ARBORESCENCE (10 min)

### Dans Replit, créez TOUS ces dossiers :

**Méthode rapide - Shell :**

1. **Cliquez sur "Shell" en bas**
2. **Copiez-collez cette commande :**

```bash
mkdir -p config core app/controllers app/models app/views/layouts app/views/home app/views/auth app/views/products app/views/cart app/views/orders app/views/seller app/views/admin app/views/payment public/css public/js public/uploads/products/thumbnails public/uploads/products/files public/uploads/products/gallery public/uploads/avatars public/uploads/shops helpers database logs
```

3. **Appuyez sur Entrée**

✅ **Tous les dossiers sont créés !**

---

## ✅ ÉTAPE 4 : CRÉER LA BASE DE DONNÉES (5 min)

### Accéder à PostgreSQL :

1. **Dans la sidebar, cliquez sur "Database" 🗄️**
2. **Vous devriez voir un bouton "Connect" ou une interface**
3. **Cherchez "Run SQL" ou équivalent**

### Exécuter le schéma SQL :

1. **Copiez TOUT le contenu de l'artifact "PostgreSQL Schema"**
2. **Collez dans l'interface SQL de Replit**
3. **Cliquez "Run" ou "Execute"**

✅ **Vous devriez voir : "17 tables créées"**

**OU via Shell :**

```bash
# Dans le Shell Replit
psql $DATABASE_URL < database/marketflow.sql
```

(Après avoir créé le fichier database/marketflow.sql)

---

## ✅ ÉTAPE 5 : CRÉER LES FICHIERS ESSENTIELS (15 min)

### 5.1 - Fichier `index.php` (racine)

**Créez le fichier** : clic droit sur Files → Add file → `index.php`

**Copiez le contenu** depuis l'artifact précédent (celui que je vous avais donné)

### 5.2 - Fichier `config/config.php`

**Créez** : `config/config.php`

**Copiez** depuis l'artifact "Config PostgreSQL Replit" que je viens de créer

**MODIFIEZ** cette ligne :

```php
define('APP_URL', 'https://' . $_SERVER['HTTP_HOST']);
```

C'est déjà dynamique, ça devrait marcher !

### 5.3 - Fichier `config/database.php`

**Créez** : `config/database.php`

**Copiez** depuis l'artifact "Database Class PostgreSQL"

### 5.4 - Fichier `config/routes.php`

**Créez** : `config/routes.php`

**Copiez** depuis l'artifact "Routes Complètes" créé précédemment

### 5.5 - Fichier `.htaccess`

**Créez** : `.htaccess` (à la racine)

**Copiez** depuis l'artifact ".htaccess" créé précédemment

### 5.6 - Fichier `database/marketflow.sql`

**Créez** : `database/marketflow.sql`

**Copiez** depuis l'artifact "PostgreSQL Schema"

---

## ✅ ÉTAPE 6 : CRÉER LES FICHIERS CORE (10 min)

### 6.1 - `core/Router.php`

**Copiez** depuis les artifacts précédents

### 6.2 - `core/Controller.php`

**Copiez** depuis les artifacts précédents

### 6.3 - `core/Model.php`

**IMPORTANT** : Il faut adapter pour PostgreSQL !

**Créez** : `core/Model.php`

**Contenu adapté :**

```php
<?php
/**
 * MARKETFLOW PRO - MODÈLE DE BASE (POSTGRESQL)
 * Fichier : core/Model.php
 */

class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Récupérer tous les enregistrements
     */
    public function all($orderBy = null) {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Récupérer par ID
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Créer un enregistrement
     * POSTGRESQL : utilise RETURNING pour récupérer l'ID
     */
    public function create($data) {
        $fields = array_keys($data);
        $values = array_values($data);
        
        $fieldList = implode(', ', $fields);
        $placeholders = ':' . implode(', :', $fields);
        
        $sql = "INSERT INTO {$this->table} ({$fieldList}) 
                VALUES ({$placeholders}) 
                RETURNING {$this->primaryKey}";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result[$this->primaryKey] ?? null;
    }
    
    /**
     * Mettre à jour
     */
    public function update($id, $data) {
        $setParts = [];
        foreach (array_keys($data) as $field) {
            $setParts[] = "{$field} = :{$field}";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Supprimer
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Compter
     */
    public function count($where = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        
        if (!empty($where)) {
            $conditions = [];
            foreach (array_keys($where) as $field) {
                $conditions[] = "{$field} = :{$field}";
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($where);
        $result = $stmt->fetch();
        
        return $result['count'] ?? 0;
    }
    
    /**
     * Requête personnalisée
     */
    protected function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
```

---

## ✅ ÉTAPE 7 : CRÉER UN CONTROLEUR DE TEST (5 min)

### Fichier `app/controllers/HomeController.php`

**Créez** : `app/controllers/HomeController.php`

**Copiez** depuis les artifacts précédents

---

## ✅ ÉTAPE 8 : TESTER LA CONNEXION BDD (5 min)

### Créer un fichier de test :

**Créez** : `test-db.php` (à la racine)

```php
<?php
/**
 * TEST CONNEXION POSTGRESQL
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

echo "<h1>Test Connexion PostgreSQL</h1>";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "✅ <strong>Connexion réussie !</strong><br><br>";
    
    // Version PostgreSQL
    $version = $db->getVersion();
    echo "📊 Version : " . htmlspecialchars($version) . "<br><br>";
    
    // Lister les tables
    $tables = $db->getTables();
    echo "📋 <strong>Tables créées (" . count($tables) . ") :</strong><br>";
    echo "<ul>";
    foreach ($tables as $table) {
        $count = $db->countRows($table['table_name']);
        echo "<li>{$table['table_name']} - {$count} enregistrements</li>";
    }
    echo "</ul>";
    
    // Test utilisateurs
    $users = dbQuery("SELECT * FROM users");
    echo "<br>👥 <strong>Utilisateurs de test :</strong><br>";
    echo "<ul>";
    foreach ($users as $user) {
        echo "<li>{$user['full_name']} ({$user['email']}) - Role: {$user['role']}</li>";
    }
    echo "</ul>";
    
    echo "<br>✅ <strong>TOUT FONCTIONNE !</strong>";
    
} catch (Exception $e) {
    echo "❌ <strong>Erreur :</strong> " . htmlspecialchars($e->getMessage());
}
?>
```

### Tester :

1. **Cliquez sur "Run" en haut de Replit**
2. **Dans le navigateur intégré, allez sur** `/test-db.php`

✅ **Vous devriez voir les 3 utilisateurs de test !**

---

## ✅ ÉTAPE 9 : COPIER TOUS LES AUTRES FICHIERS (30 min)

Maintenant que la base fonctionne, copiez TOUS les artifacts dans les bons fichiers :

### Controllers :
- `app/controllers/AuthController.php`
- `app/controllers/ProductController.php`
- `app/controllers/SellerController.php`
- `app/controllers/CartController.php`
- `app/controllers/OrderController.php`
- `app/controllers/PaymentController.php`
- `app/controllers/AdminController.php`

### Models :
- `app/models/User.php`
- `app/models/Product.php`
- `app/models/Order.php`
- `app/models/Cart.php`

**⚠️ IMPORTANT** : Pour chaque Model, remplacez `LAST_INSERT_ID()` par PostgreSQL équivalent !

### Vues :
- Tous les fichiers `.php` dans `app/views/`

### Assets :
- `public/css/style.css`
- `public/js/app.js`

### Helpers :
- `helpers/functions.php`

---

## ✅ ÉTAPE 10 : LANCER L'APPLICATION ! (2 min)

1. **Cliquez sur "Run"** en haut
2. **Attendez le démarrage du serveur**
3. **Cliquez sur le navigateur intégré**

✅ **Vous devriez voir la page d'accueil !**

---

## 🧪 CHECKLIST DE TEST

- [ ] Page d'accueil charge
- [ ] `/login` fonctionne
- [ ] `/register` fonctionne
- [ ] Connexion avec `buyer@marketflow.com` / `admin123`
- [ ] Dashboard visible

---

## ❓ PROBLÈMES COURANTS

### Erreur "Class Database not found"

**Solution** : Vérifiez que `database.php` est bien dans `config/`

### Page blanche

**Solution** : Vérifiez les logs dans la console Replit

### Erreur PostgreSQL

**Solution** : Relancez le SQL dans Database

---

## 🎯 OÙ EN ÊTES-VOUS ?

**Dites-moi :**

✅ "ÉTAPE 1 OK" quand Repl créé
✅ "ÉTAPE 2 OK" quand Database activée
✅ "ÉTAPE 3 OK" quand dossiers créés
✅ etc.

**Je vous guide à chaque étape !** 🚀

**Commencez par l'ÉTAPE 1 et confirmez-moi !**