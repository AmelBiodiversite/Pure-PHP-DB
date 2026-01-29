-- ================================================================
-- MARKETFLOW PRO - SCHÉMA DE BASE DE DONNÉES COMPLET
-- ================================================================
-- Version : 2.1
-- Date : 16 janvier 2025
-- SGBD : PostgreSQL 13+
-- Encodage : UTF-8
-- 
-- DESCRIPTION :
-- Base de données complète pour marketplace de produits digitaux
-- avec système de paiement Stripe, avis clients, wishlist et analytics.
--
-- TABLES (12 au total) :
-- ├── users              : Utilisateurs (buyers/sellers/admins)
-- ├── categories         : Catégories de produits
-- ├── products           : Produits digitaux
-- ├── tags               : Tags pour filtrage
-- ├── product_tags       : Association produits ↔ tags
-- ├── product_gallery    : Images supplémentaires produits
-- ├── promo_codes        : Codes promotionnels
-- ├── orders             : Commandes passées
-- ├── order_items        : Produits dans les commandes
-- ├── reviews            : Avis clients
-- ├── wishlist           : Favoris utilisateurs ❤️
-- └── activity_logs      : Journal d'activité (audit)
--
-- VUES (2 au total) :
-- ├── products_with_seller   : Produits avec infos vendeur
-- └── orders_with_details    : Commandes avec infos acheteur
--
-- FONCTIONNALITÉS :
-- ✅ Multi-rôles (buyer/seller/admin)
-- ✅ Système de commissions (10%)
-- ✅ Codes promo (% ou montant fixe)
-- ✅ Téléchargements limités (3 max)
-- ✅ Système de notation 1-5 étoiles
-- ✅ Wishlist avec compteur temps réel
-- ✅ Activity logs pour audit
-- ✅ Triggers automatiques (stats, numéros commande)
-- ================================================================

-- ================================================================
-- NETTOYAGE (ATTENTION : Supprime toutes les données)
-- ================================================================
-- Décommenter pour réinitialiser complètement la BDD
/*
DROP TABLE IF EXISTS activity_logs CASCADE;
DROP TABLE IF EXISTS reviews CASCADE;
DROP TABLE IF EXISTS order_items CASCADE;
DROP TABLE IF EXISTS orders CASCADE;
DROP TABLE IF EXISTS product_gallery CASCADE;
DROP TABLE IF EXISTS product_tags CASCADE;
DROP TABLE IF EXISTS tags CASCADE;
DROP TABLE IF EXISTS products CASCADE;
DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS wishlist CASCADE;
DROP TABLE IF EXISTS promo_codes CASCADE;
DROP TABLE IF EXISTS users CASCADE;

DROP VIEW IF EXISTS products_with_seller CASCADE;
DROP VIEW IF EXISTS orders_with_details CASCADE;

DROP FUNCTION IF EXISTS update_updated_at_column() CASCADE;
DROP FUNCTION IF EXISTS update_product_rating() CASCADE;
DROP FUNCTION IF EXISTS generate_order_number() CASCADE;
*/

-- ================================================================
-- TABLE : users
-- ================================================================
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    
    role VARCHAR(20) NOT NULL DEFAULT 'buyer',
    CONSTRAINT check_role CHECK (role IN ('buyer', 'seller', 'admin')),
    
    avatar_url VARCHAR(500),
    bio TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    
    remember_token VARCHAR(100),
    last_login TIMESTAMP,
    
    shop_name VARCHAR(255),
    shop_slug VARCHAR(255) UNIQUE,
    shop_description TEXT,
    shop_logo VARCHAR(500),
    shop_banner VARCHAR(500),
    
    total_sales DECIMAL(12,2) DEFAULT 0.00,
    total_earnings DECIMAL(12,2) DEFAULT 0.00,
    total_products INTEGER DEFAULT 0,
    rating_average DECIMAL(3,2) DEFAULT 0.00,
    rating_count INTEGER DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role);
CREATE INDEX IF NOT EXISTS idx_users_shop_slug ON users(shop_slug);

COMMENT ON TABLE users IS 'Utilisateurs de la plateforme (buyers, sellers, admins)';

-- ================================================================
-- TABLE : categories
-- ================================================================
CREATE TABLE IF NOT EXISTS categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    
    parent_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
    
    is_active BOOLEAN DEFAULT TRUE,
    display_order INTEGER DEFAULT 0,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_categories_slug ON categories(slug);
CREATE INDEX IF NOT EXISTS idx_categories_parent ON categories(parent_id);

COMMENT ON TABLE categories IS 'Catégories produits (hiérarchie à 2 niveaux)';

-- Catégories par défaut
INSERT INTO categories (name, slug, description, icon, display_order) VALUES
('Templates', 'templates', 'Templates web et design', '🎨', 1),
('Graphics', 'graphics', 'Ressources graphiques', '🖼️', 2),
('Code', 'code', 'Scripts et codes source', '💻', 3),
('Courses', 'courses', 'Formations et tutoriels', '📚', 4),
('Photos', 'photos', 'Photos stock', '📸', 5),
('Audio', 'audio', 'Musiques et sons', '🎵', 6),
('Fonts', 'fonts', 'Polices de caractères', '🔤', 7),
('Other', 'other', 'Autres produits digitaux', '📦', 8)
ON CONFLICT (slug) DO NOTHING;

-- ================================================================
-- TABLE : products
-- ================================================================
CREATE TABLE IF NOT EXISTS products (
    id SERIAL PRIMARY KEY,
    seller_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    category_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
    
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    short_description TEXT,
    
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    file_url VARCHAR(500) NOT NULL,
    file_size BIGINT,
    file_type VARCHAR(50),
    thumbnail_url VARCHAR(500),
    preview_url VARCHAR(500),
    demo_url VARCHAR(500),
    
    license_type VARCHAR(50) DEFAULT 'single',
    
    downloads INTEGER DEFAULT 0,
    views INTEGER DEFAULT 0,
    sales INTEGER DEFAULT 0,
    revenue DECIMAL(12,2) DEFAULT 0.00,
    rating_average DECIMAL(3,2) DEFAULT 0.00,
    rating_count INTEGER DEFAULT 0,
    
    status VARCHAR(20) DEFAULT 'pending',
    CONSTRAINT check_status CHECK (status IN ('pending', 'approved', 'rejected', 'suspended')),
    is_featured BOOLEAN DEFAULT FALSE,
    rejection_reason TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_at TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_products_seller ON products(seller_id);
CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_products_slug ON products(slug);
CREATE INDEX IF NOT EXISTS idx_products_status ON products(status);
CREATE INDEX IF NOT EXISTS idx_products_price ON products(price);

COMMENT ON TABLE products IS 'Produits digitaux vendus sur la plateforme';

-- ================================================================
-- TABLE : tags
-- ================================================================
CREATE TABLE IF NOT EXISTS tags (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    usage_count INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_tags_slug ON tags(slug);

COMMENT ON TABLE tags IS 'Tags réutilisables pour les produits';

-- ================================================================
-- TABLE : product_tags
-- ================================================================
CREATE TABLE IF NOT EXISTS product_tags (
    id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    tag_id INTEGER NOT NULL REFERENCES tags(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(product_id, tag_id)
);

CREATE INDEX IF NOT EXISTS idx_product_tags_product ON product_tags(product_id);
CREATE INDEX IF NOT EXISTS idx_product_tags_tag ON product_tags(tag_id);

-- ================================================================
-- TABLE : product_gallery
-- ================================================================
CREATE TABLE IF NOT EXISTS product_gallery (
    id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    image_url VARCHAR(500) NOT NULL,
    display_order INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_gallery_product ON product_gallery(product_id);

COMMENT ON TABLE product_gallery IS 'Images supplémentaires des produits';

-- ================================================================
-- TABLE : promo_codes
-- ================================================================
CREATE TABLE IF NOT EXISTS promo_codes (
    id SERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    
    type VARCHAR(20) NOT NULL DEFAULT 'percentage',
    CONSTRAINT check_promo_type CHECK (type IN ('percentage', 'fixed')),
    value DECIMAL(10,2) NOT NULL,
    
    min_purchase DECIMAL(10,2) DEFAULT 0.00,
    max_uses INTEGER,
    used_count INTEGER DEFAULT 0,
    expires_at TIMESTAMP,
    
    is_active BOOLEAN DEFAULT TRUE,
    created_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_promo_code ON promo_codes(code);

COMMENT ON TABLE promo_codes IS 'Codes promotionnels';

-- Code promo de bienvenue
INSERT INTO promo_codes (code, type, value, max_uses, is_active) 
VALUES ('WELCOME10', 'percentage', 10.00, 100, TRUE)
ON CONFLICT (code) DO NOTHING;

-- ================================================================
-- TABLE : orders
-- ================================================================
CREATE TABLE IF NOT EXISTS orders (
    id SERIAL PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    buyer_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    
    subtotal DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    platform_fee DECIMAL(10,2) DEFAULT 0.00,
    
    payment_method VARCHAR(50) DEFAULT 'stripe',
    payment_status VARCHAR(20) DEFAULT 'pending',
    CONSTRAINT check_payment_status CHECK (payment_status IN ('pending', 'processing', 'completed', 'failed', 'refunded')),
    stripe_payment_id VARCHAR(255),
    stripe_session_id VARCHAR(255),
    
    promo_code_id INTEGER REFERENCES promo_codes(id) ON DELETE SET NULL,
    promo_discount DECIMAL(10,2) DEFAULT 0.00,
    
    status VARCHAR(20) DEFAULT 'pending',
    CONSTRAINT check_order_status CHECK (status IN ('pending', 'processing', 'completed', 'cancelled', 'refunded')),
    
    paid_at TIMESTAMP,
    completed_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_orders_buyer ON orders(buyer_id);
CREATE INDEX IF NOT EXISTS idx_orders_number ON orders(order_number);
CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status);
CREATE INDEX IF NOT EXISTS idx_orders_created ON orders(created_at);

COMMENT ON TABLE orders IS 'Commandes passées par les acheteurs';

-- ================================================================
-- TABLE : order_items
-- ================================================================
CREATE TABLE IF NOT EXISTS order_items (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE RESTRICT,
    seller_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    
    product_title VARCHAR(255) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    quantity INTEGER DEFAULT 1,
    
    seller_amount DECIMAL(10,2) NOT NULL,
    platform_fee DECIMAL(10,2) NOT NULL,
    
    license_key VARCHAR(100) UNIQUE,
    download_count INTEGER DEFAULT 0,
    max_downloads INTEGER DEFAULT 3,
    
    review_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_order_items_order ON order_items(order_id);
CREATE INDEX IF NOT EXISTS idx_order_items_product ON order_items(product_id);
CREATE INDEX IF NOT EXISTS idx_order_items_seller ON order_items(seller_id);
CREATE INDEX IF NOT EXISTS idx_order_items_license ON order_items(license_key);

COMMENT ON TABLE order_items IS 'Produits achetés dans chaque commande';

-- ================================================================
-- TABLE : reviews
-- ================================================================
CREATE TABLE IF NOT EXISTS reviews (
    id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    buyer_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    order_item_id INTEGER REFERENCES order_items(id) ON DELETE SET NULL,
    
    rating INTEGER NOT NULL,
    CONSTRAINT check_rating CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(255),
    comment TEXT,
    
    is_verified_purchase BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT TRUE,
    
    seller_response TEXT,
    seller_responded_at TIMESTAMP,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(product_id, buyer_id)
);

CREATE INDEX IF NOT EXISTS idx_reviews_product ON reviews(product_id);
CREATE INDEX IF NOT EXISTS idx_reviews_buyer ON reviews(buyer_id);
CREATE INDEX IF NOT EXISTS idx_reviews_rating ON reviews(rating);

COMMENT ON TABLE reviews IS 'Avis clients sur les produits';

-- ================================================================
-- TABLE : wishlist
-- ================================================================
CREATE TABLE IF NOT EXISTS wishlist (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(user_id, product_id)
);

CREATE INDEX IF NOT EXISTS idx_wishlist_user ON wishlist(user_id);
CREATE INDEX IF NOT EXISTS idx_wishlist_product ON wishlist(product_id);

COMMENT ON TABLE wishlist IS 'Produits favoris des utilisateurs';

-- ================================================================
-- TABLE : activity_logs
-- ================================================================
CREATE TABLE IF NOT EXISTS activity_logs (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    
    action VARCHAR(100) NOT NULL,
    
    entity_type VARCHAR(50),
    entity_id INTEGER,
    
    ip_address INET,
    user_agent TEXT,
    
    metadata JSONB,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_logs_user ON activity_logs(user_id);
CREATE INDEX IF NOT EXISTS idx_logs_action ON activity_logs(action);
CREATE INDEX IF NOT EXISTS idx_logs_created ON activity_logs(created_at);

COMMENT ON TABLE activity_logs IS 'Journal actions (audit trail)';

-- ================================================================
-- FONCTIONS ET TRIGGERS
-- ================================================================

-- Mise à jour automatique updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_categories_updated_at BEFORE UPDATE ON categories
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_products_updated_at BEFORE UPDATE ON products
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_orders_updated_at BEFORE UPDATE ON orders
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_reviews_updated_at BEFORE UPDATE ON reviews
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Mise à jour stats produit après avis
CREATE OR REPLACE FUNCTION update_product_rating()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE products SET
        rating_average = (
            SELECT COALESCE(AVG(rating), 0)
            FROM reviews
            WHERE product_id = NEW.product_id AND is_approved = TRUE
        ),
        rating_count = (
            SELECT COUNT(*)
            FROM reviews
            WHERE product_id = NEW.product_id AND is_approved = TRUE
        )
    WHERE id = NEW.product_id;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_product_rating_after_review
AFTER INSERT OR UPDATE ON reviews
FOR EACH ROW EXECUTE FUNCTION update_product_rating();

-- Génération numéro de commande
CREATE OR REPLACE FUNCTION generate_order_number()
RETURNS TRIGGER AS $$
BEGIN
    NEW.order_number = 'ORD-' || TO_CHAR(CURRENT_TIMESTAMP, 'YYYYMMDD') || '-' || LPAD(NEW.id::TEXT, 6, '0');
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER generate_order_number_trigger
BEFORE INSERT ON orders
FOR EACH ROW EXECUTE FUNCTION generate_order_number();

-- ================================================================
-- VUES UTILES
-- ================================================================

-- Produits avec infos vendeur
CREATE OR REPLACE VIEW products_with_seller AS
SELECT 
    p.*,
    u.username as seller_username,
    u.shop_name as seller_shop_name,
    u.rating_average as seller_rating,
    c.name as category_name,
    c.slug as category_slug
FROM products p
LEFT JOIN users u ON p.seller_id = u.id
LEFT JOIN categories c ON p.category_id = c.id;

-- Commandes avec détails
CREATE OR REPLACE VIEW orders_with_details AS
SELECT 
    o.*,
    u.full_name as buyer_name,
    u.email as buyer_email,
    COUNT(oi.id) as items_count,
    COALESCE(SUM(oi.quantity), 0) as items_quantity
FROM orders o
LEFT JOIN users u ON o.buyer_id = u.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id, u.full_name, u.email;

-- ================================================================
-- DONNÉES DE TEST (OPTIONNEL)
-- ================================================================
-- Décommenter pour insérer des données de test

-- Admin : admin / admin123
INSERT INTO users (full_name, username, email, password, role, is_active, email_verified)
VALUES (
    'Administrateur',
    'admin',
    'admin@marketflowpro.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    TRUE,
    TRUE
)
ON CONFLICT (email) DO NOTHING;

-- Vendeur : johndoe / seller123
INSERT INTO users (
    full_name, username, email, password, role,
    shop_name, shop_slug, shop_description,
    is_active, email_verified
)
VALUES (
    'John Doe',
    'johndoe',
    'seller@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'seller',
    'Digital Creative Studio',
    'digital-creative',
    'High-quality digital products for creatives',
    TRUE,
    TRUE
)
ON CONFLICT (email) DO NOTHING;

-- Acheteur : janesmith / buyer123
INSERT INTO users (full_name, username, email, password, role, is_active, email_verified)
VALUES (
    'Jane Smith',
    'janesmith',
    'buyer@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'buyer',
    TRUE,
    TRUE
)
ON CONFLICT (email) DO NOTHING;

-- Produit de test
INSERT INTO products (
    seller_id,
    category_id,
    title,
    slug,
    description,
    short_description,
    price,
    file_url,
    thumbnail_url,
    status
)
SELECT 
    u.id,
    c.id,
    'Premium Dashboard Template',
    'premium-dashboard-template',
    'Modern and responsive admin dashboard template with clean design and powerful features.',
    'Beautiful admin dashboard template',
    49.99,
    '/uploads/products/dashboard-template.zip',
    '/uploads/products/dashboard-thumb.jpg',
    'approved'
FROM users u
CROSS JOIN categories c
WHERE u.username = 'johndoe' AND c.slug = 'templates'
LIMIT 1
ON CONFLICT (slug) DO NOTHING;

-- ================================================================
-- FIN DU SCHEMA
-- ================================================================
-- ================================================
-- MARKETFLOW PRO - SCHÉMA POSTGRESQL
-- Base de données complète pour Replit
-- ================================================

-- Supprimer les tables si elles existent (pour réinitialisation)
DROP TABLE IF EXISTS activity_logs CASCADE;
DROP TABLE IF EXISTS reviews CASCADE;
DROP TABLE IF EXISTS order_items CASCADE;
DROP TABLE IF EXISTS orders CASCADE;
DROP TABLE IF EXISTS product_gallery CASCADE;
DROP TABLE IF EXISTS product_tags CASCADE;
DROP TABLE IF EXISTS tags CASCADE;
DROP TABLE IF EXISTS products CASCADE;
DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS wishlist CASCADE;
DROP TABLE IF EXISTS promo_codes CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- ================================================
-- TABLE : users
-- ================================================
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'buyer',
    avatar_url VARCHAR(500),
    bio TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    remember_token VARCHAR(100),
    last_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Champs vendeur
    shop_name VARCHAR(255),
    shop_slug VARCHAR(255) UNIQUE,
    shop_description TEXT,
    shop_logo VARCHAR(500),
    shop_banner VARCHAR(500),
    
    -- Statistiques
    total_sales DECIMAL(12,2) DEFAULT 0.00,
    total_earnings DECIMAL(12,2) DEFAULT 0.00,
    total_products INTEGER DEFAULT 0,
    rating_average DECIMAL(3,2) DEFAULT 0.00,
    rating_count INTEGER DEFAULT 0,
    
    CONSTRAINT check_role CHECK (role IN ('buyer', 'seller', 'admin'))
);

-- Index pour performances
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_users_shop_slug ON users(shop_slug);

-- ================================================
-- TABLE : categories
-- ================================================
CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    parent_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
    is_active BOOLEAN DEFAULT TRUE,
    display_order INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_categories_slug ON categories(slug);
CREATE INDEX idx_categories_parent ON categories(parent_id);

-- Catégories par défaut
INSERT INTO categories (name, slug, description, icon, display_order) VALUES
('Templates', 'templates', 'Templates web et design', '🎨', 1),
('Graphics', 'graphics', 'Ressources graphiques', '🖼️', 2),
('Code', 'code', 'Scripts et codes source', '💻', 3),
('Courses', 'courses', 'Formations et tutoriels', '📚', 4),
('Photos', 'photos', 'Photos stock', '📸', 5),
('Audio', 'audio', 'Musiques et sons', '🎵', 6),
('Fonts', 'fonts', 'Polices de caractères', '🔤', 7),
('Other', 'other', 'Autres produits digitaux', '📦', 8);

-- ================================================
-- TABLE : products
-- ================================================
CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    seller_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    category_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
    
    -- Informations produit
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    short_description TEXT,
    
    -- Prix et fichiers
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    file_url VARCHAR(500) NOT NULL,
    file_size BIGINT,
    file_type VARCHAR(50),
    thumbnail_url VARCHAR(500),
    preview_url VARCHAR(500),
    demo_url VARCHAR(500),
    
    -- Licences
    license_type VARCHAR(50) DEFAULT 'single',
    
    -- Statistiques
    downloads INTEGER DEFAULT 0,
    views INTEGER DEFAULT 0,
    sales INTEGER DEFAULT 0,
    revenue DECIMAL(12,2) DEFAULT 0.00,
    rating_average DECIMAL(3,2) DEFAULT 0.00,
    rating_count INTEGER DEFAULT 0,
    
    -- Statut
    status VARCHAR(20) DEFAULT 'pending',
    is_featured BOOLEAN DEFAULT FALSE,
    rejection_reason TEXT,
    
    -- Dates
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_at TIMESTAMP,
    
    CONSTRAINT check_status CHECK (status IN ('pending', 'approved', 'rejected', 'suspended'))
);

CREATE INDEX idx_products_seller ON products(seller_id);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_slug ON products(slug);
CREATE INDEX idx_products_status ON products(status);
CREATE INDEX idx_products_price ON products(price);

-- ================================================
-- TABLE : tags
-- ================================================
CREATE TABLE tags (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    usage_count INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_tags_slug ON tags(slug);

-- ================================================
-- TABLE : product_tags
-- ================================================
CREATE TABLE product_tags (
    id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    tag_id INTEGER NOT NULL REFERENCES tags(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(product_id, tag_id)
);

CREATE INDEX idx_product_tags_product ON product_tags(product_id);
CREATE INDEX idx_product_tags_tag ON product_tags(tag_id);

-- ================================================
-- TABLE : product_gallery
-- ================================================
CREATE TABLE product_gallery (
    id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    image_url VARCHAR(500) NOT NULL,
    display_order INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_gallery_product ON product_gallery(product_id);

-- ================================================
-- TABLE : promo_codes
-- ================================================
CREATE TABLE promo_codes (
    id SERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    type VARCHAR(20) NOT NULL DEFAULT 'percentage',
    value DECIMAL(10,2) NOT NULL,
    min_purchase DECIMAL(10,2) DEFAULT 0.00,
    max_uses INTEGER,
    used_count INTEGER DEFAULT 0,
    expires_at TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT check_promo_type CHECK (type IN ('percentage', 'fixed'))
);

CREATE INDEX idx_promo_code ON promo_codes(code);

-- Code promo de test
INSERT INTO promo_codes (code, type, value, max_uses, is_active) 
VALUES ('WELCOME10', 'percentage', 10.00, 100, TRUE);

-- ================================================
-- TABLE : orders
-- ================================================
CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    buyer_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    
    -- Montants
    subtotal DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    platform_fee DECIMAL(10,2) DEFAULT 0.00,
    
    -- Paiement
    payment_method VARCHAR(50) DEFAULT 'stripe',
    payment_status VARCHAR(20) DEFAULT 'pending',
    stripe_payment_id VARCHAR(255),
    stripe_session_id VARCHAR(255),
    
    -- Code promo
    promo_code_id INTEGER REFERENCES promo_codes(id) ON DELETE SET NULL,
    promo_discount DECIMAL(10,2) DEFAULT 0.00,
    
    -- Statut
    status VARCHAR(20) DEFAULT 'pending',
    
    -- Dates
    paid_at TIMESTAMP,
    completed_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT check_payment_status CHECK (payment_status IN ('pending', 'processing', 'completed', 'failed', 'refunded')),
    CONSTRAINT check_status CHECK (status IN ('pending', 'processing', 'completed', 'cancelled', 'refunded'))
);

CREATE INDEX idx_orders_buyer ON orders(buyer_id);
CREATE INDEX idx_orders_number ON orders(order_number);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_created ON orders(created_at);

-- ================================================
-- TABLE : order_items
-- ================================================
CREATE TABLE order_items (
    id SERIAL PRIMARY KEY,
    order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE RESTRICT,
    seller_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    
    -- Détails au moment de l'achat
    product_title VARCHAR(255) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    quantity INTEGER DEFAULT 1,
    
    -- Commissions
    seller_amount DECIMAL(10,2) NOT NULL,
    platform_fee DECIMAL(10,2) NOT NULL,
    
    -- Licence et téléchargement
    license_key VARCHAR(100) UNIQUE,
    download_count INTEGER DEFAULT 0,
    max_downloads INTEGER DEFAULT 3,
    
    -- Avis
    review_id INTEGER,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_product ON order_items(product_id);
CREATE INDEX idx_order_items_seller ON order_items(seller_id);
CREATE INDEX idx_order_items_license ON order_items(license_key);

-- ================================================
-- TABLE : reviews
-- ================================================
CREATE TABLE reviews (
    id SERIAL PRIMARY KEY,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    buyer_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    order_item_id INTEGER REFERENCES order_items(id) ON DELETE SET NULL,
    
    rating INTEGER NOT NULL,
    title VARCHAR(255),
    comment TEXT,
    
    -- Modération
    is_verified_purchase BOOLEAN DEFAULT FALSE,
    is_approved BOOLEAN DEFAULT TRUE,
    
    -- Réponse vendeur
    seller_response TEXT,
    seller_responded_at TIMESTAMP,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT check_rating CHECK (rating BETWEEN 1 AND 5),
    UNIQUE(product_id, buyer_id)
);

CREATE INDEX idx_reviews_product ON reviews(product_id);
CREATE INDEX idx_reviews_buyer ON reviews(buyer_id);
CREATE INDEX idx_reviews_rating ON reviews(rating);

-- ================================================
-- TABLE : wishlist
-- ================================================
CREATE TABLE wishlist (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(user_id, product_id)
);

CREATE INDEX idx_wishlist_user ON wishlist(user_id);
CREATE INDEX idx_wishlist_product ON wishlist(product_id);

-- ================================================
-- TABLE : activity_logs
-- ================================================
CREATE TABLE activity_logs (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INTEGER,
    ip_address INET,
    user_agent TEXT,
    metadata JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_logs_user ON activity_logs(user_id);
CREATE INDEX idx_logs_action ON activity_logs(action);
CREATE INDEX idx_logs_created ON activity_logs(created_at);

-- ================================================
-- FONCTIONS ET TRIGGERS
-- ================================================

-- Fonction pour mettre à jour updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Triggers pour updated_at
CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_categories_updated_at BEFORE UPDATE ON categories
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_products_updated_at BEFORE UPDATE ON products
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_orders_updated_at BEFORE UPDATE ON orders
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_reviews_updated_at BEFORE UPDATE ON reviews
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Fonction pour mettre à jour les stats produit après avis
CREATE OR REPLACE FUNCTION update_product_rating()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE products SET
        rating_average = (
            SELECT COALESCE(AVG(rating), 0)
            FROM reviews
            WHERE product_id = NEW.product_id AND is_approved = TRUE
        ),
        rating_count = (
            SELECT COUNT(*)
            FROM reviews
            WHERE product_id = NEW.product_id AND is_approved = TRUE
        )
    WHERE id = NEW.product_id;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER update_product_rating_after_review
AFTER INSERT OR UPDATE ON reviews
FOR EACH ROW EXECUTE FUNCTION update_product_rating();

-- Fonction pour générer un numéro de commande unique
CREATE OR REPLACE FUNCTION generate_order_number()
RETURNS TRIGGER AS $$
BEGIN
    NEW.order_number = 'ORD-' || TO_CHAR(CURRENT_TIMESTAMP, 'YYYYMMDD') || '-' || LPAD(NEW.id::TEXT, 6, '0');
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER generate_order_number_trigger
BEFORE INSERT ON orders
FOR EACH ROW EXECUTE FUNCTION generate_order_number();

-- ================================================
-- DONNÉES DE TEST (optionnel)
-- ================================================

-- Utilisateur admin par défaut
INSERT INTO users (full_name, username, email, password, role, is_active, email_verified)
VALUES (
    'Admin Principal',
    'admin',
    'admin@marketflow.com',
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5Y7BvqMBG.yl2', -- password: admin123
    'admin',
    TRUE,
    TRUE
);

-- Vendeur de test
INSERT INTO users (
    full_name, username, email, password, role, 
    shop_name, shop_slug, shop_description, is_active, email_verified
) VALUES (
    'John Seller',
    'johnseller',
    'seller@marketflow.com',
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5Y7BvqMBG.yl2', -- password: admin123
    'seller',
    'John''s Digital Shop',
    'johns-digital-shop',
    'Premium digital products and templates',
    TRUE,
    TRUE
);

-- Acheteur de test
INSERT INTO users (full_name, username, email, password, role, is_active, email_verified)
VALUES (
    'Jane Buyer',
    'janebuyer',
    'buyer@marketflow.com',
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/LewY5Y7BvqMBG.yl2', -- password: admin123
    'buyer',
    TRUE,
    TRUE
);

-- ================================================
-- PERMISSIONS ET SÉCURITÉ
-- ================================================

-- Activer Row Level Security (optionnel mais recommandé)
-- ALTER TABLE users ENABLE ROW LEVEL SECURITY;
-- ALTER TABLE products ENABLE ROW LEVEL SECURITY;
-- etc.

-- ================================================
-- VUES UTILES (optionnel)
-- ================================================

-- Vue des produits avec informations vendeur
CREATE VIEW products_with_seller AS
SELECT 
    p.*,
    u.username as seller_username,
    u.shop_name as seller_shop_name,
    u.rating_average as seller_rating,
    c.name as category_name,
    c.slug as category_slug
FROM products p
LEFT JOIN users u ON p.seller_id = u.id
LEFT JOIN categories c ON p.category_id = c.id;

-- Vue des commandes avec détails
CREATE VIEW orders_with_details AS
SELECT 
    o.*,
    u.full_name as buyer_name,
    u.email as buyer_email,
    COUNT(oi.id) as items_count
FROM orders o
LEFT JOIN users u ON o.buyer_id = u.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id, u.full_name, u.email;

-- ================================================
-- FIN DU SCHÉMA
-- ================================================

-- Afficher les tables créées
SELECT table_name 
FROM information_schema.tables 
WHERE table_schema = 'public' 
ORDER BY table_name;-- ================================================================
-- MARKETFLOW PRO - DONNÉES FICTIVES POUR LA VENTE
-- 26 produits répartis sur 11 catégories + 3 vendeurs
-- ================================================================

-- ================================================================
-- 1. CRÉATION DES VENDEURS FICTIFS
-- ================================================================

INSERT INTO users (username, email, password, full_name, role, status, created_at) VALUES
('sarah_designs', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah Martinez', 'seller', 'active', NOW()),
('alex_creative', 'alex@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alex Chen', 'seller', 'active', NOW()),
('emma_studio', 'emma@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emma Williams', 'seller', 'active', NOW());

-- ================================================================
-- 2. TEMPLATES (3 produits) - Category ID: 1
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(2, 1, 'Modern SaaS Landing Page', 'modern-saas-landing-page', 'Template de landing page professionnelle pour startups SaaS. Design moderne avec animations fluides, sections hero impactantes, pricing tables, témoignages clients et CTA optimisés. Entièrement responsive et optimisé SEO. Inclut HTML, CSS, JS et documentation complète.', 'Landing page SaaS moderne et responsive avec animations', 49.00, 79.00, '/uploads/products/saas-template.zip', 'application/zip', 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800', 'extended', 'approved', true, 4.80, 47, 234, 1856, 234, NOW() - INTERVAL '15 days'),

(3, 1, 'E-Commerce Product Showcase', 'ecommerce-product-showcase', 'Template e-commerce complet avec système de filtres avancés, grille produits responsive, pages détail optimisées et panier intégré. Design minimaliste et élégant. Compatible avec tous les CMS populaires. Inclut 12 variations de pages.', 'Template e-commerce premium avec filtres et panier', 65.00, 99.00, '/uploads/products/ecommerce-template.zip', 'application/zip', 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=800', 'extended', 'approved', true, 4.90, 62, 189, 2134, 189, NOW() - INTERVAL '8 days'),

(1, 1, 'Portfolio Creative Agency', 'portfolio-creative-agency', 'Portfolio moderne pour agences créatives et freelances. Mise en page spectaculaire avec parallax, galerie projets interactive, animations on-scroll et dark mode. Parfait pour designers, photographes et studios. 100% customizable.', 'Portfolio créatif avec parallax et dark mode', 39.00, 59.00, '/uploads/products/portfolio-template.zip', 'application/zip', 'https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?w=800', 'single', 'approved', false, 4.70, 31, 156, 987, 156, NOW() - INTERVAL '20 days');

-- ================================================================
-- 3. GRAPHICS (2 produits) - Category ID: 2
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(2, 2, 'Abstract 3D Backgrounds Pack', 'abstract-3d-backgrounds-pack', 'Collection de 50 fonds abstraits 3D en haute résolution (4K). Parfaits pour sites web, présentations, réseaux sociaux. Formats PNG et JPG inclus. Couleurs modernes et vibrantes. Chaque image optimisée pour le web et l impression.', 'Pack de 50 backgrounds 3D abstraits 4K', 29.00, 49.00, '/uploads/products/3d-backgrounds.zip', 'application/zip', 'https://images.unsplash.com/photo-1557672172-298e090bd0f1?w=800', 'extended', 'approved', true, 4.85, 73, 412, 2891, 412, NOW() - INTERVAL '12 days'),

(3, 2, 'Gradient Mesh Collection', 'gradient-mesh-collection', 'Collection artistique de 30 mesh gradients fluides. Idéal pour designs modernes, interfaces, branding. Fichiers vectoriels SVG + PNG haute résolution. Palette de couleurs tendance 2026. Facilement personnalisable dans Figma, Adobe XD ou Illustrator.', 'Collection de 30 mesh gradients modernes', 24.00, NULL, '/uploads/products/gradient-mesh.zip', 'application/zip', 'https://images.unsplash.com/photo-1558591710-4b4a1ae0f04d?w=800', 'extended', 'approved', false, 4.60, 28, 198, 1456, 198, NOW() - INTERVAL '18 days');

-- ================================================================
-- 4. CODE (2 produits) - Category ID: 3
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(4, 3, 'React Dashboard Components', 'react-dashboard-components', 'Bibliothèque complète de composants React pour dashboards. +40 composants (charts, tables, cards, forms). TypeScript support. Styled avec Tailwind CSS. Documentation interactive. Compatible React 18+. Mise à jour régulière et support technique inclus.', 'Bibliothèque de 40+ composants React dashboard', 79.00, 129.00, '/uploads/products/react-dashboard.zip', 'application/zip', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800', 'extended', 'approved', true, 4.95, 89, 267, 3421, 267, NOW() - INTERVAL '5 days'),

(1, 3, 'PHP Authentication System', 'php-authentication-system', 'Système d authentification PHP complet et sécurisé. Inscription, connexion, reset mot de passe, 2FA, gestion sessions. Protection CSRF, XSS, SQL injection. Compatible PostgreSQL et MySQL. Code propre PSR-12. Documentation détaillée.', 'Système auth PHP sécurisé avec 2FA', 45.00, NULL, '/uploads/products/php-auth.zip', 'application/zip', 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=800', 'single', 'approved', false, 4.75, 44, 178, 1234, 178, NOW() - INTERVAL '22 days');

-- ================================================================
-- 5. COURSES (2 produits) - Category ID: 4
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(2, 4, 'UI/UX Design Masterclass 2026', 'ui-ux-design-masterclass-2026', 'Formation complète UI/UX Design (12h de vidéo). Apprenez Figma, design systems, prototypage, user research. 25 projets pratiques. Certificat inclus. Accès lifetime et mises à jour gratuites. Fichiers sources Figma inclus.', 'Formation UI/UX complète avec 25 projets pratiques', 89.00, 149.00, '/uploads/products/uiux-course.zip', 'application/zip', 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=800', 'single', 'approved', true, 4.92, 156, 423, 4567, 423, NOW() - INTERVAL '3 days'),

(3, 4, 'JavaScript Avancé - Concepts Modernes', 'javascript-avance-concepts-modernes', 'Maîtrisez JavaScript moderne (ES6+). Promises, async/await, modules, closures, prototypes. 8h de vidéo + exercices corrigés. Projets réels : API REST, SPA, tests unitaires. Code source complet fourni.', 'Formation JavaScript ES6+ avec projets réels', 69.00, 99.00, '/uploads/products/js-advanced-course.zip', 'application/zip', 'https://images.unsplash.com/photo-1579468118864-1b9ea3c0db4a?w=800', 'single', 'approved', false, 4.80, 67, 234, 2345, 234, NOW() - INTERVAL '14 days');

-- ================================================================
-- 6. PHOTOS (2 produits) - Category ID: 5
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(4, 5, 'Workspace Flat Lay Photography', 'workspace-flat-lay-photography', 'Collection de 40 photos flat lay professionnelles de bureaux créatifs. Haute résolution (6000x4000px). Parfait pour blogs, réseaux sociaux, sites web. Ambiances minimalistes et modernes. License commerciale étendue.', 'Pack de 40 photos flat lay workspace HD', 35.00, NULL, '/uploads/products/workspace-photos.zip', 'application/zip', 'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=800', 'extended', 'approved', false, 4.70, 38, 167, 1567, 167, NOW() - INTERVAL '19 days'),

(2, 5, 'Nature & Landscape Premium', 'nature-landscape-premium', 'Collection exclusive de 60 photos nature et paysages. Montagnes, forêts, océans, ciels dramatiques. Résolution 8K. Retouche professionnelle. Idéal pour sites voyage, magazines, impressions grand format. Formats RAW + JPG.', 'Collection de 60 photos nature et paysages 8K', 59.00, 89.00, '/uploads/products/nature-photos.zip', 'application/zip', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800', 'extended', 'approved', true, 4.88, 91, 289, 2678, 289, NOW() - INTERVAL '7 days');

-- ================================================================
-- 7. AUDIO (2 produits) - Category ID: 6
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(3, 6, 'UI Sound Effects Pack', 'ui-sound-effects-pack', 'Pack de 150 effets sonores pour interfaces (clics, notifications, transitions, succès, erreurs). Format WAV 48kHz. Parfait pour apps, sites web, jeux. Sons discrets et professionnels. License commerciale illimitée.', 'Pack de 150 sons UI professionnels', 32.00, NULL, '/uploads/products/ui-sounds.zip', 'application/zip', 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?w=800', 'extended', 'approved', false, 4.65, 42, 198, 1345, 198, NOW() - INTERVAL '16 days'),

(4, 6, 'Ambient Music for Content Creators', 'ambient-music-content-creators', 'Collection de 20 morceaux ambient/lo-fi (2-5min chacun). Parfait pour vlogs, podcasts, vidéos YouTube. Format MP3 320kbps + WAV. Atmosphères relaxantes et inspirantes. License YouTube et réseaux sociaux incluse.', 'Pack de 20 musiques ambient pour créateurs', 44.00, 69.00, '/uploads/products/ambient-music.zip', 'application/zip', 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=800', 'extended', 'approved', true, 4.82, 67, 312, 2234, 312, NOW() - INTERVAL '9 days');

-- ================================================================
-- 8. FONTS (2 produits) - Category ID: 7
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(1, 7, 'Modernist Sans - Font Family', 'modernist-sans-font-family', 'Famille de polices sans-serif moderne (9 graisses). Parfaite pour interfaces, branding, éditorial. Support multilingue complet. Formats OTF, TTF, WOFF, WOFF2. Ligatures et alternatives stylistiques. License desktop + web.', 'Police sans-serif moderne - 9 graisses', 55.00, 99.00, '/uploads/products/modernist-font.zip', 'application/zip', 'https://images.unsplash.com/photo-1456086272160-b28b0645b729?w=800', 'extended', 'approved', true, 4.78, 53, 234, 1987, 234, NOW() - INTERVAL '11 days'),

(2, 7, 'Handwritten Signature Font', 'handwritten-signature-font', 'Police manuscrite élégante style signature. Idéale pour logos, invitations, branding luxe. Caractères alternatifs et ligatures. Support accents français. Formats OTF et TTF. License commerciale complète.', 'Police manuscrite élégante pour branding', 28.00, NULL, '/uploads/products/signature-font.zip', 'application/zip', 'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=800', 'single', 'approved', false, 4.60, 29, 145, 1123, 145, NOW() - INTERVAL '24 days');

-- ================================================================
-- 9. OTHER (2 produits) - Category ID: 8
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(3, 8, 'Social Media Templates Bundle', 'social-media-templates-bundle', 'Mega pack de 200+ templates pour Instagram, Facebook, LinkedIn. Stories, posts, carrousels. Formats Canva + PSD. Design moderne et professionnel. Entièrement personnalisable. Idéal pour entrepreneurs et agences.', 'Pack de 200+ templates réseaux sociaux', 42.00, 79.00, '/uploads/products/social-templates.zip', 'application/zip', 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800', 'extended', 'approved', true, 4.75, 94, 378, 3234, 378, NOW() - INTERVAL '6 days'),

(4, 8, 'Business Presentation Slides', 'business-presentation-slides', 'Pack de 150 slides PowerPoint et Keynote professionnels. Layouts variés pour pitchs, rapports, formations. Graphiques, timelines, mockups. Design minimaliste et corporate. Facile à customiser.', 'Pack de 150 slides présentation business', 38.00, NULL, '/uploads/products/presentation-slides.zip', 'application/zip', 'https://images.unsplash.com/photo-1551818255-e6e10975bc17?w=800', 'single', 'approved', false, 4.68, 47, 201, 1678, 201, NOW() - INTERVAL '13 days');

-- ================================================================
-- 10. UI KITS (3 produits) - Category ID: 9
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(2, 9, 'iOS App UI Kit - Finance', 'ios-app-ui-kit-finance', 'UI Kit complet pour applications finance iOS. +60 écrans (onboarding, dashboard, transactions, analytics). Design system complet. Components Figma auto-layout. Dark mode inclus. Guide de style détaillé.', 'UI Kit iOS Finance avec 60+ écrans', 69.00, 119.00, '/uploads/products/ios-finance-ui.zip', 'application/zip', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800', 'extended', 'approved', true, 4.92, 112, 345, 4123, 345, NOW() - INTERVAL '4 days'),

(4, 9, 'Mobile E-commerce UI Kit', 'mobile-ecommerce-ui-kit', 'Kit UI e-commerce mobile moderne. 50+ écrans pour app shopping complète. Catalogue, détails produits, panier, checkout, profil. Format Figma et Sketch. Design system cohérent. iOS et Android.', 'Kit UI e-commerce mobile - 50+ écrans', 59.00, 99.00, '/uploads/products/mobile-ecom-ui.zip', 'application/zip', 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=800', 'extended', 'approved', true, 4.85, 87, 289, 2987, 289, NOW() - INTERVAL '10 days'),

(3, 9, 'Dashboard Admin UI Components', 'dashboard-admin-ui-components', 'Bibliothèque de composants pour dashboards admin. Tables avancées, charts interactifs, forms complexes, notifications, modals. Format Figma avec variants. Design system complet. Light et dark mode.', 'Composants dashboard admin Figma', 52.00, NULL, '/uploads/products/admin-dashboard-ui.zip', 'application/zip', 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800', 'single', 'approved', false, 4.72, 63, 223, 1876, 223, NOW() - INTERVAL '17 days');

-- ================================================================
-- 11. ICÔNES (3 produits) - Category ID: 11
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(1, 11, 'Minimalist Line Icons 500+', 'minimalist-line-icons-500', 'Collection de 500+ icônes line minimalistes. 20 catégories (business, tech, social, e-commerce...). Stroke ajustable. Formats SVG, PNG, IconFont. Grid parfaite. Cohérence visuelle garantie.', 'Pack de 500+ icônes line minimalistes', 34.00, 59.00, '/uploads/products/line-icons.zip', 'application/zip', 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800', 'extended', 'approved', true, 4.88, 124, 467, 3876, 467, NOW() - INTERVAL '2 days'),

(2, 11, '3D Isometric Icons Pack', '3d-isometric-icons-pack', 'Pack de 120 icônes 3D isométriques. Style moderne et coloré. Parfait pour apps, presentations, sites web. Formats PNG 4K + fichiers 3D sources (Blender). 6 variations de couleurs par icône.', 'Pack de 120 icônes 3D isométriques', 48.00, NULL, '/uploads/products/3d-icons.zip', 'application/zip', 'https://images.unsplash.com/photo-1618761714954-0b8cd0026356?w=800', 'extended', 'approved', true, 4.80, 78, 312, 2654, 312, NOW() - INTERVAL '8 days'),

(4, 11, 'Animated Icons for Web', 'animated-icons-web', 'Collection de 80 icônes animées (Lottie JSON). Micro-interactions fluides. Categories : actions, status, navigation, social. Poids léger. Compatible tous frameworks. Fichiers After Effects sources inclus.', 'Pack de 80 icônes animées Lottie', 44.00, 69.00, '/uploads/products/animated-icons.zip', 'application/zip', 'https://images.unsplash.com/photo-1557804506-669a67965ba0?w=800', 'single', 'approved', false, 4.75, 56, 234, 1987, 234, NOW() - INTERVAL '15 days');

-- ================================================================
-- 12. ILLUSTRATIONS (3 produits) - Category ID: 12
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(3, 12, 'Business Illustrations Pack', 'business-illustrations-pack', 'Collection de 40 illustrations business professionnelles. Teamwork, success, innovation, growth. Style flat moderne. Formats SVG + PNG. Couleurs personnalisables. Parfait pour presentations et landing pages.', 'Pack de 40 illustrations business flat', 39.00, 69.00, '/uploads/products/business-illustrations.zip', 'application/zip', 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800', 'extended', 'approved', true, 4.82, 89, 334, 2765, 334, NOW() - INTERVAL '6 days'),

(1, 12, 'Character Avatars Collection', 'character-avatars-collection', 'Set de 100 avatars de personnages divers et inclusifs. Styles variés, expressions multiples. Format SVG vectoriel. Idéal pour apps sociales, forums, dashboards. Facile à personnaliser dans Figma.', 'Collection de 100 avatars personnages', 32.00, NULL, '/uploads/products/character-avatars.zip', 'application/zip', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=800', 'single', 'approved', false, 4.68, 52, 198, 1654, 198, NOW() - INTERVAL '21 days'),

(2, 12, 'Abstract Geometric Shapes', 'abstract-geometric-shapes', 'Pack de 60 formes géométriques abstraites. Design moderne et minimaliste. Parfait pour backgrounds, headers, decorations. Formats AI, SVG, PNG. Couleurs tendance 2026. Haute qualité vectorielle.', 'Pack de 60 formes géométriques abstraites', 29.00, 49.00, '/uploads/products/geometric-shapes.zip', 'application/zip', 'https://images.unsplash.com/photo-1557672172-298e090bd0f1?w=800', 'extended', 'approved', false, 4.70, 41, 176, 1432, 176, NOW() - INTERVAL '12 days');

-- ================================================================
-- FIN DU FICHIER
-- Total : 3 vendeurs + 26 produits (2-3 par catégorie)
-- ================================================================-- Table des codes promotionnels
CREATE TABLE IF NOT EXISTS promo_codes (
    id SERIAL PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    discount_type VARCHAR(20) NOT NULL CHECK (discount_type IN ('percentage', 'fixed')),
    discount_value DECIMAL(10, 2) NOT NULL,
    min_amount DECIMAL(10, 2) DEFAULT 0,
    usage_limit INTEGER,
    usage_count INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    expires_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index sur le code pour recherche rapide
CREATE INDEX IF NOT EXISTS idx_promo_code ON promo_codes(code);

-- Quelques codes promo de test
INSERT INTO promo_codes (code, discount_type, discount_value, min_amount, usage_limit, is_active, expires_at) VALUES
('BIENVENUE10', 'percentage', 10.00, 0, 100, TRUE, NOW() + INTERVAL '30 days'),
('PROMO20', 'percentage', 20.00, 50.00, 50, TRUE, NOW() + INTERVAL '60 days'),
('NOEL25', 'percentage', 25.00, 100.00, 200, TRUE, '2026-12-31 23:59:59'),
('FIXE5', 'fixed', 5.00, 20.00, NULL, TRUE, NULL)
ON CONFLICT (code) DO NOTHING;
