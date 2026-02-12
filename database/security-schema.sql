-- ================================================================
-- MARKETFLOW PRO - SCHÉMA DE SÉCURITÉ v2.0
-- ================================================================
-- Tables dédiées au système de sécurité :
--   - security_logs      : Journal des événements de sécurité
--   - blocked_ips        : IPs bloquées (manuellement ou auto)
--   - whitelisted_ips    : IPs en liste blanche
--   - rate_limits        : Rate limiting persistant en DB
--
-- À exécuter APRÈS le schema.sql principal
-- ================================================================

-- ================================================================
-- TABLE : security_logs
-- ================================================================
-- Stocke tous les événements de sécurité détectés par le système
-- (tentatives de connexion, attaques XSS/SQLi/CSRF, etc.)
CREATE TABLE IF NOT EXISTS security_logs (
    id SERIAL PRIMARY KEY,

    -- Type d'événement (LOGIN_FAILED, XSS_ATTEMPT, CSRF_VIOLATION, etc.)
    event_type VARCHAR(50) NOT NULL,

    -- Niveau de sévérité déduit automatiquement par SecurityLogger
    severity VARCHAR(20) NOT NULL DEFAULT 'INFO',
    CONSTRAINT check_severity CHECK (severity IN ('INFO', 'WARNING', 'CRITICAL')),

    -- Adresse IP source (type INET natif PostgreSQL pour filtrage réseau)
    ip INET,

    -- URI ciblée par la requête suspecte
    uri VARCHAR(500),

    -- User-Agent du navigateur
    user_agent TEXT,

    -- Utilisateur associé (NULL si anonyme)
    user_id INTEGER REFERENCES users(id) ON DELETE SET NULL,

    -- Email de l'utilisateur (dénormalisé pour recherche rapide)
    user_email VARCHAR(255),

    -- Données contextuelles en JSON (payload, raison, etc.)
    data JSONB,

    -- Horodatage de l'événement (avec timezone pour multi-régions)
    timestamp TIMESTAMP WITH TIME ZONE DEFAULT NOW(),

    -- Date de création technique
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Index pour les requêtes fréquentes du dashboard
CREATE INDEX IF NOT EXISTS idx_security_logs_timestamp ON security_logs(timestamp DESC);
CREATE INDEX IF NOT EXISTS idx_security_logs_event_type ON security_logs(event_type);
CREATE INDEX IF NOT EXISTS idx_security_logs_severity ON security_logs(severity);
CREATE INDEX IF NOT EXISTS idx_security_logs_ip ON security_logs(ip);
CREATE INDEX IF NOT EXISTS idx_security_logs_user_id ON security_logs(user_id);

-- Index composé pour les filtres combinés du dashboard
CREATE INDEX IF NOT EXISTS idx_security_logs_type_severity ON security_logs(event_type, severity);

-- Index GIN pour la recherche full-text dans les données JSON
CREATE INDEX IF NOT EXISTS idx_security_logs_data ON security_logs USING GIN(data);

COMMENT ON TABLE security_logs IS 'Journal des événements de sécurité (attaques, connexions, violations)';


-- ================================================================
-- TABLE : blocked_ips
-- ================================================================
-- IPs bloquées manuellement par un admin ou automatiquement par le système
CREATE TABLE IF NOT EXISTS blocked_ips (
    id SERIAL PRIMARY KEY,

    -- Adresse IP bloquée (UNIQUE pour éviter les doublons)
    ip INET NOT NULL UNIQUE,

    -- Raison du blocage (affichée dans le dashboard)
    reason TEXT DEFAULT '',

    -- Type de blocage : MANUAL (admin) ou AUTO (système)
    block_type VARCHAR(20) NOT NULL DEFAULT 'AUTO',
    CONSTRAINT check_block_type CHECK (block_type IN ('MANUAL', 'AUTO')),

    -- Admin qui a bloqué l'IP (NULL si AUTO)
    blocked_by INTEGER REFERENCES users(id) ON DELETE SET NULL,

    -- Date de blocage
    blocked_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),

    -- Date d'expiration (NULL = permanent, sinon temporaire)
    expires_at TIMESTAMP WITH TIME ZONE,

    -- Flag actif/inactif (permet de débloquer sans supprimer l'historique)
    is_active BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Index pour la vérification rapide à chaque requête HTTP
CREATE INDEX IF NOT EXISTS idx_blocked_ips_active ON blocked_ips(ip) WHERE is_active = TRUE;
CREATE INDEX IF NOT EXISTS idx_blocked_ips_expires ON blocked_ips(expires_at) WHERE expires_at IS NOT NULL;

COMMENT ON TABLE blocked_ips IS 'Adresses IP bloquées (manuellement ou automatiquement)';


-- ================================================================
-- TABLE : whitelisted_ips
-- ================================================================
-- IPs en liste blanche qui ne seront jamais bloquées automatiquement
CREATE TABLE IF NOT EXISTS whitelisted_ips (
    id SERIAL PRIMARY KEY,

    -- Adresse IP whitelistée (UNIQUE)
    ip INET NOT NULL UNIQUE,

    -- Description (ex: "Bureau Paris", "VPN entreprise")
    description TEXT DEFAULT '',

    -- Admin qui a ajouté l'IP
    added_by INTEGER REFERENCES users(id) ON DELETE SET NULL,

    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

COMMENT ON TABLE whitelisted_ips IS 'Adresses IP en liste blanche (jamais bloquées auto)';


-- ================================================================
-- TABLE : rate_limits
-- ================================================================
-- Rate limiting persistant en base de données
-- Remplace le rate limiting en mémoire PHP (perdu à chaque requête)
CREATE TABLE IF NOT EXISTS rate_limits (
    id SERIAL PRIMARY KEY,

    -- Identifiant unique de l'action (ex: "block_ip_user_1")
    action_key VARCHAR(255) NOT NULL,

    -- Compteur de tentatives dans la fenêtre courante
    attempts INTEGER DEFAULT 1,

    -- Début de la fenêtre de temps
    window_start TIMESTAMP WITH TIME ZONE DEFAULT NOW(),

    -- Expiration de la fenêtre (pour nettoyage automatique)
    window_expires TIMESTAMP WITH TIME ZONE NOT NULL,

    -- Contrainte UNIQUE pour le UPSERT
    UNIQUE(action_key)
);

-- Index pour le nettoyage des fenêtres expirées
CREATE INDEX IF NOT EXISTS idx_rate_limits_expires ON rate_limits(window_expires);

COMMENT ON TABLE rate_limits IS 'Rate limiting persistant pour les actions admin sensibles';


-- ================================================================
-- NETTOYAGE AUTOMATIQUE (optionnel - à planifier via cron)
-- ================================================================

-- Fonction de nettoyage des vieux logs INFO (> 90 jours)
-- et des fenêtres de rate limiting expirées
CREATE OR REPLACE FUNCTION cleanup_security_data()
RETURNS void AS $$
BEGIN
    -- Supprimer les logs INFO de plus de 90 jours
    DELETE FROM security_logs
    WHERE severity = 'INFO'
      AND timestamp < NOW() - INTERVAL '90 days';

    -- Supprimer les fenêtres de rate limiting expirées
    DELETE FROM rate_limits
    WHERE window_expires < NOW();

    -- Désactiver les blocages expirés
    UPDATE blocked_ips
    SET is_active = FALSE
    WHERE is_active = TRUE
      AND expires_at IS NOT NULL
      AND expires_at < NOW();
END;
$$ LANGUAGE plpgsql;

COMMENT ON FUNCTION cleanup_security_data() IS 'Nettoyage périodique des données de sécurité obsolètes';

-- ================================================================
-- FIN DU SCHÉMA DE SÉCURITÉ
-- ================================================================
