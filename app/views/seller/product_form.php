<?php
/**
 * MARKETFLOW PRO - FORMULAIRE PRODUIT VENDEUR (VERSION AVEC LICENCE)
 * Création et édition de produits
 * Fichier : app/views/seller/product_form.php
 */

$isEdit = $mode === 'edit';
$product = $product ?? null;
$old = $old ?? [];
?>

<div style="
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
    padding: var(--space-8) 0;
">
    <div class="container" style="max-width: 900px;">
        
        <!-- Header avec dégradé -->
        <div style="
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #8b5cf6 100%);
            border-radius: 24px;
            padding: var(--space-10);
            margin-bottom: var(--space-8);
            color: white;
            box-shadow: 0 20px 60px rgba(99, 102, 241, 0.3);
            position: relative;
            overflow: hidden;
        ">
            <!-- Cercle décoratif -->
            <div style="
                position: absolute;
                top: -50px;
                right: -50px;
                width: 200px;
                height: 200px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                filter: blur(40px);
            "></div>
            
            <div style="position: relative; z-index: 1;">
                <h1 style="
                    font-size: 2.5rem;
                    margin-bottom: var(--space-3);
                    color: white;
                    font-weight: 800;
                ">
                    <?= $isEdit ? '✏️ Modifier le produit' : '✨ Nouveau produit' ?>
                </h1>
                <p style="
                    font-size: 1.125rem;
                    color: rgba(255, 255, 255, 0.9);
                    margin: 0;
                ">
                    <?= $isEdit ? 'Modifiez les informations de votre produit' : 'Créez un produit exceptionnel pour vos clients' ?>
                </p>
            </div>
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
            <div style="
                background: white;
                border-radius: 20px;
                padding: var(--space-8);
                margin-bottom: var(--space-6);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                border: 1px solid rgba(99, 102, 241, 0.1);
            ">
                
                <div style="
                    display: flex;
                    align-items: center;
                    gap: var(--space-3);
                    margin-bottom: var(--space-6);
                    padding-bottom: var(--space-4);
                    border-bottom: 2px solid;
                    border-image: linear-gradient(135deg, #3b82f6, #8b5cf6) 1;
                ">
                    <div style="
                        width: 48px;
                        height: 48px;
                        background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
                        border-radius: 12px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 1.5rem;
                    ">
                        📝
                    </div>
                    <h2 style="
                        font-size: 1.5rem;
                        margin: 0;
                        color: #1e293b;
                    ">
                        Informations principales
                    </h2>
                </div>

                <!-- Titre -->
                <div style="margin-bottom: var(--space-6);">
                    <label style="
                        display: block;
                        font-weight: 600;
                        margin-bottom: var(--space-2);
                        color: #1e293b;
                        font-size: 0.95rem;
                    " for="title">
                        Titre du produit <span style="color: #ef4444;">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        style="
                            width: 100%;
                            padding: var(--space-4);
                            border: 2px solid <?= isset($errors['title']) ? '#ef4444' : '#e2e8f0' ?>;
                            border-radius: 12px;
                            font-size: 1rem;
                            transition: all 0.3s;
                        "
                        placeholder="Ex: Dashboard UI Kit Premium - 150+ Composants"
                        value="<?= $isEdit ? e($product['title']) : e($old['title'] ?? '') ?>"
                        required
                        maxlength="255"
                        onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 4px rgba(99, 102, 241, 0.1)'"
                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"
                    >
                    <small style="
                        font-size: 0.75rem;
                        color: #64748b;
                        display: block;
                        margin-top: var(--space-1);
                    ">
                        Soyez descriptif et précis (min. 10 caractères)
                    </small>
                    <?php if (isset($errors['title'])): ?>
                        <div style="color: #ef4444; font-size: 0.875rem; margin-top: var(--space-2);">
                            <?= e($errors['title']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Catégorie -->
                <div style="margin-bottom: var(--space-6);">
                    <label style="
                        display: block;
                        font-weight: 600;
                        margin-bottom: var(--space-2);
                        color: #1e293b;
                        font-size: 0.95rem;
                    " for="category_id">
                        Catégorie <span style="color: #ef4444;">*</span>
                    </label>
                    <select 
                        id="category_id" 
                        name="category_id" 
                        style="
                            width: 100%;
                            padding: var(--space-4);
                            border: 2px solid <?= isset($errors['category_id']) ? '#ef4444' : '#e2e8f0' ?>;
                            border-radius: 12px;
                            font-size: 1rem;
                            transition: all 0.3s;
                            background: white;
                        "
                        required
                        onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 4px rgba(99, 102, 241, 0.1)'"
                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"
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
                        <div style="color: #ef4444; font-size: 0.875rem; margin-top: var(--space-2);">
                            <?= e($errors['category_id']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <div style="margin-bottom: var(--space-6);">
                    <label style="
                        display: block;
                        font-weight: 600;
                        margin-bottom: var(--space-2);
                        color: #1e293b;
                        font-size: 0.95rem;
                    " for="description">
                        Description <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        style="
                            width: 100%;
                            padding: var(--space-4);
                            border: 2px solid <?= isset($errors['description']) ? '#ef4444' : '#e2e8f0' ?>;
                            border-radius: 12px;
                            font-size: 1rem;
                            transition: all 0.3s;
                            resize: vertical;
                            font-family: inherit;
                        "
                        placeholder="Décrivez votre produit en détail : fonctionnalités, contenu, utilisation recommandée..."
                        rows="8"
                        required
                        onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 4px rgba(99, 102, 241, 0.1)'"
                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"
                    ><?= $isEdit ? e($product['description']) : e($old['description'] ?? '') ?></textarea>
                    <small style="
                        font-size: 0.75rem;
                        color: #64748b;
                        display: block;
                        margin-top: var(--space-1);
                    ">
                        Min. 50 caractères - Soyez complet et précis
                    </small>
                    <?php if (isset($errors['description'])): ?>
                        <div style="color: #ef4444; font-size: 0.875rem; margin-top: var(--space-2);">
                            <?= e($errors['description']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tags -->
                <div>
                    <label style="
                        display: block;
                        font-weight: 600;
                        margin-bottom: var(--space-2);
                        color: #1e293b;
                        font-size: 0.95rem;
                    " for="tags">
                        Tags (mots-clés)
                    </label>
                    <input 
                        type="text" 
                        id="tags" 
                        name="tags" 
                        style="
                            width: 100%;
                            padding: var(--space-4);
                            border: 2px solid #e2e8f0;
                            border-radius: 12px;
                            font-size: 1rem;
                            transition: all 0.3s;
                        "
                        placeholder="Ex: ui kit, dashboard, admin, bootstrap, responsive"
                        value="<?= $isEdit ? e($product['tags_string'] ?? '') : e($old['tags'] ?? '') ?>"
                        onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 4px rgba(99, 102, 241, 0.1)'"
                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"
                    >
                    <small style="
                        font-size: 0.75rem;
                        color: #64748b;
                        display: block;
                        margin-top: var(--space-1);
                    ">
                        Séparez les tags par des virgules. Aide au référencement.
                    </small>
                </div>

            </div>

            <!-- Section 2 : Prix et Licence -->
            <div style="
                background: white;
                border-radius: 20px;
                padding: var(--space-8);
                margin-bottom: var(--space-6);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                border: 1px solid rgba(99, 102, 241, 0.1);
            ">
                
                <div style="
                    display: flex;
                    align-items: center;
                    gap: var(--space-3);
                    margin-bottom: var(--space-6);
                    padding-bottom: var(--space-4);
                    border-bottom: 2px solid;
                    border-image: linear-gradient(135deg, #3b82f6, #8b5cf6) 1;
                ">
                    <div style="
                        width: 48px;
                        height: 48px;
                        background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
                        border-radius: 12px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 1.5rem;
                    ">
                        💰
                    </div>
                    <h2 style="
                        font-size: 1.5rem;
                        margin: 0;
                        color: #1e293b;
                    ">
                        Prix et Licence
                    </h2>
                </div>

                <!-- Type de licence -->
                <div style="margin-bottom: var(--space-6);">
                    <label style="
                        display: block;
                        font-weight: 600;
                        margin-bottom: var(--space-3);
                        color: #1e293b;
                        font-size: 0.95rem;
                    ">
                        Type de licence <span style="color: #ef4444;">*</span>
                    </label>
                    
                    <div style="
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: var(--space-4);
                    ">
                        <!-- Licence Single -->
                        <label class="license-card" data-license="single" style="
                            cursor: pointer;
                            border: 2px solid #e2e8f0;
                            border-radius: 16px;
                            padding: var(--space-5);
                            transition: all 0.3s;
                            position: relative;
                            <?= (!$isEdit && !isset($old['license_type'])) || ($isEdit && $product['license_type'] == 'single') || ($old['license_type'] ?? '') == 'single' ? 'border-color: #6366f1; background: rgba(99, 102, 241, 0.05);' : '' ?>
                        ">
                            <input 
                                type="radio" 
                                name="license_type" 
                                value="single" 
                                <?= (!$isEdit && !isset($old['license_type'])) || ($isEdit && $product['license_type'] == 'single') || ($old['license_type'] ?? '') == 'single' ? 'checked' : '' ?>
                                style="display: none;"
                            >
                            <div style="
                                width: 40px;
                                height: 40px;
                                background: linear-gradient(135deg, #3b82f6, #6366f1);
                                border-radius: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 1.25rem;
                                margin-bottom: var(--space-3);
                            ">
                                👤
                            </div>
                            <h3 style="
                                font-size: 1.125rem;
                                margin-bottom: var(--space-2);
                                color: #1e293b;
                            ">
                                Licence Simple
                            </h3>
                            <p style="
                                font-size: 0.875rem;
                                color: #64748b;
                                margin: 0;
                                line-height: 1.6;
                            ">
                                Usage personnel ou 1 projet client
                            </p>
                            <ul style="
                                font-size: 0.8rem;
                                color: #64748b;
                                margin-top: var(--space-3);
                                padding-left: var(--space-5);
                            ">
                                <li>✓ 1 utilisation</li>
                                <li>✓ Usage commercial</li>
                                <li>✗ Revente interdite</li>
                            </ul>
                        </label>

                        <!-- Licence Extended -->
                        <label class="license-card" data-license="extended" style="
                            cursor: pointer;
                            border: 2px solid #e2e8f0;
                            border-radius: 16px;
                            padding: var(--space-5);
                            transition: all 0.3s;
                            position: relative;
                            <?= ($isEdit && $product['license_type'] == 'extended') || ($old['license_type'] ?? '') == 'extended' ? 'border-color: #6366f1; background: rgba(99, 102, 241, 0.05);' : '' ?>
                        ">
                            <input 
                                type="radio" 
                                name="license_type" 
                                value="extended"
                                <?= ($isEdit && $product['license_type'] == 'extended') || ($old['license_type'] ?? '') == 'extended' ? 'checked' : '' ?>
                                style="display: none;"
                            >
                            <div style="
                                width: 40px;
                                height: 40px;
                                background: linear-gradient(135deg, #8b5cf6, #a78bfa);
                                border-radius: 10px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                font-size: 1.25rem;
                                margin-bottom: var(--space-3);
                            ">
                                👥
                            </div>
                            <!--<div style="
                                position: absolute;
                                top: var(--space-3);
                                right: var(--space-3);
                                background: linear-gradient(135deg, #f59e0b, #d97706);
                                color: white;
                                padding: 0.25rem 0.5rem;
                                border-radius: 6px;
                                font-size: 0.7rem;
                                font-weight: 700;
                            ">
                                RECOMMANDÉ
                            </div>-->
                            <h3 style="
                                font-size: 1.125rem;
                                margin-bottom: var(--space-2);
                                color: #1e293b;
                            ">
                                Licence Étendue
                            </h3>
                            <p style="
                                font-size: 0.875rem;
                                color: #64748b;
                                margin: 0;
                                line-height: 1.6;
                            ">
                                Usage illimité pour clients multiples
                            </p>
                            <ul style="
                                font-size: 0.8rem;
                                color: #64748b;
                                margin-top: var(--space-3);
                                padding-left: var(--space-5);
                            ">
                                <li>✓ Projets illimités</li>
                                <li>✓ Usage commercial</li>
                                <li>✓ Fichiers sources inclus</li>
                            </ul>
                        </label>
                    </div>
                </div>

                <!-- Prix -->
                <div style="
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: var(--space-6);
                    margin-bottom: var(--space-6);
                ">
                    
                    <div>
                        <label style="
                            display: block;
                            font-weight: 600;
                            margin-bottom: var(--space-2);
                            color: #1e293b;
                            font-size: 0.95rem;
                        " for="price">
                            Prix <span style="color: #ef4444;">*</span>
                        </label>
                        <div style="position: relative;">
                            <input 
                                type="number" 
                                id="price" 
                                name="price" 
                                style="
                                    width: 100%;
                                    padding: var(--space-4);
                                    padding-right: 50px;
                                    border: 2px solid <?= isset($errors['price']) ? '#ef4444' : '#e2e8f0' ?>;
                                    border-radius: 12px;
                                    font-size: 1rem;
                                    transition: all 0.3s;
                                "
                                placeholder="49.99"
                                step="0.01"
                                min="0"
                                autocomplete="off"
                                value="<?= $isEdit ? $product['price'] : ($old['price'] ?? '') ?>"
                                required
                                onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 4px rgba(99, 102, 241, 0.1)'"
                                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"
                            >
                            <span style="
                                position: absolute;
                                right: 15px;
                                top: 50%;
                                transform: translateY(-50%);
                                color: #64748b;
                                font-weight: 700;
                                font-size: 1.125rem;
                            ">€</span>
                        </div>
                        <?php if (isset($errors['price'])): ?>
                            <div style="color: #ef4444; font-size: 0.875rem; margin-top: var(--space-2);">
                                <?= e($errors['price']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label style="
                            display: block;
                            font-weight: 600;
                            margin-bottom: var(--space-2);
                            color: #1e293b;
                            font-size: 0.95rem;
                        " for="original_price">
                            Prix barré (optionnel)
                        </label>
                        <div style="position: relative;">
                            <input 
                                type="number" 
                                id="original_price" 
                                name="original_price" 
                                style="
                                    width: 100%;
                                    padding: var(--space-4);
                                    padding-right: 50px;
                                    border: 2px solid #e2e8f0;
                                    border-radius: 12px;
                                    font-size: 1rem;
                                    transition: all 0.3s;
                                "
                                placeholder="99.99"
                                step="0.01"
                                min="0"
                                autocomplete="off"
                                value="<?= $isEdit ? $product['original_price'] : ($old['original_price'] ?? '') ?>"
                                onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 4px rgba(99, 102, 241, 0.1)'"
                                onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"
                            >
                            <span style="
                                position: absolute;
                                right: 15px;
                                top: 50%;
                                transform: translateY(-50%);
                                color: #64748b;
                                font-weight: 700;
                                font-size: 1.125rem;
                            ">€</span>
                        </div>
                        <small style="
                            font-size: 0.75rem;
                            color: #64748b;
                            display: block;
                            margin-top: var(--space-1);
                        " id="discountInfo">
                            Pour afficher une promotion
                        </small>
                    </div>

                </div>

            </div>

            <!-- Section 3 : Fichiers -->
            <div style="
                background: white;
                border-radius: 20px;
                padding: var(--space-8);
                margin-bottom: var(--space-6);
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                border: 1px solid rgba(99, 102, 241, 0.1);
            ">
                
                <div style="
                    display: flex;
                    align-items: center;
                    gap: var(--space-3);
                    margin-bottom: var(--space-6);
                    padding-bottom: var(--space-4);
                    border-bottom: 2px solid;
                    border-image: linear-gradient(135deg, #3b82f6, #8b5cf6) 1;
                ">
                    <div style="
                        width: 48px;
                        height: 48px;
                        background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
                        border-radius: 12px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 1.5rem;
                    ">
                        📦
                    </div>
                    <h2 style="
                        font-size: 1.5rem;
                        margin: 0;
                        color: #1e293b;
                    ">
                        Fichiers du produit
                    </h2>
                </div>

                <!-- Type de fichier -->
                <div style="margin-bottom: var(--space-6);">
                    <label style="
                        display: block;
                        font-weight: 600;
                        margin-bottom: var(--space-2);
                        color: #1e293b;
                        font-size: 0.95rem;
                    " for="file_type">
                        Type de fichier
                    </label>
                    <select 
                        id="file_type" 
                        name="file_type" 
                        style="
                            width: 100%;
                            padding: var(--space-4);
                            border: 2px solid #e2e8f0;
                            border-radius: 12px;
                            font-size: 1rem;
                            transition: all 0.3s;
                            background: white;
                        "
                        onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 4px rgba(99, 102, 241, 0.1)'"
                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"
                    >
                        <option value="">Sélectionnez...</option>
                        <option value="zip" <?= ($isEdit && $product['file_type'] == 'zip') || ($old['file_type'] ?? '') == 'zip' ? 'selected' : '' ?>>ZIP</option>
                        <option value="pdf" <?= ($isEdit && $product['file_type'] == 'pdf') || ($old['file_type'] ?? '') == 'pdf' ? 'selected' : '' ?>>PDF</option>
                        <option value="psd" <?= ($isEdit && $product['file_type'] == 'psd') || ($old['file_type'] ?? '') == 'psd' ? 'selected' : '' ?>>PSD</option>
                        <option value="ai" <?= ($isEdit && $product['file_type'] == 'ai') || ($old['file_type'] ?? '') == 'ai' ? 'selected' : '' ?>>AI</option>
                        <option value="sketch" <?= ($isEdit && $product['file_type'] == 'sketch') || ($old['file_type'] ?? '') == 'sketch' ? 'selected' : '' ?>>Sketch</option>
                        <option value="figma" <?= ($isEdit && $product['file_type'] == 'figma') || ($old['file_type'] ?? '') == 'figma' ? 'selected' : '' ?>>Figma</option>
                        <option value="xd" <?= ($isEdit && $product['file_type'] == 'xd') || ($old['file_type'] ?? '') == 'xd' ? 'selected' : '' ?>>Adobe XD</option>
                        <option value="jpg" <?= ($isEdit && $product['file_type'] == 'jpg') || ($old['file_type'] ?? '') == 'jpg' ? 'selected' : '' ?>>JPEG/JPG</option>
                        <option value="png" <?= ($isEdit && $product['file_type'] == 'png') || ($old['file_type'] ?? '') == 'png' ? 'selected' : '' ?>>PNG</option>
                        <option value="gif" <?= ($isEdit && $product['file_type'] == 'gif') || ($old['file_type'] ?? '') == 'gif' ? 'selected' : '' ?>>GIF</option>
                        <option value="svg" <?= ($isEdit && $product['file_type'] == 'svg') || ($old['file_type'] ?? '') == 'svg' ? 'selected' : '' ?>>SVG</option>
                        <option value="mp4" <?= ($isEdit && $product['file_type'] == 'mp4') || ($old['file_type'] ?? '') == 'mp4' ? 'selected' : '' ?>>Vidéo MP4</option>
                    </select>
                </div>

                <!-- Image principale -->
                <div style="margin-bottom: var(--space-6);">
                    <label style="
                        display: block;
                        font-weight: 600;
                        margin-bottom: var(--space-2);
                        color: #1e293b;
                        font-size: 0.95rem;
                    " for="thumbnail">
                        Image principale <?= !$isEdit ? '<span style="color: #ef4444;">*</span>' : '' ?>
                    </label>
                    
                    <?php if ($isEdit && $product['thumbnail']): ?>
                    <div style="margin-bottom: var(--space-4);">
                        <img 
                            src="<?= e($product['thumbnail']) ?>" 
                            alt="Image actuelle"
                            style="
                                max-width: 400px;
                                border-radius: 16px;
                                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
                            "
                        >
                        <p style="
                            font-size: 0.875rem;
                            color: #64748b;
                            margin-top: var(--space-2);
                        ">
                            Image actuelle (laissez vide pour garder)
                        </p>
                    </div>
                    <?php endif; ?>

                    <div style="
                        border: 2px dashed #cbd5e1;
                        border-radius: 16px;
                        padding: var(--space-8);
                        text-align: center;
                        background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
                        transition: all 0.3s;
                        cursor: pointer;
                    " 
                    onclick="document.getElementById('thumbnail').click()"
                    onmouseover="this.style.borderColor='#6366f1'; this.style.background='linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)'"
                    onmouseout="this.style.borderColor='#cbd5e1'; this.style.background='linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%)'">
                        <div style="font-size: 3rem; margin-bottom: var(--space-3);">📷</div>
                        <p style="
                            font-weight: 600;
                            color: #1e293b;
                            margin-bottom: var(--space-2);
                        ">
                            Cliquez pour choisir une image
                        </p>
                        <p style="
                            font-size: 0.875rem;
                            color: #64748b;
                            margin: 0;
                        ">
                            JPG, PNG, WEBP - Max 5MB - 1200x800px recommandé
                        </p>
                    </div>
                    
                    <input 
                        type="file" 
                        id="thumbnail" 
                        name="thumbnail" 
                        accept="image/jpeg,image/png,image/webp,image/gif"
                        <?= !$isEdit ? 'required' : '' ?>
                        onchange="previewImage(this, 'thumbnailPreview')"
                        style="display: none;"
                    >
                    <div id="thumbnailPreview" style="margin-top: var(--space-4);"></div>
                    
                    <?php if (isset($errors['thumbnail'])): ?>
                        <div style="color: #ef4444; font-size: 0.875rem; margin-top: var(--space-2);">
                            <?= e($errors['thumbnail']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Fichier produit -->
                <div style="margin-bottom: var(--space-6);">
                    <label style="
                        display: block;
                        font-weight: 600;
                        margin-bottom: var(--space-2);
                        color: #1e293b;
                        font-size: 0.95rem;
                    " for="product_file">
                        Fichier produit <?= !$isEdit ? '<span style="color: #ef4444;">*</span>' : '' ?>
                    </label>
                    
                    <?php if ($isEdit && $product['file_path']): ?>
                    <div style="
                        padding: var(--space-4);
                        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
                        border-radius: 12px;
                        margin-bottom: var(--space-4);
                        color: #065f46;
                        display: flex;
                        align-items: center;
                        gap: var(--space-3);
                    ">
                        <div style="
                            width: 40px;
                            height: 40px;
                            background: white;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 1.25rem;
                        ">✓</div>
                        <div>
                            <strong>Fichier actuel en place</strong><br>
                            <small>Taille: <?= number_format($product['file_size'] / 1024, 1) ?> MB - Téléchargez un nouveau fichier pour le remplacer</small>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div style="
                        border: 2px dashed #cbd5e1;
                        border-radius: 16px;
                        padding: var(--space-8);
                        text-align: center;
                        background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
                        transition: all 0.3s;
                        cursor: pointer;
                    "
                    onclick="document.getElementById('product_file').click()"
                    onmouseover="this.style.borderColor='#6366f1'; this.style.background='linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%)'"
                    onmouseout="this.style.borderColor='#cbd5e1'; this.style.background='linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%)'">
                        <div style="font-size: 3rem; margin-bottom: var(--space-3);">📦</div>
                        <p style="
                            font-weight: 600;
                            color: #1e293b;
                            margin-bottom: var(--space-2);
                        ">
                            Cliquez pour choisir votre fichier
                        </p>
                        <p style="
                            font-size: 0.875rem;
                            color: #64748b;
                            margin: 0;
                        ">
                            ZIP, PDF, PSD, AI, Sketch, Figma, XD - Max 50MB
                        </p>
                    </div>
                    
                    <input 
                        type="file" 
                        id="product_file" 
                        name="product_file" 
                        accept=".zip,.pdf,.psd,.ai,.sketch,.fig,.xd"
                        <?= !$isEdit ? 'required' : '' ?>
                        style="display: none;"
                    >
                    
                    <?php if (isset($errors['product_file'])): ?>
                        <div style="color: #ef4444; font-size: 0.875rem; margin-top: var(--space-2);">
                            <?= e($errors['product_file']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- URL Démo -->
                <div>
                    <label style="
                        display: block;
                        font-weight: 600;
                        margin-bottom: var(--space-2);
                        color: #1e293b;
                        font-size: 0.95rem;
                    " for="demo_url">
                        URL de démonstration (optionnel)
                    </label>
                    <input 
                        type="url" 
                        id="demo_url" 
                        name="demo_url" 
                        style="
                            width: 100%;
                            padding: var(--space-4);
                            border: 2px solid #e2e8f0;
                            border-radius: 12px;
                            font-size: 1rem;
                            transition: all 0.3s;
                        "
                        placeholder="https://demo.example.com"
                        value="<?= $isEdit ? e($product['demo_url']) : e($old['demo_url'] ?? '') ?>"
                        onfocus="this.style.borderColor='#6366f1'; this.style.boxShadow='0 0 0 4px rgba(99, 102, 241, 0.1)'"
                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"
                    >
                    <small style="
                        font-size: 0.75rem;
                        color: #64748b;
                        display: block;
                        margin-top: var(--space-1);
                    ">
                        Lien vers une démo en ligne de votre produit
                    </small>
                </div>

            </div>

            <!-- Boutons d'action -->
            <div style="
                display: flex;
                gap: var(--space-4);
                justify-content: flex-end;
            ">
                <a href="/seller/products" style="
                    padding: var(--space-4) var(--space-8);
                    border-radius: 12px;
                    font-weight: 600;
                    background: white;
                    color: #64748b;
                    border: 2px solid #e2e8f0;
                    text-decoration: none;
                    transition: all 0.3s;
                    display: inline-block;
                "
                onmouseover="this.style.borderColor='#cbd5e1'; this.style.background='#f8fafc'"
                onmouseout="this.style.borderColor='#e2e8f0'; this.style.background='white'">
                    Annuler
                </a>
                <button type="submit" id="submitBtn" style="
                    padding: var(--space-4) var(--space-8);
                    background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #8b5cf6 100%);
                    color: white;
                    border: none;
                    border-radius: 12px;
                    font-size: 1rem;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s;
                    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
                "
                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(99, 102, 241, 0.5)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(99, 102, 241, 0.4)'">
                    <?= $isEdit ? '💾 Mettre à jour le produit' : '✨ Créer le produit' ?>
                </button>
            </div>

        </form>

    </div>
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
                <div style="
                    padding: var(--space-4);
                    background: white;
                    border-radius: 16px;
                    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
                ">
                    <img 
                        src="${e.target.result}" 
                        style="
                            max-width: 100%;
                            border-radius: 12px;
                        "
                        alt="Aperçu"
                    >
                    <p style="
                        text-align: center;
                        margin-top: var(--space-3);
                        color: #10b981;
                        font-weight: 600;
                    ">
                        ✓ Image chargée avec succès
                    </p>
                </div>
            `;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Gestion des cartes de licence
const licenseCards = document.querySelectorAll('.license-card');
licenseCards.forEach(card => {
    card.addEventListener('click', function() {
        // Retirer la sélection de toutes les cartes
        licenseCards.forEach(c => {
            c.style.borderColor = '#e2e8f0';
            c.style.background = 'transparent';
        });
        
        // Sélectionner la carte cliquée
        this.style.borderColor = '#6366f1';
        this.style.background = 'rgba(99, 102, 241, 0.05)';
        
        // Cocher le radio
        this.querySelector('input[type="radio"]').checked = true;
    });
});

// Validation du formulaire
const form = document.getElementById('productForm');
const submitBtn = document.getElementById('submitBtn');

form.addEventListener('submit', function(e) {
    submitBtn.disabled = true;
    submitBtn.innerHTML = '⏳ Envoi en cours...';
    submitBtn.style.opacity = '0.6';
    
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
});

// Compteur de caractères pour description
const descTextarea = document.getElementById('description');
if (descTextarea) {
    const counter = document.createElement('div');
    counter.style.cssText = 'font-size: 0.75rem; margin-top: var(--space-1); text-align: right; font-weight: 600;';
    descTextarea.parentElement.appendChild(counter);
    
    function updateCounter() {
        const length = descTextarea.value.length;
        counter.textContent = `${length} caractères`;
        counter.style.color = length < 50 ? '#ef4444' : '#10b981';
    }
    
    descTextarea.addEventListener('input', updateCounter);
    updateCounter();
}

// Calculer la réduction automatique
const priceInput = document.getElementById('price');
const originalPriceInput = document.getElementById('original_price');
const discountInfo = document.getElementById('discountInfo');

if (priceInput && originalPriceInput) {
    function updateDiscount() {
        const price = parseFloat(priceInput.value);
        const originalPrice = parseFloat(originalPriceInput.value);
        
        if (price && originalPrice && originalPrice > price) {
            const discount = Math.round((1 - price / originalPrice) * 100);
            discountInfo.innerHTML = `Pour afficher une promotion <strong style="color: #ef4444;">(-${discount}%)</strong>`;
        } else {
            discountInfo.textContent = 'Pour afficher une promotion';
        }
    }
    
    priceInput.addEventListener('input', updateDiscount);
    originalPriceInput.addEventListener('input', updateDiscount);
}

// Afficher le nom du fichier sélectionné
document.getElementById('product_file').addEventListener('change', function(e) {
    if (this.files.length > 0) {
        const fileName = this.files[0].name;
        const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2);
        this.parentElement.querySelector('p:last-child').innerHTML = 
            `<strong style="color: #10b981;">✓ ${fileName}</strong> (${fileSize} MB)`;
    }
});
</script>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

form > div {
    animation: fadeIn 0.5s ease-out backwards;
}

form > div:nth-child(2) {
    animation-delay: 0.1s;
}

form > div:nth-child(3) {
    animation-delay: 0.2s;
}

form > div:nth-child(4) {
    animation-delay: 0.3s;
}
</style>