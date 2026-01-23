-- Table des codes promotionnels
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
