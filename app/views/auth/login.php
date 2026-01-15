<?php
/**
 * MARKETFLOW PRO - PAGE DE CONNEXION
 * Fichier : app/views/auth/login.php
 */
?>

<div class="container" style="max-width: 450px; margin-top: var(--space-16); margin-bottom: var(--space-16);">
    
    <!-- Card de connexion -->
    <div class="card" style="padding: var(--space-8);">
        
        <!-- Logo/Titre -->
        <div class="text-center mb-8">
            <h1 class="text-gradient" style="font-size: 2rem; margin-bottom: var(--space-2);">
                MarketFlow Pro
            </h1>
            <p style="color: var(--text-secondary); font-size: 0.875rem;">
                Connectez-vous à votre compte
            </p>
        </div>

        <!-- Message d'erreur -->
        <?php if (isset($error)): ?>
        <div style="
            background: var(--error-light);
            border: 1px solid var(--error);
            color: var(--error);
            padding: var(--space-3) var(--space-4);
            border-radius: var(--radius);
            margin-bottom: var(--space-6);
            font-size: 0.875rem;
        ">
            ⚠ <?= e($error) ?>
        </div>
        <?php endif; ?>

        <!-- Formulaire -->
        <form method="POST" action="/login" id="loginForm">
            
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <!-- Email -->
            <div class="form-group">
                <label class="form-label" for="email">
                    Adresse email
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input"
                    placeholder="vous@example.com"
                    value="<?= isset($email) ? e($email) : '' ?>"
                    required
                    autofocus
                >
            </div>

            <!-- Mot de passe -->
            <div class="form-group">
                <div class="flex-between" style="margin-bottom: var(--space-2);">
                    <label class="form-label" for="password" style="margin-bottom: 0;">
                        Mot de passe
                    </label>
                    <a href="/forgot-password" style="font-size: 0.875rem; color: var(--primary-600);">
                        Mot de passe oublié ?
                    </a>
                </div>
                <div style="position: relative;">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input"
                            placeholder="••••••••"
                            style="padding-right: 45px;"
                            required
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword()" 
                            style="
                                position: absolute;
                                right: 10px;
                                top: 50%;
                                transform: translateY(-50%);
                                background: none;
                                border: none;
                                cursor: pointer;
                                font-size: 1.2rem;
                                padding: 5px;
                            "
                            title="Afficher/Masquer"
                        >
                            👁️
                        </button>
                    </div>
                </div>

                <script>
                function togglePassword() {
                    const passwordInput = document.getElementById('password');
                    const button = event.currentTarget;

                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        button.textContent = '🙈';
                    } else {
                        passwordInput.type = 'password';
                        button.textContent = '👁️';
                    }
                }
                </script>
            </div>

            <!-- Remember me -->
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: var(--space-2); cursor: pointer;">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        style="width: 18px; height: 18px; cursor: pointer;"
                    >
                    <span style="font-size: 0.875rem; color: var(--text-secondary);">
                        Se souvenir de moi
                    </span>
                </label>
            </div>

            <!-- Bouton de connexion -->
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: var(--space-4);">
                Se connecter
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
            <span style="color: var(--text-tertiary); font-size: 0.875rem;">OU</span>
            <div style="flex: 1; height: 1px; background: var(--border-color);"></div>
        </div>

        <!-- Lien inscription -->
        <div class="text-center">
            <p style="color: var(--text-secondary); font-size: 0.9375rem;">
                Vous n'avez pas de compte ?
                <a href="/register" style="color: var(--primary-600); font-weight: 600;">
                    S'inscrire gratuitement
                </a>
            </p>
        </div>

    </div>

    <!-- Infos additionnelles -->
    <div class="text-center mt-8" style="color: var(--text-tertiary); font-size: 0.875rem;">
        <p>
            En vous connectant, vous acceptez nos 
            <a href="/terms" style="color: var(--primary-600);">Conditions d'utilisation</a>
            et notre 
            <a href="/privacy" style="color: var(--primary-600);">Politique de confidentialité</a>
        </p>
    </div>

</div>

<!-- JavaScript pour améliorer l'UX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    form.addEventListener('submit', function(e) {
        // Désactiver le bouton pendant la soumission
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Connexion en cours...';
        submitBtn.style.opacity = '0.7';
        
        // Validation côté client
        const email = form.email.value.trim();
        const password = form.password.value;
        
        if (!email || !password) {
            e.preventDefault();
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Se connecter';
            submitBtn.style.opacity = '1';
            alert('Veuillez remplir tous les champs');
            return false;
        }
        
        if (!email.includes('@')) {
            e.preventDefault();
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Se connecter';
            submitBtn.style.opacity = '1';
            alert('Veuillez entrer un email valide');
            return false;
        }
    });
});
</script>

<style>
/* Animation du formulaire */
.card {
    animation: fadeIn 0.5s ease-out;
}

/* Focus states améliorés */
.form-input:focus {
    transform: translateY(-1px);
}

/* Hover sur le bouton */
.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
}

.btn-primary:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}
</style>