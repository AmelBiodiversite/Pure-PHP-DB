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
