<?php
/**
 * MARKETFLOW PRO - FORMULAIRE PRODUIT VENDEUR
 * Création et édition de produits
 * Fichier : app/views/seller/product_form.php
 */

$isEdit = $mode === 'edit';
$product = $product ?? null;
$old = $old ?? [];
?>

<div class="container" style="max-width: 900px; margin-top: var(--space-8); margin-bottom: var(--space-16);">
    
    <!-- Header -->
    <div class="mb-8">
        <h1><?= $isEdit ? 'Modifier le produit' : 'Ajouter un nouveau produit' ?></h1>
        <p style="color: var(--text-secondary); margin-top: var(--space-2);">
            <?= $isEdit ? 'Modifiez les informations de votre produit' : 'Remplissez les informations pour créer votre produit' ?>
        </p>
    </div>

    <!-- Formulaire -->
    <form 
        method="POST" 
        action="<?= $isEdit ? "/seller/products/{$product['id']}/update" : '/seller/products/store' ?>" 
        enctype="multipart/form-data"
        id="productForm"
    >
        
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

        <!-- Section 1 : Informations principales -->
        <div class="card" style="padding: var(--space-8); margin-bottom: var(--space-6);">
            
            <h2 style="font-size: 1.5rem; margin-bottom: var(--space-6); padding-bottom: var(--space-4); border-bottom: 1px solid var(--border-color);">
                📝 Informations principales
            </h2>

            <!-- Titre -->
            <div class="form-group">
                <label class="form-label" for="title">
                    Titre du produit <span style="color: var(--error);">*</span>
                </label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-input <?= isset($errors['title']) ? 'error' : '' ?>"
                    placeholder="Ex: Dashboard UI Kit Premium - 150+ Composants"
                    value="<?= $isEdit ? e($product['title']) : e($old['title'] ?? '') ?>"
                    required
                    maxlength="255"
                >
                <small style="font-size: 0.75rem; color: var(--text-tertiary); display: block; margin-top: var(--space-1);">
                    Soyez descriptif et précis (min. 10 caractères)
                </small>
                <?php if (isset($errors['title'])): ?>
                    <div class="form-error"><?= e($errors['title']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Catégorie -->
            <div class="form-group">
                <label class="form-label" for="category_id">
                    Catégorie <span style="color: var(--error);">*</span>
                </label>
                <select 
                    id="category_id" 
                    name="category_id" 
                    class="form-select <?= isset($errors['category_id']) ? 'error' : '' ?>"
                    required
                >
                    <option value="">Sélectionnez une catégorie</option>
                    <?php foreach ($categories as $cat): ?>
                    <option 
                        value="<?= $cat['id'] ?>"
                        <?= ($isEdit && $product['category_id'] == $cat['id']) || (!$isEdit && ($old['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>
                    >
                        <?= e($cat['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['category_id'])): ?>
                    <div class="form-error"><?= e($errors['category_id']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="form-label" for="description">
                    Description <span style="color: var(--error);">*</span>
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    class="form-textarea <?= isset($errors['description']) ? 'error' : '' ?>"
                    placeholder="Décrivez votre produit en détail : fonctionnalités, contenu, utilisation recommandée..."
                    rows="8"
                    required
                ><?= $isEdit ? e($product['description']) : e($old['description'] ?? '') ?></textarea>
                <small style="font-size: 0.75rem; color: var(--text-tertiary); display: block; margin-top: var(--space-1);">
                    Min. 50 caractères - Soyez complet et précis
                </small>
                <?php if (isset($errors['description'])): ?>
                    <div class="form-error"><?= e($errors['description']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Tags -->
            <div class="form-group">
                <label class="form-label" for="tags">
                    Tags (mots-clés)
                </label>
                <input 
                    type="text" 
                    id="tags" 
                    name="tags" 
                    class="form-input"
                    placeholder="Ex: ui kit, dashboard, admin, bootstrap, responsive"
                    value="<?= $isEdit ? e($product['tags_string'] ?? '') : e($old['tags'] ?? '') ?>"
                >
                <small style="font-size: 0.75rem; color: var(--text-tertiary); display: block; margin-top: var(--space-1);">
                    Séparez les tags par des virgules. Aide au référencement.
                </small>
            </div>

        </div>

        <!-- Section 2 : Prix et fichiers -->
        <div class="card" style="padding: var(--space-8); margin-bottom: var(--space-6);">
            
            <h2 style="font-size: 1.5rem; margin-bottom: var(--space-6); padding-bottom: var(--space-4); border-bottom: 1px solid var(--border-color);">
                💰 Prix et fichiers
            </h2>

            <!-- Prix -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-6);">
                
                <div class="form-group">
                    <label class="form-label" for="price">
                        Prix <span style="color: var(--error);">*</span>
                    </label>
                    <div style="position: relative;">
                        <input 
                            type="number" 
                            id="price" 
                            name="price" 
                            class="form-input <?= isset($errors['price']) ? 'error' : '' ?>"
                            placeholder="49.99"
                            step="0.01"
                            min="0"
                            value="<?= $isEdit ? $product['price'] : ($old['price'] ?? '') ?>"
                            required
                            style="padding-right: 40px;"
                        >
                        <span style="
                            position: absolute;
                            right: 15px;
                            top: 50%;
                            transform: translateY(-50%);
                            color: var(--text-tertiary);
                            font-weight: 600;
                        ">€</span>
                    </div>
                    <?php if (isset($errors['price'])): ?>
                        <div class="form-error"><?= e($errors['price']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="original_price">
                        Prix barré (optionnel)
                    </label>
                    <div style="position: relative;">
                        <input 
                            type="number" 
                            id="original_price" 
                            name="original_price" 
                            class="form-input"
                            placeholder="99.99"
                            step="0.01"
                            min="0"
                            value="<?= $isEdit ? $product['original_price'] : ($old['original_price'] ?? '') ?>"
                            style="padding-right: 40px;"
                        >
                        <span style="
                            position: absolute;
                            right: 15px;
                            top: 50%;
                            transform: translateY(-50%);
                            color: var(--text-tertiary);
                            font-weight: 600;
                        ">€</span>
                    </div>
                    <small style="font-size: 0.75rem; color: var(--text-tertiary); display: block; margin-top: var(--space-1);">
                        Pour afficher une promotion
                    </small>
                </div>

            </div>

            <!-- Type de fichier -->
            <div class="form-group">
                <label class="form-label" for="file_type">
                    Type de fichier
                </label>
                <select id="file_type" name="file_type" class="form-select">
                    <option value="">Sélectionnez...</option>
                    <option value="zip" <?= ($isEdit && $product['file_type'] == 'zip') || ($old['file_type'] ?? '') == 'zip' ? 'selected' : '' ?>>ZIP</option>
                    <option value="pdf" <?= ($isEdit && $product['file_type'] == 'pdf') || ($old['file_type'] ?? '') == 'pdf' ? 'selected' : '' ?>>PDF</option>
                    <option value="psd" <?= ($isEdit && $product['file_type'] == 'psd') || ($old['file_type'] ?? '') == 'psd' ? 'selected' : '' ?>>PSD</option>
                    <option value="ai" <?= ($isEdit && $product['file_type'] == 'ai') || ($old['file_type'] ?? '') == 'ai' ? 'selected' : '' ?>>AI</option>
                    <option value="sketch" <?= ($isEdit && $product['file_type'] == 'sketch') || ($old['file_type'] ?? '') == 'sketch' ? 'selected' : '' ?>>Sketch</option>
                    <option value="figma" <?= ($isEdit && $product['file_type'] == 'figma') || ($old['file_type'] ?? '') == 'figma' ? 'selected' : '' ?>>Figma</option>
                    <option value="xd" <?= ($isEdit && $product['file_type'] == 'xd') || ($old['file_type'] ?? '') == 'xd' ? 'selected' : '' ?>>Adobe XD</option>
                </select>
            </div>

            <!-- Image principale (Thumbnail) -->
            <div class="form-group">
                <label class="form-label" for="thumbnail">
                    Image principale <?= !$isEdit ? '<span style="color: var(--error);">*</span>' : '' ?>
                </label>
                
                <?php if ($isEdit && $product['thumbnail']): ?>
                <div style="margin-bottom: var(--space-4);">
                    <img 
                        src="<?= e($product['thumbnail']) ?>" 
                        alt="Image actuelle"
                        style="max-width: 300px; border-radius: var(--radius); box-shadow: var(--shadow);"
                    >
                    <p style="font-size: 0.875rem; color: var(--text-tertiary); margin-top: var(--space-2);">
                        Image actuelle (laissez vide pour garder)
                    </p>
                </div>
                <?php endif; ?>

                <input 
                    type="file" 
                    id="thumbnail" 
                    name="thumbnail" 
                    class="form-input"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                    <?= !$isEdit ? 'required' : '' ?>
                    onchange="previewImage(this, 'thumbnailPreview')"
                >
                <div id="thumbnailPreview" style="margin-top: var(--space-4);"></div>
                <small style="font-size: 0.75rem; color: var(--text-tertiary); display: block; margin-top: var(--space-1);">
                    Format: JPG, PNG, WEBP - Max 5MB - Dimensions recommandées: 1200x800px
                </small>
                <?php if (isset($errors['thumbnail'])): ?>
                    <div class="form-error"><?= e($errors['thumbnail']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Fichier produit -->
            <div class="form-group">
                <label class="form-label" for="product_file">
                    Fichier produit <?= !$isEdit ? '<span style="color: var(--error);">*</span>' : '' ?>
                </label>
                
                <?php if ($isEdit && $product['file_path']): ?>
                <div style="
                    padding: var(--space-4);
                    background: var(--success-light);
                    border-radius: var(--radius);
                    margin-bottom: var(--space-4);
                    color: #065f46;
                ">
                    ✓ Fichier actuel en place (<?= number_format($product['file_size'] / 1024, 1) ?> MB)
                    <br>
                    <small>Téléchargez un nouveau fichier pour le remplacer</small>
                </div>
                <?php endif; ?>

                <input 
                    type="file" 
                    id="product_file" 
                    name="product_file" 
                    class="form-input"
                    accept=".zip,.pdf,.psd,.ai,.sketch,.fig,.xd"
                    <?= !$isEdit ? 'required' : '' ?>
                >
                <small style="font-size: 0.75rem; color: var(--text-tertiary); display: block; margin-top: var(--space-1);">
                    Format: ZIP, PDF, PSD, AI, Sketch, Figma, XD - Max 50MB
                </small>
                <?php if (isset($errors['product_file'])): ?>
                    <div class="form-error"><?= e($errors['product_file']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Galerie d'images (optionnel) -->
            <div class="form-group">
                <label class="form-label" for="gallery">
                    Images supplémentaires (optionnel)
                </label>
                
                <?php if ($isEdit && !empty($product['images'])): ?>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--space-3); margin-bottom: var(--space-4);">
                    <?php foreach ($product['images'] as $img): ?>
                    <div style="position: relative;">
                        <img 
                            src="<?= e($img['image_url']) ?>" 
                            style="width: 100%; aspect-ratio: 16/10; object-fit: cover; border-radius: var(--radius);"
                        >
                        <button 
                            type="button"
                            onclick="deleteImage(<?= $img['id'] ?>)"
                            style="
                                position: absolute;
                                top: 5px;
                                right: 5px;
                                background: var(--error);
                                color: white;
                                border: none;
                                border-radius: 50%;
                                width: 25px;
                                height: 25px;
                                cursor: pointer;
                                font-size: 0.875rem;
                            ">
                            ✕
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <input 
                    type="file" 
                    id="gallery" 
                    name="gallery[]" 
                    class="form-input"
                    accept="image/jpeg,image/png,image/webp"
                    multiple
                    onchange="previewGallery(this)"
                >
                <div id="galleryPreview" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--space-3); margin-top: var(--space-4);"></div>
                <small style="font-size: 0.75rem; color: var(--text-tertiary); display: block; margin-top: var(--space-1);">
                    Max 6 images - JPG, PNG, WEBP - 5MB par image
                </small>
            </div>

        </div>

        <!-- Section 3 : Informations complémentaires -->
        <div class="card" style="padding: var(--space-8); margin-bottom: var(--space-6);">
            
            <h2 style="font-size: 1.5rem; margin-bottom: var(--space-6); padding-bottom: var(--space-4); border-bottom: 1px solid var(--border-color);">
                🔗 Informations complémentaires
            </h2>

            <!-- URL Démo -->
            <div class="form-group">
                <label class="form-label" for="demo_url">
                    URL de démonstration (optionnel)
                </label>
                <input 
                    type="url" 
                    id="demo_url" 
                    name="demo_url" 
                    class="form-input"
                    placeholder="https://demo.example.com"
                    value="<?= $isEdit ? e($product['demo_url']) : e($old['demo_url'] ?? '') ?>"
                >
                <small style="font-size: 0.75rem; color: var(--text-tertiary); display: block; margin-top: var(--space-1);">
                    Lien vers une démo en ligne de votre produit
                </small>
            </div>

        </div>

        <!-- Boutons d'action -->
        <div class="flex gap-4" style="justify-content: flex-end;">
            <a href="/seller/products" class="btn btn-secondary">
                Annuler
            </a>
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <?= $isEdit ? '💾 Mettre à jour le produit' : '✨ Créer le produit' ?>
            </button>
        </div>

    </form>

</div>

<!-- JavaScript -->
<script>
// Preview image principale
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <img 
                    src="${e.target.result}" 
                    style="max-width: 400px; border-radius: var(--radius); box-shadow: var(--shadow);"
                    alt="Aperçu"
                >
            `;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Preview galerie
function previewGallery(input) {
    const preview = document.getElementById('galleryPreview');
    preview.innerHTML = '';
    
    if (input.files) {
        Array.from(input.files).slice(0, 6).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.innerHTML = `
                    <img 
                        src="${e.target.result}" 
                        style="width: 100%; aspect-ratio: 16/10; object-fit: cover; border-radius: var(--radius);"
                    >
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
}

// Validation du formulaire
const form = document.getElementById('productForm');
const submitBtn = document.getElementById('submitBtn');

form.addEventListener('submit', function(e) {
    // Désactiver le bouton pendant la soumission
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ Envoi en cours...';
    submitBtn.style.opacity = '0.6';
    
    // Validation client
    const title = form.title.value.trim();
    const description = form.description.value.trim();
    const price = parseFloat(form.price.value);
    
    let errors = [];
    
    if (title.length < 10) {
        errors.push('Le titre doit contenir au moins 10 caractères');
    }
    
    if (description.length < 50) {
        errors.push('La description doit contenir au moins 50 caractères');
    }
    
    if (price <= 0 || isNaN(price)) {
        errors.push('Le prix doit être supérieur à 0');
    }
    
    if (errors.length > 0) {
        e.preventDefault();
        alert('Erreurs détectées :\n\n' + errors.join('\n'));
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<?= $isEdit ? "💾 Mettre à jour le produit" : "✨ Créer le produit" ?>';
        submitBtn.style.opacity = '1';
        return false;
    }
    
    // Vérifier la taille des fichiers
    const thumbnail = form.thumbnail.files[0];
    const productFile = form.product_file.files[0];
    
    if (thumbnail && thumbnail.size > 5 * 1024 * 1024) {
        e.preventDefault();
        alert('L\'image principale ne doit pas dépasser 5MB');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<?= $isEdit ? "💾 Mettre à jour le produit" : "✨ Créer le produit" ?>';
        submitBtn.style.opacity = '1';
        return false;
    }
    
    if (productFile && productFile.size > 50 * 1024 * 1024) {
        e.preventDefault();
        alert('Le fichier produit ne doit pas dépasser 50MB');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<?= $isEdit ? "💾 Mettre à jour le produit" : "✨ Créer le produit" ?>';
        submitBtn.style.opacity = '1';
        return false;
    }
    
    // Si tout est OK, montrer un indicateur de chargement
    MarketFlow.LoadingSpinner.show();
});

// Compteur de caractères pour description
const descTextarea = document.getElementById('description');
if (descTextarea) {
    const counter = document.createElement('div');
    counter.style.cssText = 'font-size: 0.75rem; color: var(--text-tertiary); margin-top: var(--space-1); text-align: right;';
    descTextarea.parentElement.appendChild(counter);
    
    function updateCounter() {
        const length = descTextarea.value.length;
        counter.textContent = `${length} caractères`;
        counter.style.color = length < 50 ? 'var(--error)' : 'var(--success)';
    }
    
    descTextarea.addEventListener('input', updateCounter);
    updateCounter();
}

// Calculer la réduction automatique
const priceInput = document.getElementById('price');
const originalPriceInput = document.getElementById('original_price');

if (priceInput && originalPriceInput) {
    function updateDiscount() {
        const price = parseFloat(priceInput.value);
        const originalPrice = parseFloat(originalPriceInput.value);
        
        if (price && originalPrice && originalPrice > price) {
            const discount = Math.round((1 - price / originalPrice) * 100);
            originalPriceInput.parentElement.querySelector('small').innerHTML = 
                `Pour afficher une promotion <strong style="color: var(--error);">(-${discount}%)</strong>`;
        }
    }
    
    priceInput.addEventListener('input', updateDiscount);
    originalPriceInput.addEventListener('input', updateDiscount);
}
</script>

<style>
/* Amélioration visuelle des inputs file */
input[type="file"] {
    cursor: pointer;
}

input[type="file"]:hover {
    border-color: var(--primary-600);
}

/* Animation du formulaire */
.card {
    animation: fadeIn 0.5s ease-out;
}

/* Style pour les erreurs */
.form-input.error,
.form-textarea.error,
.form-select.error {
    border-color: var(--error);
}
</style>