<?php
/**
 * Page Contact
 */
?>

<div class="container mt-8">
    <div style="max-width: 600px; margin: 0 auto;">
        <h1 class="mb-8 text-center">Contactez-nous</h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success mb-6">
                <?= e($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="card" style="padding: var(--space-8);">
            <form method="POST" action="/contact">
                <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">

                <div class="mb-6">
                    <label class="form-label">Nom complet</label>
                    <input 
                        type="text" 
                        name="name" 
                        class="input" 
                        required
                        placeholder="Votre nom"
                    >
                </div>

                <div class="mb-6">
                    <label class="form-label">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        class="input" 
                        required
                        placeholder="votre@email.com"
                    >
                </div>

                <div class="mb-6">
                    <label class="form-label">Sujet</label>
                    <select name="subject" class="input" required>
                        <option value="">Choisissez un sujet</option>
                        <option value="general">Question générale</option>
                        <option value="technical">Support technique</option>
                        <option value="billing">Facturation</option>
                        <option value="partnership">Partenariat</option>
                        <option value="other">Autre</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="form-label">Message</label>
                    <textarea 
                        name="message" 
                        class="input" 
                        rows="6" 
                        required
                        placeholder="Décrivez votre demande..."
                        style="resize: vertical;"
                    ></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Envoyer le message
                </button>
            </form>
        </div>

        <div class="mt-8" style="text-align: center; color: var(--text-secondary);">
            <p style="margin-bottom: var(--space-4);">
                <strong>Temps de réponse moyen :</strong> 24-48h
            </p>
            <p>
                Pour les questions urgentes, consultez notre 
                <a href="/help" style="color: var(--primary-600);">Centre d'aide</a>
            </p>
        </div>
    </div>
</div>
<style>
/* === DESIGN MAQUETTE2 — CONTACT === */
.container{background:#faf9f5;padding-top:32px!important}
h1{font-family:Georgia,serif;font-weight:400;color:#1e1208;font-size:26px}
.card{background:#fff!important;border:0.5px solid #ede8df!important;border-radius:14px!important;box-shadow:none!important}
.form-label{font-family:'Manrope',sans-serif;font-size:12px;font-weight:500;color:#1e1208}
.input{width:100%;padding:9px 14px;border:0.5px solid #ddd6c8!important;border-radius:10px!important;background:#faf9f5;font-family:'Manrope',sans-serif;font-size:13px;color:#1e1208;outline:none;box-sizing:border-box;transition:border-color 0.15s}
.input:focus{border-color:#7c6cf0!important;background:#fff;box-shadow:0 0 0 3px rgba(124,108,240,.1)}
.btn-primary{background:#7c6cf0!important;color:#fff!important;border:none!important;border-radius:8px!important;font-family:'Manrope',sans-serif!important;font-size:13px!important;font-weight:500!important;padding:10px 20px!important}
.btn-primary:hover{background:#6558d4!important}
.alert-success{background:#e4f1d8;border-radius:10px;padding:12px 16px;font-family:'Manrope',sans-serif;font-size:13px;color:#2d6a35;margin-bottom:16px}
a[style*="color: var(--primary-600)"]{color:#7c6cf0!important}
</style>
