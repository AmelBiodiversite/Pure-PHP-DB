<?php
/**
 * MARKETFLOW PRO - LISTE DES CATÉGORIES
 * Design pastel éditorial — version intégrée
 */

// Palette pastel par slug de catégorie
$svgIcons = [
    'developpement-personnel' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none"><line x1="16" y1="26" x2="16" y2="8" stroke="#0F6E56" stroke-width="1.5" stroke-linecap="round"/><polyline points="11,13 16,8 21,13" fill="none" stroke="#0F6E56" stroke-width="1.5" stroke-linejoin="round" stroke-linecap="round"/><path d="M9 22 Q16 19 23 22" stroke="#5DCAA5" stroke-width="1.5" stroke-linecap="round" fill="none"/></svg>',
    'sante-alimentation' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none"><path d="M16 5 C11 5 8 9 8 14 C8 20 12 27 16 27 C20 27 24 20 24 14 C24 9 21 5 16 5Z" fill="#C0DD97" stroke="#3B6D11" stroke-width="1.5"/><path d="M13 17 L16 20 L21 13" stroke="#3B6D11" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    'jardin-autonomie' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none"><path d="M16 6C14 9 13 12 13 15a3 3 0 006 0C19 12 18 9 16 6z" stroke="#854F0B" stroke-width="1.5" stroke-linejoin="round"/><path d="M10 12C8 14 7 17 7 20a3 3 0 006 0C13 17 12 14 10 12z" stroke="#854F0B" stroke-width="1.5" stroke-linejoin="round"/><path d="M22 12C20 14 19 17 19 20a3 3 0 006 0C25 17 24 14 22 12z" stroke="#854F0B" stroke-width="1.5" stroke-linejoin="round"/></svg>',
    'maison-energie' => '<svg width="32" height="32" viewBox="0 0 32 32" fill="none"><path d="M4 28h24M8 28V18L16 10L24 18V28" stroke="#185FA5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><rect x="13" y="20" width="6" height="8" rx="1" stroke="#185FA5" stroke-width="1.5"/></svg>',
];

$svgIconFallback = '<svg width="32" height="32" viewBox="0 0 32 32" fill="none"><rect x="6" y="6" width="20" height="20" rx="4" fill="#B5D4F4" stroke="#185FA5" stroke-width="1.5"/><line x1="11" y1="16" x2="21" y2="16" stroke="#185FA5" stroke-width="1.5" stroke-linecap="round"/><line x1="16" y1="11" x2="16" y2="21" stroke="#185FA5" stroke-width="1.5" stroke-linecap="round"/></svg>';

$categoryStyles = [
    'developpement-personnel' => ['bg'=>'#EEEDFE','bar'=>'#534AB7','badge-bg'=>'#AFA9EC','badge-text'=>'#26215C','num'=>'#534AB7'],
    'sante-alimentation'      => ['bg'=>'#EAF3DE','bar'=>'#3B6D11','badge-bg'=>'#C0DD97','badge-text'=>'#173404','num'=>'#639922'],
    'jardin-autonomie'        => ['bg'=>'#FAEEDA','bar'=>'#854F0B','badge-bg'=>'#FAC775','badge-text'=>'#412402','num'=>'#BA7517'],
    'maison-energie'          => ['bg'=>'#E6F1FB','bar'=>'#185FA5','badge-bg'=>'#85B7EB','badge-text'=>'#042C53','num'=>'#378ADD'],
];

// Fallback palette tournante pour catégories non listées
$fallbackStyles = [
    ['bg'=>'#E6F1FB','bar'=>'#185FA5','badge-bg'=>'#85B7EB','badge-text'=>'#042C53','num'=>'#378ADD'],
    ['bg'=>'#EEEDFE','bar'=>'#534AB7','badge-bg'=>'#AFA9EC','badge-text'=>'#26215C','num'=>'#534AB7'],
    ['bg'=>'#E1F5EE','bar'=>'#0F6E56','badge-bg'=>'#9FE1CB','badge-text'=>'#04342C','num'=>'#1D9E75'],
    ['bg'=>'#EAF3DE','bar'=>'#3B6D11','badge-bg'=>'#C0DD97','badge-text'=>'#173404','num'=>'#639922'],
    ['bg'=>'#FAEEDA','bar'=>'#854F0B','badge-bg'=>'#FAC775','badge-text'=>'#412402','num'=>'#BA7517'],
    ['bg'=>'#FAECE7','bar'=>'#993C1D','badge-bg'=>'#F0997B','badge-text'=>'#4A1B0C','num'=>'#D85A30'],
    ['bg'=>'#FBEAF0','bar'=>'#993556','badge-bg'=>'#ED93B1','badge-text'=>'#4B1528','num'=>'#D4537E'],
    ['bg'=>'#FCEBEB','bar'=>'#A32D2D','badge-bg'=>'#F09595','badge-text'=>'#501313','num'=>'#E24B4A'],
];
?>

<style>
.mfp-cat-hero {
    padding: 3.5rem 0 2.5rem;
    border-bottom: 0.5px solid var(--gray-200);
    margin-bottom: 2.5rem;
}
.mfp-cat-grid {
    display: grid;
    grid-template-columns: 1.45fr 1fr;
    gap: 16px;
}
/* Row 2 inversée : petit → grand */
.mfp-cat-row { display: grid; gap: 16px; margin-bottom: 16px; }
.mfp-cat-row--gp { grid-template-columns: 1.45fr 1fr; } /* grand-petit */
.mfp-cat-row--pg { grid-template-columns: 1fr 1.45fr; } /* petit-grand */
@media (max-width: 640px) {
    .mfp-cat-row--gp, .mfp-cat-row--pg { grid-template-columns: 1fr; }
}
@media (max-width: 900px) {
    .mfp-cat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .mfp-cat-wide { grid-column: span 2 !important; }
    .mfp-cat-full { grid-column: span 2 !important; flex-direction: column !important; }
    .mfp-cat-full .mfp-cat-badge { margin-left: 0 !important; margin-top: 1rem; }
}
@media (max-width: 560px) {
    .mfp-cat-grid { grid-template-columns: 1fr; }
    .mfp-cat-wide, .mfp-cat-full { grid-column: span 1 !important; flex-direction: column !important; }
    .mfp-cat-full .mfp-cat-badge { margin-left: 0 !important; margin-top: 1rem; }
}
.mfp-cat-card {
    position: relative;
    overflow: hidden;
    border-radius: 14px;
    padding: 1.75rem 2rem;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    min-height: 210px;
    transition: transform 0.22s ease, box-shadow 0.22s ease;
}
.mfp-cat-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 40px rgba(0,0,0,0.10);
}
.mfp-cat-full {
    flex-direction: row !important;
    align-items: center;
    min-height: 140px !important;
    gap: 2rem;
}
.mfp-cat-full .mfp-cat-num { top: auto; right: 1.5rem; bottom: 0; opacity: 0.07; }
.mfp-cat-full .mfp-cat-name { margin-bottom: 0 !important; }
.mfp-cat-bar {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 14px 14px 0 0;
}
.mfp-cat-num {
    position: absolute;
    top: -0.75rem; right: 1rem;
    font-size: 88px;
    font-weight: 600;
    opacity: 0.08;
    line-height: 1;
    pointer-events: none;
    user-select: none;
    font-family: Georgia, 'Times New Roman', serif;
}
.mfp-cat-icon {
    font-size: 2.25rem;
    margin-bottom: 1.1rem;
    display: inline-block;
    transition: transform 0.22s ease;
    flex-shrink: 0;
}
.mfp-cat-card:hover .mfp-cat-icon {
    transform: scale(1.15) rotate(5deg);
}
.mfp-cat-label {
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.08em;
    opacity: 0.65;
    margin-bottom: 5px;
    text-transform: none;
}
.mfp-cat-name {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 1.45rem;
    font-weight: 500;
    line-height: 1.2;
    margin: 0 0 1.1rem;
}
.mfp-cat-wide .mfp-cat-name {
    font-size: 1.75rem;
}
.mfp-cat-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.875rem;
    font-weight: 600;
    padding: 0.4rem 1.1rem;
    border-radius: 8px;
    flex-shrink: 0;
}
.mfp-stats-bar {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 2.5rem;
    flex-wrap: wrap;
}
.mfp-stat-item {
    display: flex;
    flex-direction: column;
}
.mfp-stat-value {
    font-family: Georgia, 'Times New Roman', serif;
    font-size: 2rem;
    font-weight: 500;
    color: var(--text-primary);
    line-height: 1;
}
.mfp-stat-label {
    font-size: 0.8rem;
    color: var(--text-secondary);
    margin-top: 4px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}
@keyframes mfpFadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>

<!-- Hero -->
<section class="mfp-cat-hero container">
    <p style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.1em; color: var(--text-secondary); margin: 0 0 0.6rem; text-transform: uppercase;">
        MarketFlow Pro
    </p>
    <h1 style="font-family: Georgia, 'Times New Roman', serif; font-size: clamp(2rem, 4vw, 2.75rem); font-weight: 500; margin: 0 0 0.75rem; color: var(--text-primary); line-height: 1.15;">
        Nos catégories
    </h1>
    <p style="font-size: 1.1rem; color: var(--text-secondary); max-width: 560px; margin: 0 0 2rem;">
        Explorez nos univers dédiés à l'autonomie et au bien-être
    </p>

    <!-- Barre de recherche -->
    <form action="/products" method="GET" style="display: flex; gap: 0.75rem; max-width: 560px;">
        <input
            type="text"
            name="q"
            placeholder="Rechercher une ressource, une catégorie…"
            style="flex: 1; padding: 0.75rem 1.25rem; border: 1.5px solid var(--gray-200); border-radius: 10px; font-size: 0.975rem; transition: border-color 0.2s;"
            onfocus="this.style.borderColor='var(--primary-500)'"
            onblur="this.style.borderColor='var(--gray-200)'"
        />
        <button type="submit" class="btn btn-primary">Rechercher</button>
    </form>

    <!-- Stats réelles -->
    <div class="mfp-stats-bar" style="margin-top: 2.5rem;">
        <div class="mfp-stat-item">
            <span class="mfp-stat-value"><?= count($categories) ?></span>
            <span class="mfp-stat-label">Catégories</span>
        </div>
        <div style="width: 0.5px; background: var(--gray-200); align-self: stretch;"></div>
        <div class="mfp-stat-item">
            <span class="mfp-stat-value">
                <?php
                $totalProducts = array_sum(array_column($categories, 'product_count'));
                echo number_format($totalProducts, 0, ',', ' ');
                ?>
            </span>
            <span class="mfp-stat-label">Ressources disponibles</span>
        </div>
    </div>
</section>

<!-- Grille des catégories -->
<section class="container mb-16">
    <!-- Ligne 1 : grand-petit -->
    <div class="mfp-cat-row mfp-cat-row--gp">
        <?php foreach (array_slice($categories, 0, 2) as $index => $category):
            $slug  = $category['slug'];
            $style = $categoryStyles[$slug] ?? $fallbackStyles[$index % count($fallbackStyles)];
            $num   = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $icon  = $svgIcons[$slug] ?? $svgIconFallback;
            $cardClass = 'mfp-cat-card';

            $count = (int)($category['product_count'] ?? 0);
            $countLabel = $count > 0
                ? number_format($count, 0, ',', ' ') . ' ressource' . ($count > 1 ? 's' : '')
                : '— ressources';
        ?>
        <a
            href="/category/<?= e($slug) ?>"
            class="<?= $cardClass ?>"
            style="
                background: <?= $style['bg'] ?>;
                animation: mfpFadeUp 0.5s ease-out <?= $index * 0.06 ?>s both;
            "
        >
            <div class="mfp-cat-bar" style="background: <?= $style['bar'] ?>;"></div>
            <span class="mfp-cat-num" style="color: <?= $style['num'] ?>;"><?= $num ?></span>

            <span class="mfp-cat-icon"><?= $icon ?></span>

            <div class="mfp-cat-label" style="color: <?= $style['bar'] ?>;">
                <?= $num ?> · <?= e($category['name']) ?>
            </div>
            <h3 class="mfp-cat-name" style="color: <?= $style['badge-text'] ?>;">
                <?= e($category['name']) ?>
            </h3>

            <span class="mfp-cat-badge" style="background: <?= $style['badge-bg'] ?>; color: <?= $style['badge-text'] ?>;">
                <?= $countLabel ?>
            </span>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Ligne 2 : petit-grand -->
    <div class="mfp-cat-row mfp-cat-row--pg">
        <?php foreach (array_slice($categories, 2, 2) as $i => $category):
            $index = $i + 2;
            $slug  = $category['slug'];
            $style = $categoryStyles[$slug] ?? $fallbackStyles[$index % count($fallbackStyles)];
            $num   = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
            $icon  = $svgIcons[$slug] ?? $svgIconFallback;
            $cardClass = 'mfp-cat-card';
            $count = (int)($category['product_count'] ?? 0);
            $countLabel = $count > 0
                ? number_format($count, 0, ',', ' ') . ' ressource' . ($count > 1 ? 's' : '')
                : '— ressources';
        ?>
        <a href="/category/<?= e($slug) ?>" class="<?= $cardClass ?>"
           style="background: <?= $style['bg'] ?>; animation: mfpFadeUp 0.5s ease-out <?= $index * 0.06 ?>s both;">
            <div class="mfp-cat-bar" style="background: <?= $style['bar'] ?>;"></div>
            <span class="mfp-cat-num" style="color: <?= $style['num'] ?>;"><?= $num ?></span>
            <span class="mfp-cat-icon"><?= $icon ?></span>
            <div class="mfp-cat-label" style="color: <?= $style['bar'] ?>"><?= $num ?> · <?= e($category['name']) ?></div>
            <h3 class="mfp-cat-name" style="color: <?= $style['badge-text'] ?>"><?= e($category['name']) ?></h3>
            <span class="mfp-cat-badge" style="background: <?= $style['badge-bg'] ?>; color: <?= $style['badge-text'] ?>"><?= $countLabel ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- CTA Vendeur -->
<section class="container mb-16">
    <div class="card text-center p-12" style="background: var(--gradient-primary); color: white; position: relative; overflow: hidden;">
        <div style="position: absolute; inset: 0; opacity: 0.08; background-image: repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,.15) 35px, rgba(255,255,255,.15) 70px);"></div>
        <div style="position: relative; z-index: 1;">
            <h2 style="color: white; margin-bottom: 1rem; font-size: 2rem; font-family: Georgia, 'Times New Roman', serif; font-weight: 500;">
                Vous êtes créateur ?
            </h2>
            <p style="font-size: 1.1rem; margin-bottom: 2rem; opacity: 0.92;">
                Rejoignez notre communauté et partagez vos ressources avec le monde
            </p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="/register?role=seller" class="btn btn-lg" style="background: white; color: var(--primary-600); font-weight: 700; box-shadow: 0 8px 24px rgba(0,0,0,0.18);">
                    Devenir vendeur →
                </a>
            <?php elseif ($_SESSION['user_role'] === 'seller'): ?>
                <a href="/seller/products/create" class="btn btn-lg" style="background: white; color: var(--primary-600); font-weight: 700; box-shadow: 0 8px 24px rgba(0,0,0,0.18);">
                    Ajouter une ressource →
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
/* Délégation d'événements — un seul listener pour toutes les cards */
document.querySelectorAll('.mfp-cat-card').forEach(function(card) {
    card.addEventListener('mouseenter', function() {
        var icon = this.querySelector('.mfp-cat-icon');
        if (icon) icon.style.transform = 'scale(1.15) rotate(5deg)';
    });
    card.addEventListener('mouseleave', function() {
        var icon = this.querySelector('.mfp-cat-icon');
        if (icon) icon.style.transform = '';
    });
});
</script>
