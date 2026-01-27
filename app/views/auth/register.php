                    <?php
                    /**
                     * MARKETFLOW PRO - PAGE D'INSCRIPTION (VERSION AMÉLIORÉE)
                     * Fichier : app/views/auth/register.php
                     */
                    ?>
                    <div style="
                        min-height: 100vh;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        padding: var(--space-8) var(--space-4);
                        background: #f8fafc;
                        position: relative;
                        overflow: hidden;
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


                        <!-- Card d'inscription -->
                        <div style="
                            background: white;
                            border-radius: 24px;
                            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                            max-width: 550px;
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
                                    Créer un compte
                                </h1>
                                <p style="color: #64748b; font-size: 0.95rem;">
                                    Rejoignez la marketplace des créateurs digitaux 🚀
                                </p>
                            </div>

                            <!-- Message d'erreur général -->
                            <?php if (isset($errors['general'])): ?>
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
                                <span><?= e($errors['general']) ?></span>
                            </div>
                            <?php endif; ?>

                            <!-- Formulaire -->
                            <form method="POST" action="/register" id="registerForm">

                                <!-- CSRF Token -->
                                <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">

                                <!-- Type de compte -->
                                <div style="margin-bottom: var(--space-6);">
                                    <label style="
                                        display: block;
                                        font-weight: 600;
                                        margin-bottom: var(--space-3);
                                        color: var(--text-primary);
                                        font-size: 0.95rem;
                                    ">
                                        Type de compte
                                    </label>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-3);">

                                        <label class="account-type-card active" data-type="buyer" style="
                                            cursor: pointer;
                                            border: 2px solid var(--border-color);
                                            border-radius: 16px;
                                            padding: var(--space-5);
                                            transition: all 0.3s;
                                            text-align: center;
                                            position: relative;
                                        ">
                                            <input type="radio" name="role" value="buyer" checked style="display: none;">
                                            <div style="font-size: 2.5rem; margin-bottom: var(--space-2);">🛍️</div>
                                            <h4 style="font-size: 1.05rem; margin-bottom: var(--space-1); font-weight: 600;">Acheteur</h4>
                                            <p style="font-size: 0.8rem; color: #94a3b8; margin: 0;">
                                                Acheter des produits digitaux
                                            </p>
                                        </label>

                                        <label class="account-type-card" data-type="seller" style="
                                            cursor: pointer;
                                            border: 2px solid var(--border-color);
                                            border-radius: 16px;
                                            padding: var(--space-5);
                                            transition: all 0.3s;
                                            text-align: center;
                                            position: relative;
                                        ">
                                            <input type="radio" name="role" value="seller" style="display: none;">
                                            <div style="font-size: 2.5rem; margin-bottom: var(--space-2);">💼</div>
                                            <h4 style="font-size: 1.05rem; margin-bottom: var(--space-1); font-weight: 600;">Vendeur</h4>
                                            <p style="font-size: 0.8rem; color: #94a3b8; margin: 0;">
                                                Vendre vos créations
                                            </p>
                                        </label>

                                    </div>
                                </div>

                                <!-- Nom complet -->
                                <div style="margin-bottom: var(--space-5);">
                                    <label style="
                                        display: block;
                                        font-weight: 600;
                                        margin-bottom: var(--space-2);
                                        color: var(--text-primary);
                                        font-size: 0.9rem;
                                    " for="full_name">
                                        👤 Nom complet
                                    </label>
                                    <input 
                                        type="text" 
                                        id="full_name" 
                                        name="full_name" 
                                        style="
                                            width: 100%;
                                            padding: var(--space-4);
                                            border: 2px solid <?= isset($errors['full_name']) ? '#ef4444' : 'var(--border-color)' ?>;
                                            border-radius: 12px;
                                            font-size: 1rem;
                                            transition: all 0.3s;
                                        "
                                        placeholder="Jean Dupont"
                                        value="<?= isset($old['full_name']) ? e($old['full_name']) : '' ?>"
                                        onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 4px rgba(102, 126, 234, 0.1)'"
                                        onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'"
                                        autofocus
                                    >
                                    <?php if (isset($errors['full_name'])): ?>
                                        <div style="color: #ef4444; font-size: 0.85rem; margin-top: var(--space-2);">
                                            <?= e($errors['full_name']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Email -->
                                <div style="margin-bottom: var(--space-5);">
                                    <label style="
                                        display: block;
                                        font-weight: 600;
                                        margin-bottom: var(--space-2);
                                        color: var(--text-primary);
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
                                            border: 2px solid <?= isset($errors['email']) ? '#ef4444' : 'var(--border-color)' ?>;
                                            border-radius: 12px;
                                            font-size: 1rem;
                                            transition: all 0.3s;
                                        "
                                        placeholder="vous@example.com"
                                        value="<?= isset($old['email']) ? e($old['email']) : '' ?>"
                                        onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 4px rgba(102, 126, 234, 0.1)'"
                                        onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'"
                                        required
                                    >
                                    <?php if (isset($errors['email'])): ?>
                                        <div style="color: #ef4444; font-size: 0.85rem; margin-top: var(--space-2);">
                                            <?= e($errors['email']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Username -->
                                <div style="margin-bottom: var(--space-5);">
                                    <label style="
                                        display: block;
                                        font-weight: 600;
                                        margin-bottom: var(--space-2);
                                        color: var(--text-primary);
                                        font-size: 0.9rem;
                                    " for="username">
                                        🔖 Nom d'utilisateur
                                    </label>
                                    <input 
                                        type="text" 
                                        id="username" 
                                        name="username" 
                                        style="
                                            width: 100%;
                                            padding: var(--space-4);
                                            border: 2px solid <?= isset($errors['username']) ? '#ef4444' : 'var(--border-color)' ?>;
                                            border-radius: 12px;
                                            font-size: 1rem;
                                            transition: all 0.3s;
                                        "
                                        placeholder="jeandupont"
                                        value="<?= isset($old['username']) ? e($old['username']) : '' ?>"
                                        onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 4px rgba(102, 126, 234, 0.1)'"
                                        onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'"
                                        required
                                    >
                                    <small style="font-size: 0.8rem; color: #94a3b8; display: block; margin-top: var(--space-1);">
                                        Lettres, chiffres, tirets et underscores uniquement
                                    </small>
                                    <?php if (isset($errors['username'])): ?>
                                        <div style="color: #ef4444; font-size: 0.85rem; margin-top: var(--space-2);">
                                            <?= e($errors['username']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Shop Name (uniquement pour vendeurs) -->
                                <div id="shopNameGroup" style="margin-bottom: var(--space-5); display: none;">
                                    <label style="
                                        display: block;
                                        font-weight: 600;
                                        margin-bottom: var(--space-2);
                                        color: var(--text-primary);
                                        font-size: 0.9rem;
                                    " for="shop_name">
                                        🏪 Nom de votre boutique
                                    </label>
                                    <input 
                                        type="text" 
                                        id="shop_name" 
                                        name="shop_name" 
                                        style="
                                            width: 100%;
                                            padding: var(--space-4);
                                            border: 2px solid <?= isset($errors['shop_name']) ? '#ef4444' : 'var(--border-color)' ?>;
                                            border-radius: 12px;
                                            font-size: 1rem;
                                            transition: all 0.3s;
                                        "
                                        placeholder="Ma Super Boutique"
                                        value="<?= isset($old['shop_name']) ? e($old['shop_name']) : '' ?>"
                                        onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 4px rgba(102, 126, 234, 0.1)'"
                                        onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'"
                                    >
                                    <?php if (isset($errors['shop_name'])): ?>
                                        <div style="color: #ef4444; font-size: 0.85rem; margin-top: var(--space-2);">
                                            <?= e($errors['shop_name']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Mot de passe -->
                    <div style="margin-bottom: var(--space-5);">
                        <label style="
                            display: block;
                            font-weight: 600;
                            margin-bottom: var(--space-2);
                            color: var(--text-primary);
                            font-size: 0.9rem;
                        " for="password">
                            🔒 Mot de passe
                        </label>
                        <div style="position: relative;">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                style="
                                    width: 100%;
                                    padding: var(--space-4);
                                    padding-right: 3rem;
                                    border: 2px solid <?= isset($errors['password']) ? '#ef4444' : 'var(--border-color)' ?>;
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
                                onclick="togglePassword('password')"
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
                                    color: var(--text-tertiary);
                                    transition: color 0.2s;
                                "
                                onmouseover="this.style.color='var(--text-primary)'"
                                onmouseout="this.style.color='var(--text-tertiary)'"
                                title="Afficher/masquer le mot de passe"
                            >
                                <span id="password-toggle-icon">👁️</span>
                            </button>
                        </div>

                                    <small style="font-size: 0.8rem; color: #94a3b8; display: block; margin-top: var(--space-1);">
                                        Minimum 8 caractères
                                    </small>
                                    <?php if (isset($errors['password'])): ?>
                                        <div style="color: #ef4444; font-size: 0.85rem; margin-top: var(--space-2);">
                                            <?= e($errors['password']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                               <!-- Confirmation mot de passe -->
                    <div style="margin-bottom: var(--space-6);">
                        <label style="
                            display: block;
                            font-weight: 600;
                            margin-bottom: var(--space-2);
                            color: var(--text-primary);
                            font-size: 0.9rem;
                        " for="password_confirm">
                            🔒 Confirmer le mot de passe
                        </label>
                        <div style="position: relative;">
                            <input 
                                type="password" 
                                id="password_confirm" 
                                name="password_confirm" 
                                style="
                                    width: 100%;
                                    padding: var(--space-4);
                                    padding-right: 3rem;
                                    border: 2px solid <?= isset($errors['password_confirm']) ? '#ef4444' : 'var(--border-color)' ?>;
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
                                onclick="togglePassword('password_confirm')"
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
                                    color: var(--text-tertiary);
                                    transition: color 0.2s;
                                "
                                onmouseover="this.style.color='var(--text-primary)'"
                                onmouseout="this.style.color='var(--text-tertiary)'"
                                title="Afficher/masquer le mot de passe"
                            >
                                <span id="password_confirm-toggle-icon">👁️</span>
                            </button>
                        </div>
                                    <?php if (isset($errors['password_confirm'])): ?>
                                        <div style="color: #ef4444; font-size: 0.85rem; margin-top: var(--space-2);">
                                            <?= e($errors['password_confirm']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Acceptation des CGU -->
                                <div style="margin-bottom: var(--space-6);">
                                    <label style="
                                        display: flex;
                                        align-items: start;
                                        gap: var(--space-3);
                                        cursor: pointer;
                                        padding: var(--space-3);
                                        border-radius: 8px;
                                        transition: background 0.2s;
                                    " onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                        <input 
                                            type="checkbox" 
                                            name="terms" 
                                            required
                                            style="
                                                width: 20px;
                                                height: 20px;
                                                cursor: pointer;
                                                margin-top: 2px;
                                                accent-color: #667eea;
                                                flex-shrink: 0;
                                            "
                                        >
                                        <span style="font-size: 0.9rem; color: #475569; line-height: 1.5;">
                                            J'accepte les 
                                            <a href="/terms" target="_blank" style="color: #667eea; font-weight: 600; text-decoration: none;">Conditions d'utilisation</a>
                                            et la 
                                            <a href="/privacy" target="_blank" style="color: #667eea; font-weight: 600; text-decoration: none;">Politique de confidentialité</a>
                                        </span>
                                    </label>
                                </div>

                                <!-- Bouton d'inscription -->
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
                                    🚀 Créer mon compte
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

                            <!-- Lien connexion -->
                            <div style="text-align: center;">
                                <p style="color: #64748b; font-size: 0.95rem;">
                                    Vous avez déjà un compte ?
                                    <a href="/login" style="
                                        color: #667eea;
                                        font-weight: 600;
                                        text-decoration: none;
                                        transition: color 0.2s;
                                    " onmouseover="this.style.color='#764ba2'" onmouseout="this.style.color='#667eea'">
                                        Se connecter →
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

                        // Fonction pour toggle password visibility
                        window.togglePassword = function(fieldId) {
                            const field = document.getElementById(fieldId);
                            const icon = document.getElementById(fieldId + '-toggle-icon');

                            if (field.type === 'password') {
                                field.type = 'text';
                                icon.textContent = '🙈';
                            } else {
                                field.type = 'password';
                                icon.textContent = '👁️';
                            }
                        };

                        // Gestion du changement de type de compte
                        accountTypeCards.forEach(card => {
                            card.addEventListener('click', function() {
                                // Retirer l'état actif de toutes les cards
                                accountTypeCards.forEach(c => {
                                    c.classList.remove('active');
                                    c.style.borderColor = 'var(--border-color)';
                                    c.style.background = 'transparent';
                                });

                                // Activer la card cliquée
                                this.classList.add('active');
                                this.style.borderColor = '#667eea';
                                this.style.background = 'rgba(102, 126, 234, 0.05)';

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

                        // Validation du formulaire
                        form.addEventListener('submit', function(e) {
                            const password = form.password.value;
                            const passwordConfirm = form.password_confirm.value;
                            const terms = form.terms.checked;

                            if (password !== passwordConfirm) {
                                e.preventDefault();
                                alert('❌ Les mots de passe ne correspondent pas');
                                return false;
                            }

                            if (password.length < 8) {
                                e.preventDefault();
                                alert('❌ Le mot de passe doit contenir au moins 8 caractères');
                                return false;
                            }

                            if (!terms) {
                                e.preventDefault();
                                alert('❌ Vous devez accepter les conditions d\'utilisation');
                                return false;
                            }

                            // Désactiver le bouton pendant la soumission
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = '⏳ Création en cours...';
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

                    .account-type-card.active {
                        border-color: #667eea !important;
                        background: rgba(102, 126, 234, 0.05) !important;
                    }

                    .account-type-card:hover {
                        border-color: #667eea !important;
                        background: rgba(102, 126, 234, 0.03) !important;
                        transform: translateY(-2px);
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                    }

                    /* Responsive */
                    @media (max-width: 640px) {
                        div[style*="padding: var(--space-10)"] {
                            padding: var(--space-6) !important;
                        }
                    }
                    </style>