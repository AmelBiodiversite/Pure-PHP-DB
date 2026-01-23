<?php
/**
 * ================================================
 * MARKETFLOW PRO - MODÈLE WISHLIST (FAVORIS)
 * ================================================
 * 
 * Fichier : app/models/Wishlist.php
 * Version : 1.0
 * Date : 16 janvier 2025
 * 
 * DESCRIPTION :
 * Gère la liste de favoris (wishlist) des utilisateurs.
 * Permet d'ajouter, supprimer et récupérer les produits favoris.
 * 
 * FONCTIONNALITÉS :
 * ✅ Ajouter un produit aux favoris
 * ✅ Supprimer un produit des favoris
 * ✅ Vérifier si un produit est en favoris
 * ✅ Récupérer tous les favoris d'un utilisateur
 * ✅ Compter le nombre de favoris
 * ✅ Supprimer tous les favoris d'un utilisateur
 * 
 * UTILISATION :
 * $wishlist = new Wishlist();
 * $wishlist->add($userId, $productId);
 * $favorites = $wishlist->getUserWishlist($userId);
 * 
 * ================================================
 */

namespace App\Models;

use PDO;
use Core\Database;

class Wishlist {
    /**
     * Connexion à la base de données PostgreSQL
     * @var PDO
     */
    private $db;

    /**
     * ============================================
     * CONSTRUCTEUR
     * ============================================
     * Initialise la connexion à la base de données
     */
    public function __construct() {
        $this->db = Database::getInstance()->getPdo();
    }

    /**
     * ============================================
     * AJOUTER UN PRODUIT AUX FAVORIS
     * ============================================
     * 
     * Ajoute un produit à la wishlist d'un utilisateur.
     * Ignore silencieusement si le produit est déjà en favoris (UNIQUE constraint).
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $productId ID du produit à ajouter
     * @return bool TRUE si ajouté avec succès, FALSE sinon
     * 
     * EXEMPLE :
     * $wishlist = new Wishlist();
     * if ($wishlist->add(5, 42)) {
     *     echo "Produit ajouté aux favoris !";
     * }
     */
    public function add($userId, $productId) {
        try {
            // Requête INSERT avec ON CONFLICT pour éviter les doublons
            // Si le couple (user_id, product_id) existe déjà, ne rien faire
            $sql = "INSERT INTO wishlist (user_id, product_id) 
                    VALUES (:user_id, :product_id)
                    ON CONFLICT (user_id, product_id) DO NOTHING
                    RETURNING id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            
            // Si RETURNING renvoie un ID, l'insertion a réussi
            // Si pas d'ID, le produit était déjà en favoris (ce qui est OK)
            return true;
            
        } catch (PDOException $e) {
            // Log l'erreur pour debugging
            error_log("Erreur ajout wishlist: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ============================================
     * SUPPRIMER UN PRODUIT DES FAVORIS
     * ============================================
     * 
     * Retire un produit de la wishlist d'un utilisateur.
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $productId ID du produit à retirer
     * @return bool TRUE si supprimé avec succès, FALSE sinon
     * 
     * EXEMPLE :
     * $wishlist = new Wishlist();
     * if ($wishlist->remove(5, 42)) {
     *     echo "Produit retiré des favoris !";
     * }
     */
    public function remove($userId, $productId) {
        try {
            $sql = "DELETE FROM wishlist 
                    WHERE user_id = :user_id AND product_id = :product_id";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            
            return $result;
            
        } catch (PDOException $e) {
            error_log("Erreur suppression wishlist: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ============================================
     * VÉRIFIER SI UN PRODUIT EST EN FAVORIS
     * ============================================
     * 
     * Vérifie si un produit spécifique est dans la wishlist d'un utilisateur.
     * Utile pour afficher/cacher le cœur plein sur les boutons.
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $productId ID du produit à vérifier
     * @return bool TRUE si en favoris, FALSE sinon
     * 
     * EXEMPLE :
     * $wishlist = new Wishlist();
     * $isInWishlist = $wishlist->exists(5, 42);
     * // Affiche un cœur plein si TRUE, vide si FALSE
     */
    public function exists($userId, $productId) {
        try {
            // Utilisation de EXISTS pour une requête ultra-rapide
            $sql = "SELECT EXISTS(
                        SELECT 1 FROM wishlist 
                        WHERE user_id = :user_id AND product_id = :product_id
                    )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':product_id' => $productId
            ]);
            
            // fetchColumn() renvoie TRUE ou FALSE directement
            return $stmt->fetchColumn() === 't'; // PostgreSQL renvoie 't' ou 'f'
            
        } catch (PDOException $e) {
            error_log("Erreur vérification wishlist: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ============================================
     * RÉCUPÉRER TOUS LES FAVORIS D'UN UTILISATEUR
     * ============================================
     * 
     * Récupère la liste complète des produits favoris d'un utilisateur
     * avec toutes les informations nécessaires (titre, prix, image, vendeur).
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $limit Nombre max de résultats (optionnel)
     * @return array Tableau de produits avec leurs infos complètes
     * 
     * STRUCTURE DU RETOUR :
     * [
     *   [
     *     'wishlist_id' => 1,
     *     'product_id' => 42,
     *     'title' => 'Template Bootstrap',
     *     'slug' => 'template-bootstrap',
     *     'price' => 29.99,
     *     'thumbnail_url' => '/uploads/...',
     *     'seller_name' => 'John Doe',
     *     'seller_username' => 'johndoe',
     *     'rating_average' => 4.5,
     *     'added_at' => '2025-01-16 12:30:00'
     *   ],
     *   ...
     * ]
     * 
     * EXEMPLE :
     * $wishlist = new Wishlist();
     * $favorites = $wishlist->getUserWishlist(5);
     * foreach ($favorites as $product) {
     *     echo $product['title'] . " - " . $product['price'] . "€";
     * }
     */
    public function getUserWishlist($userId, $limit = null) {
        try {
            // Jointure avec products et users pour récupérer toutes les infos
            $sql = "SELECT 
                        w.id as wishlist_id,
                        w.created_at as added_at,
                        p.id as product_id,
                        p.title,
                        p.slug,
                        p.price,
                        p.original_price,
                        p.thumbnail_url,
                        p.rating_average,
                        p.rating_count,
                        p.sales,
                        u.username as seller_username,
                        u.full_name as seller_name,
                        u.shop_name as seller_shop_name
                    FROM wishlist w
                    INNER JOIN products p ON w.product_id = p.id
                    INNER JOIN users u ON p.seller_id = u.id
                    WHERE w.user_id = :user_id
                    AND p.status = 'approved'
                    ORDER BY w.created_at DESC";
            
            // Ajouter une limite si spécifiée
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            
            if ($limit !== null) {
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Erreur récupération wishlist: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ============================================
     * COMPTER LE NOMBRE DE FAVORIS
     * ============================================
     * 
     * Compte le nombre total de produits dans la wishlist d'un utilisateur.
     * Utilisé pour afficher le compteur dans le header.
     * 
     * @param int $userId ID de l'utilisateur
     * @return int Nombre de produits en favoris
     * 
     * EXEMPLE :
     * $wishlist = new Wishlist();
     * $count = $wishlist->getCount(5);
     * echo "Vous avez {$count} produits en favoris";
     */
    public function getCount($userId) {
        try {
            $sql = "SELECT COUNT(*) 
                    FROM wishlist 
                    WHERE user_id = :user_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            
            return (int) $stmt->fetchColumn();
            
        } catch (PDOException $e) {
            error_log("Erreur comptage wishlist: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * ============================================
     * SUPPRIMER TOUS LES FAVORIS D'UN UTILISATEUR
     * ============================================
     * 
     * Vide complètement la wishlist d'un utilisateur.
     * Utile pour un bouton "Vider mes favoris" ou lors de la suppression du compte.
     * 
     * @param int $userId ID de l'utilisateur
     * @return bool TRUE si succès, FALSE sinon
     * 
     * EXEMPLE :
     * $wishlist = new Wishlist();
     * if ($wishlist->clearUserWishlist(5)) {
     *     echo "Tous vos favoris ont été supprimés";
     * }
     */
    public function clearUserWishlist($userId) {
        try {
            $sql = "DELETE FROM wishlist WHERE user_id = :user_id";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':user_id' => $userId]);
            
        } catch (PDOException $e) {
            error_log("Erreur clear wishlist: " . $e->getMessage());
            return false;
        }
    }

    /**
     * ============================================
     * OBTENIR LES IDs DES PRODUITS EN FAVORIS
     * ============================================
     * 
     * Récupère uniquement les IDs des produits en favoris (léger et rapide).
     * Utile pour vérifier rapidement plusieurs produits à la fois.
     * 
     * @param int $userId ID de l'utilisateur
     * @return array Tableau d'IDs de produits [42, 51, 89, ...]
     * 
     * EXEMPLE :
     * $wishlist = new Wishlist();
     * $favoriteIds = $wishlist->getUserWishlistIds(5);
     * // [42, 51, 89]
     * if (in_array($productId, $favoriteIds)) {
     *     echo "Ce produit est en favoris !";
     * }
     */
    public function getUserWishlistIds($userId) {
        try {
            $sql = "SELECT product_id FROM wishlist WHERE user_id = :user_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            
            // fetchAll avec FETCH_COLUMN renvoie un simple tableau d'IDs
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
            
        } catch (PDOException $e) {
            error_log("Erreur récupération IDs wishlist: " . $e->getMessage());
            return [];
        }
    }

    /**
     * ============================================
     * VÉRIFIER SI UN PRODUIT EST POPULAIRE EN WISHLIST
     * ============================================
     * 
     * Compte combien d'utilisateurs ont ajouté ce produit en favoris.
     * Utile pour afficher "🔥 2,341 personnes veulent ce produit".
     * 
     * @param int $productId ID du produit
     * @return int Nombre d'utilisateurs ayant ce produit en favoris
     * 
     * EXEMPLE :
     * $wishlist = new Wishlist();
     * $popularity = $wishlist->getProductWishlistCount(42);
     * echo "{$popularity} personnes veulent ce produit !";
     */
    public function getProductWishlistCount($productId) {
        try {
            $sql = "SELECT COUNT(*) FROM wishlist WHERE product_id = :product_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':product_id' => $productId]);
            
            return (int) $stmt->fetchColumn();
            
        } catch (PDOException $e) {
            error_log("Erreur comptage produit wishlist: " . $e->getMessage());
            return 0;
        }
    }
}

/**
 * ================================================
 * FIN DU MODÈLE WISHLIST
 * ================================================
 * 
 * NOTES POUR LA MAINTENANCE :
 * 
 * 1. PERFORMANCES :
 *    - Index sur (user_id, product_id) = requêtes ultra-rapides
 *    - EXISTS() plus performant que COUNT() pour vérifications
 *    - FETCH_COLUMN pour récupérer uniquement les IDs
 * 
 * 2. SÉCURITÉ :
 *    - Paramètres bindés (protection SQL injection)
 *    - ON CONFLICT DO NOTHING (évite erreurs doublons)
 *    - try/catch sur toutes les requêtes
 * 
 * 3. ÉVOLUTIONS POSSIBLES :
 *    - Ajouter une notification email "Produit en promo"
 *    - Limiter le nombre de favoris par utilisateur
 *    - Statistiques sur les produits les plus ajoutés
 *    - Export CSV de sa wishlist
 * 
 * ================================================
 */
