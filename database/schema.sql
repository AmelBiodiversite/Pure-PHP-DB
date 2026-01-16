-- ================================================
-- MARKETFLOW PRO - SCHÉMA DE BASE DE DONNÉES
-- ================================================
-- Version : 2.0
-- Date : 16 janvier 2025
-- SGBD : PostgreSQL 13+
-- Encodage : UTF-8
-- 
-- DESCRIPTION :
-- Base de données complète pour une marketplace de produits digitaux
-- avec système de paiement, avis, wishlist et analytics.
--
-- TABLES PRINCIPALES :
-- - users : Utilisateurs (buyers, sellers, admins)
-- - products : Produits digitaux
-- - orders : Commandes et paiements
-- - reviews : Système d'avis clients
-- - wishlist : Liste de favoris ✨ NOUVELLE
--
-- FONCTIONNALITÉS :
-- ✅ Multi-rôles (buyer/seller/admin)
-- ✅ Gestion des commissions
-- ✅ Codes promo
-- ✅ Téléchargements limités
-- ✅ Système de notation
-- ✅ Activity logs
-- ✅ Wishlist (favoris produits) ✨
-- ================================================

-- NOTE : Ce fichier documente la structure actuelle de la BDD
-- Il a été généré depuis une base existante et sert de référence
-- pour la transmission du projet et la maintenance future.

-- ================================================
-- TABLE : users (Utilisateurs)
-- ================================================
-- Stocke tous les utilisateurs (acheteurs, vendeurs, admins)
-- RÔLES : buyer, seller, admin
-- ================================================

-- TABLE : categories (Catégories de produits)
-- TABLE : products (Produits digitaux)
-- TABLE : tags (Tags pour filtrage)
-- TABLE : product_tags (Association produits-tags)
-- TABLE : product_gallery (Images supplémentaires)
-- TABLE : promo_codes (Codes promotionnels)
-- TABLE : orders (Commandes)
-- TABLE : order_items (Détails des commandes)
-- TABLE : reviews (Avis clients)

-- ================================================
-- TABLE : wishlist (NOUVELLE FONCTIONNALITÉ) ✨
-- ================================================
-- Liste de favoris/souhaits des utilisateurs
-- 
-- FONCTIONNALITÉS :
-- ✅ Permet aux utilisateurs de sauvegarder leurs produits préférés
-- ✅ Contrainte d'unicité : un produit ne peut être favori qu'une fois
-- ✅ Suppression en cascade si user ou produit supprimé
-- 
-- UTILISATION :
-- - Bouton cœur sur les pages produits
-- - Page "Mes Favoris" dans le compte utilisateur
-- - Compteur dans le header
-- ================================================

CREATE TABLE IF NOT EXISTS wishlist (
    -- Identifiant unique
    id SERIAL PRIMARY KEY,
    
    -- Utilisateur propriétaire de la wishlist
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    
    -- Produit ajouté aux favoris
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    
    -- Date d'ajout aux favoris
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- CONTRAINTE : Un utilisateur ne peut ajouter le même produit qu'une fois
    UNIQUE(user_id, product_id)
);

-- INDEX pour optimisation des requêtes
CREATE INDEX IF NOT EXISTS idx_wishlist_user ON wishlist(user_id);
CREATE INDEX IF NOT EXISTS idx_wishlist_product ON wishlist(product_id);

-- COMMENTAIRES pour documentation
COMMENT ON TABLE wishlist IS 'Produits favoris des utilisateurs';
COMMENT ON COLUMN wishlist.user_id IS 'ID de l''utilisateur (référence users.id)';
COMMENT ON COLUMN wishlist.product_id IS 'ID du produit favori (référence products.id)';

-- TABLE : activity_logs (Logs d'activité)

-- ================================================
-- FIN DU SCHÉMA
-- ================================================
-- 
-- NOTES POUR LA TRANSMISSION :
-- 
-- 1. WISHLIST :
--    - Table simple et performante
--    - Contrainte UNIQUE évite les doublons
--    - Index sur user_id pour requêtes rapides
--    - Cascade DELETE pour nettoyage automatique
-- 
-- 2. REQUÊTES COURANTES :
--    - Ajouter : INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)
--    - Supprimer : DELETE FROM wishlist WHERE user_id = ? AND product_id = ?
--    - Vérifier : SELECT EXISTS(SELECT 1 FROM wishlist WHERE user_id = ? AND product_id = ?)
--    - Compter : SELECT COUNT(*) FROM wishlist WHERE user_id = ?
-- 
-- 3. PERFORMANCES :
--    - Index sur user_id + product_id = recherches instantanées
--    - UNIQUE constraint vérifié en O(log n) par PostgreSQL
-- ================================================
