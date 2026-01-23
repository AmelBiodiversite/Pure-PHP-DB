<?php
/**
 * MARKETFLOW PRO - PAGE DE CONNEXION (VERSION AMÉLIORÉE)
 * Fichier : app/views/auth/login.php
 */
?>

<div style="
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-8);
    position: relative;
    overflow: hidden;
    background: #f8fafc;
">
        <!-- Background decoratif -->
        <div style="
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: rgba(59, 130, 246, 0.35);
            border-radius: 50%;
            filter: blur(100px);
        "></div>

        <div style="
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: rgba(37, 99, 235, 0.25);
            border-radius: 50%;
            filter: blur(80px);
        "></div>
        
        <!-- Card de connexion -->
        <div style="
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 480px;
            width: 100%;
            padding: var(--space-10);
            position: relative;
            z-index: 10;
            animation: slideUp 0.6s ease-out;
        ">    
        <!-- Logo/Titre -->
        <div style="text-align: center; margin-bottom: var(--space-8);">
            <div style="
                width: 70px;
                height: 70px;
                margin: 0 auto var(--space-4);
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                border-radius: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 2rem;
                font-weight: 900;
                box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            ">
                M
            </div>
            <h1 style="
                font-size: 2rem;
                margin-bottom: var(--space-2);
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            ">
                Bon retour ! 👋
            </h1>
            <p style="color: #64748b; font-size: 0.95rem;">
                Connectez-vous pour continuer
            </p>
        </div>

        <!-- Message d'erreur -->
        <?php if (isset($error)): ?>
        <div style="
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            color: #dc2626;
            padding: var(--space-4);
            border-radius: var(--radius-lg);
            margin-bottom: var(--space-6);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: var(--space-3);
        ">
            <span style="font-size: 1.5rem;">⚠️</span>
            <span><?= e($error) ?></span>
        </div>
        <?php endif; ?>

        <!-- Formulaire -->
        <form method="POST" action="/login" id="loginForm">
            
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <!-- Email -->
            <div style="margin-bottom: var(--space-5);">
                <label style="
                    display: block;
                    font-weight: 600;
                    margin-bottom: var(--space-2);
                    color: #1e293b;
                    font-size: 0.9rem;
                " for="email">
                    📧 Adresse email
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    style="
                        width: 100%;
                        padding: var(--space-4);
                        border: 2px solid var(--border-color);
                        border-radius: 12px;
                        font-size: 1rem;
                        transition: all 0.3s;
                    "
                    placeholder="vous@example.com"
                    value="<?= isset($email) ? e($email) : '' ?>"
                    onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 4px rgba(102, 126, 234, 0.1)'"
                    onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'"
                    required
                    autofocus
                >
            </div>

            <!-- Mot de passe -->
            <div style="margin-bottom: var(--space-4);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-2);">
                    <label style="
                        font-weight: 600;
                        color: #1e293b;
                        font-size: 0.9rem;
                    " for="password">
                        🔒 Mot de passe
                    </label>
                    <a href="/forgot-password" style="
                        font-size: 0.85rem;
                        color: #667eea;
                        text-decoration: none;
                        font-weight: 500;
                        transition: color 0.2s;
                    " onmouseover="this.style.color='#764ba2'" onmouseout="this.style.color='#667eea'">
                        Mot de passe oublié ?
                    </a>
                </div>
                <div style="position: relative;">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        style="
                            width: 100%;
                            padding: var(--space-4);
                            padding-right: 3rem;
                            border: 2px solid var(--border-color);
                            border-radius: 12px;
                            font-size: 1rem;
                            transition: all 0.3s;
                        "
                        placeholder="••••••••"
                        onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 4px rgba(102, 126, 234, 0.1)'"
                        onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'"
                        required
                    >
                    <button 
                        type="button" 
                        onclick="togglePassword()" 
                        style="
                            position: absolute;
                            right: 12px;
                            top: 50%;
                            transform: translateY(-50%);
                            background: none;
                            border: none;
                            cursor: pointer;
                            font-size: 1.3rem;
                            padding: 0.5rem;
                            color: #94a3b8;
                            transition: color 0.2s;
                        "
                        onmouseover="this.style.color='#1e293b'"
                        onmouseout="this.style.color='#94a3b8'"
                        title="Afficher/masquer le mot de passe"
                    >
                        <span id="password-toggle-icon">👁️</span>
                    </button>
                </div>
            </div>

            <!-- Remember me -->
            <div style="margin-bottom: var(--space-6);">
                <label style="
                    display: flex;
                    align-items: center;
                    gap: var(--space-2);
                    cursor: pointer;
                    padding: var(--space-2);
                    border-radius: 8px;
                    transition: background 0.2s;
                " onmouseover="this.style.background='var(--bg-secondary)'" onmouseout="this.style.background='transparent'">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        style="
                            width: 20px;
                            height: 20px;
                            cursor: pointer;
                            accent-color: #667eea;
                            flex-shrink: 0;
                        "
                    >
                    <span style="font-size: 0.9rem; color: #64748b;">
                        Se souvenir de moi
                    </span>
                </label>
            </div>

            <!-- Bouton de connexion -->
            <button type="submit" style="
                width: 100%;
                padding: var(--space-4);
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                border-radius: 12px;
                font-size: 1.05rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(102, 126, 234, 0.5)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.4)'">
                🚀 Se connecter
            </button>

        </form>

        <!-- Séparateur -->
        <div style="
            display: flex;
            align-items: center;
            margin: var(--space-8) 0;
            gap: var(--space-4);
        ">
            <div style="flex: 1; height: 1px; background: var(--border-color);"></div>
            <span style="color: #94a3b8; font-size: 0.9rem; font-weight: 500;">OU</span>
            <div style="flex: 1; height: 1px; background: var(--border-color);"></div>
        </div>

        <!-- Lien inscription -->
        <div style="text-align: center;">
            <p style="color: #64748b; font-size: 0.95rem; margin: 0;">
                Vous n'avez pas de compte ?
                <a href="/register" style="
                    color: #667eea;
                    font-weight: 600;
                    text-decoration: none;
                    transition: color 0.2s;
                " onmouseover="this.style.color='#764ba2'" onmouseout="this.style.color='#667eea'">
                    S'inscrire gratuitement →
                </a>
            </p>
        </div>

        <!-- Infos additionnelles -->
        <div style="
            text-align: center;
            margin-top: var(--space-6);
            padding-top: var(--space-6);
            border-top: 1px solid var(--border-color);
            color: #94a3b8;
            font-size: 0.8rem;
            line-height: 1.6;
        ">
            En vous connectant, vous acceptez nos
            <a href="/terms" style="color: #667eea; text-decoration: none;">CGU</a>
            et notre
            <a href="/privacy" style="color: #667eea; text-decoration: none;">Politique de confidentialité</a>
        </div>

    </div>

</div>

<!-- JavaScript -->
<script>
// Toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const icon = document.getElementById('password-toggle-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.textContent = '🙈';
    } else {
        passwordInput.type = 'password';
        icon.textContent = '👁️';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    form.addEventListener('submit', function(e) {
        const email = form.email.value.trim();
        const password = form.password.value;
        
        if (!email || !password) {
            e.preventDefault();
            alert('❌ Veuillez remplir tous les champs');
            return false;
        }
        
        if (!email.includes('@')) {
            e.preventDefault();
            alert('❌ Veuillez entrer un email valide');
            return false;
        }
        
        // Désactiver le bouton pendant la soumission
        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ Connexion en cours...';
        submitBtn.style.opacity = '0.7';
    });
});
</script>

<style>
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 640px) {
    div[style*="padding: var(--space-10)"] {
        padding: var(--space-6) !important;
    }
}
</style>