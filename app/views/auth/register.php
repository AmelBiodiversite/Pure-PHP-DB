<?php
/**
 * MARKETFLOW PRO - PAGE D'INSCRIPTION
 * Fichier : app/views/auth/register.php
 */
?>

<div class="container" style="max-width: 550px; margin-top: var(--space-12); margin-bottom: var(--space-16);">
    
    <!-- Card d'inscription -->
    <div class="card" style="padding: var(--space-8);">
        
        <!-- Logo/Titre -->
        <div class="text-center mb-8">
            <h1 class="text-gradient" style="font-size: 2rem; margin-bottom: var(--space-2);">
                Créer un compte
            </h1>
            <p style="color: var(--text-secondary); font-size: 0.875rem;">
                Rejoignez la marketplace des créateurs digitaux
            </p>
        </div>

        <!-- Message d'erreur général -->
        <?php if (isset($errors['general'])): ?>
        <div style="
            background: var(--error-light);
            border: 1px solid var(--error);
            color: var(--error);
            padding: var(--space-3) var(--space-4);
            border-radius: var(--radius);
            margin-bottom: var(--space-6);
            font-size: 0.875rem;
        ">
            ⚠ <?= e($errors['general']) ?>
        </div>
        <?php endif; ?>

        <!-- Formulaire -->
        <form method="POST" action="/register" id="registerForm">
            
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

            <!-- Type de compte -->
            <div class="form-group">
                <label class="form-label">Type de compte</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-3);">
                    
                    <label class="account-type-card" data-type="buyer">
                        <input type="radio" name="user_type" value="buyer" checked style="display: none;">
                        <div class="account-type-content">
                            <div style="font-size: 2rem; margin-bottom: var(--space-2);">🛍️</div>
                            <h4 style="font-size: 1rem; margin-bottom: var(--space-1);">Acheteur</h4>
                            <p style="font-size: 0.75rem; color: var(--text-tertiary); margin: 0;">
                                Acheter des produits digitaux
                            </p>
                        </div>
                    </label>

                    <label class="account-type-card" data-type="seller">
                        <input type="radio" name="user_type" value="seller" style="display: none;">
                        <div class="account-type-content">
                            <div style="font-size: 2rem; margin-bottom: var(--space-2);">💼</div>
                            <h4 style="font-size: 1rem; margin-bottom: var(--space-1);">Vendeur</h4>
                            <p style="font-size: 0.75rem; color: var(--text-tertiary); margin: 0;">
                                Vendre vos créations
                            </p>
                        </div>
                    </label>

                </div>
            </div>

            <!-- Nom complet -->
            <div class="form-group">
                <label class="form-label" for="full_name">
                    Nom complet
                </label>
                <input 
                    type="text" 
                    id="full_name" 
                    name="full_name" 
                    class="form-input <?= isset($errors['full_name']) ? 'error' : '' ?>"
                    placeholder="Jean Dupont"
                    value="<?= isset($old['full_name']) ? e($old['full_name']) : '' ?>"
                    autofocus
                >
                <?php if (isset($errors['full_name'])): ?>
                    <div class="form-error"><?= e($errors['full_name']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label class="form-label" for="email">
                    Adresse email
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input <?= isset($errors['email']) ? 'error' : '' ?>"
                    placeholder="vous@example.com"
                    value="<?= isset($old['email']) ? e($old['email']) : '' ?>"
                    required
                >
                <?php if (isset($errors['email'])): ?>
                    <div class="form-error"><?= e($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Username -->
            <div class="form-group">
                <label class="form-label" for="username">
                    Nom d'utilisateur
                </label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="form-input <?= isset($errors['username']) ? 'error' : '' ?>"
                    placeholder="jeandupont"
                    value="<?= isset($old['username']) ? e($old['username']) : '' ?>"
                    required
                >
                <small style="font-size: 0.75rem; color: var(--text-tertiary); display: block; margin-top: var(--space-1);">
                    Lettres, chiffres, tirets et underscores uniquement
                </small>
                <?php if (isset($errors['username'])): ?>
                    <div class="form-error"><?= e($errors['username']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Shop Name (uniquement pour vendeurs) -->
            <div class="form-group" id="shopNameGroup" style="display: none;">
                <label class="form-label" for="shop_name">
                    Nom de votre boutique
                </label>
                <input 
                    type="text" 
                    id="shop_name" 
                    name="shop_name" 
                    class="form-input <?= isset($errors['shop_name']) ? 'error' : '' ?>"
                    placeholder="Ma Super Boutique"
                    value="<?= isset($old['shop_name']) ? e($old['shop_name']) : '' ?>"
                >
                <?php if (isset($errors['shop_name'])): ?>
                    <div class="form-error"><?= e($errors['shop_name']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Mot de passe -->
            <div class="form-group">
                <label class="form-label" for="password">
                    Mot de passe
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input <?= isset($errors['password']) ? 'error' : '' ?>"
                    placeholder="••••••••"
                    required
                >
                <small style="font-size: 0.75rem; color: var(--text-tertiary); display: block; margin-top: var(--space-1);">
                    Minimum 8 caractères
                </small>
                <?php if (isset($errors['password'])): ?>
                    <div class="form-error"><?= e($errors['password']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Confirmation mot de passe -->
            <div class="form-group">
                <label class="form-label" for="password_confirm">
                    Confirmer le mot de passe
                </label>
                <input 
                    type="password" 
                    id="password_confirm" 
                    name="password_confirm" 
                    class="form-input <?= isset($errors['password_confirm']) ? 'error' : '' ?>"
                    placeholder="••••••••"
                    required
                >
                <?php if (isset($errors['password_confirm'])): ?>
                    <div class="form-error"><?= e($errors['password_confirm']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Acceptation des CGU -->
            <div class="form-group">
                <label style="display: flex; align-items: start; gap: var(--space-2); cursor: pointer;">
                    <input 
                        type="checkbox" 
                        name="terms" 
                        required
                        style="width: 18px; height: 18px; cursor: pointer; margin-top: 2px;"
                    >
                    <span style="font-size: 0.875rem; color: var(--text-secondary);">
                        J'accepte les 
                        <a href="/terms" target="_blank" style="color: var(--primary-600);">Conditions d'utilisation</a>
                        et la 
                        <a href="/privacy" target="_blank" style="color: var(--primary-600);">Politique de confidentialité</a>
                    </span>
                </label>
            </div>

            <!-- Bouton d'inscription -->
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: var(--space-4);">
                Créer mon compte
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

        <!-- Lien connexion -->
        <div class="text-center">
            <p style="color: var(--text-secondary); font-size: 0.9375rem;">
                Vous avez déjà un compte ?
                <a href="/login" style="color: var(--primary-600); font-weight: 600;">
                    Se connecter
                </a>
            </p>
        </div>

    </div>

</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const submitBtn = form.querySelector('button[type="submit"]');
    const accountTypeCards = document.querySelectorAll('.account-type-card');
    const shopNameGroup = document.getElementById('shopNameGroup');
    const shopNameInput = document.getElementById('shop_name');

    // Gestion du changement de type de compte
    accountTypeCards.forEach(card => {
        card.addEventListener('click', function() {
            // Retirer l'état actif de toutes les cards
            accountTypeCards.forEach(c => c.classList.remove('active'));
            
            // Activer la card cliquée
            this.classList.add('active');
            
            // Cocher le radio correspondant
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Afficher/masquer le champ shop_name
            if (radio.value === 'seller') {
                shopNameGroup.style.display = 'block';
                shopNameInput.required = true;
            } else {
                shopNameGroup.style.display = 'none';
                shopNameInput.required = false;
            }
        });
    });

    // Activer la première card par défaut
    accountTypeCards[0].classList.add('active');

    // Validation du formulaire
    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Création en cours...';
        submitBtn.style.opacity = '0.7';

        // Validation côté client
        const password = form.password.value;
        const passwordConfirm = form.password_confirm.value;
        const terms = form.terms.checked;

        if (password !== passwordConfirm) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Créer mon compte';
            submitBtn.style.opacity = '1';
            return false;
        }

        if (password.length < 8) {
            e.preventDefault();
            alert('Le mot de passe doit contenir au moins 8 caractères');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Créer mon compte';
            submitBtn.style.opacity = '1';
            return false;
        }

        if (!terms) {
            e.preventDefault();
            alert('Vous devez accepter les conditions d\'utilisation');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Créer mon compte';
            submitBtn.style.opacity = '1';
            return false;
        }
    });

    // Indicateur de force du mot de passe
    const passwordInput = document.getElementById('password');
    passwordInput.addEventListener('input', function() {
        const strength = calculatePasswordStrength(this.value);
        // Vous pouvez ajouter un indicateur visuel ici
    });

    function calculatePasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        if (/\d/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;
        return strength;
    }
});
</script>

<style>
/* Styles des cards de type de compte */
.account-type-card {
    cursor: pointer;
    border: 2px solid var(--border-color);
    border-radius: var(--radius);
    padding: var(--space-4);
    transition: all var(--transition);
    text-align: center;
}

.account-type-card:hover {
    border-color: var(--primary-300);
    background: var(--bg-secondary);
}

.account-type-card.active {
    border-color: var(--primary-600);
    background: var(--primary-50);
}

@media (prefers-color-scheme: dark) {
    .account-type-card.active {
        background: rgba(14, 165, 233, 0.1);
    }
}

.account-type-content {
    pointer-events: none;
}

/* Input avec erreur */
.form-input.error {
    border-color: var(--error);
}

.form-input.error:focus {
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

/* Animation */
.card {
    animation: fadeIn 0.5s ease-out;
}
</style>