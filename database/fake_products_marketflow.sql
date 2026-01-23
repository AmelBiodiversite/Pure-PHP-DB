-- ================================================================
-- MARKETFLOW PRO - DONNÉES FICTIVES POUR LA VENTE
-- 26 produits répartis sur 11 catégories + 3 vendeurs
-- ================================================================

-- ================================================================
-- 1. CRÉATION DES VENDEURS FICTIFS
-- ================================================================

INSERT INTO users (username, email, password, full_name, role, status, created_at) VALUES
('sarah_designs', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah Martinez', 'seller', 'active', NOW()),
('alex_creative', 'alex@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alex Chen', 'seller', 'active', NOW()),
('emma_studio', 'emma@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emma Williams', 'seller', 'active', NOW());

-- ================================================================
-- 2. TEMPLATES (3 produits) - Category ID: 1
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(2, 1, 'Modern SaaS Landing Page', 'modern-saas-landing-page', 'Template de landing page professionnelle pour startups SaaS. Design moderne avec animations fluides, sections hero impactantes, pricing tables, témoignages clients et CTA optimisés. Entièrement responsive et optimisé SEO. Inclut HTML, CSS, JS et documentation complète.', 'Landing page SaaS moderne et responsive avec animations', 49.00, 79.00, '/uploads/products/saas-template.zip', 'application/zip', 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800', 'extended', 'approved', true, 4.80, 47, 234, 1856, 234, NOW() - INTERVAL '15 days'),

(3, 1, 'E-Commerce Product Showcase', 'ecommerce-product-showcase', 'Template e-commerce complet avec système de filtres avancés, grille produits responsive, pages détail optimisées et panier intégré. Design minimaliste et élégant. Compatible avec tous les CMS populaires. Inclut 12 variations de pages.', 'Template e-commerce premium avec filtres et panier', 65.00, 99.00, '/uploads/products/ecommerce-template.zip', 'application/zip', 'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=800', 'extended', 'approved', true, 4.90, 62, 189, 2134, 189, NOW() - INTERVAL '8 days'),

(1, 1, 'Portfolio Creative Agency', 'portfolio-creative-agency', 'Portfolio moderne pour agences créatives et freelances. Mise en page spectaculaire avec parallax, galerie projets interactive, animations on-scroll et dark mode. Parfait pour designers, photographes et studios. 100% customizable.', 'Portfolio créatif avec parallax et dark mode', 39.00, 59.00, '/uploads/products/portfolio-template.zip', 'application/zip', 'https://images.unsplash.com/photo-1467232004584-a241de8bcf5d?w=800', 'single', 'approved', false, 4.70, 31, 156, 987, 156, NOW() - INTERVAL '20 days');

-- ================================================================
-- 3. GRAPHICS (2 produits) - Category ID: 2
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(2, 2, 'Abstract 3D Backgrounds Pack', 'abstract-3d-backgrounds-pack', 'Collection de 50 fonds abstraits 3D en haute résolution (4K). Parfaits pour sites web, présentations, réseaux sociaux. Formats PNG et JPG inclus. Couleurs modernes et vibrantes. Chaque image optimisée pour le web et l impression.', 'Pack de 50 backgrounds 3D abstraits 4K', 29.00, 49.00, '/uploads/products/3d-backgrounds.zip', 'application/zip', 'https://images.unsplash.com/photo-1557672172-298e090bd0f1?w=800', 'extended', 'approved', true, 4.85, 73, 412, 2891, 412, NOW() - INTERVAL '12 days'),

(3, 2, 'Gradient Mesh Collection', 'gradient-mesh-collection', 'Collection artistique de 30 mesh gradients fluides. Idéal pour designs modernes, interfaces, branding. Fichiers vectoriels SVG + PNG haute résolution. Palette de couleurs tendance 2026. Facilement personnalisable dans Figma, Adobe XD ou Illustrator.', 'Collection de 30 mesh gradients modernes', 24.00, NULL, '/uploads/products/gradient-mesh.zip', 'application/zip', 'https://images.unsplash.com/photo-1558591710-4b4a1ae0f04d?w=800', 'extended', 'approved', false, 4.60, 28, 198, 1456, 198, NOW() - INTERVAL '18 days');

-- ================================================================
-- 4. CODE (2 produits) - Category ID: 3
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(4, 3, 'React Dashboard Components', 'react-dashboard-components', 'Bibliothèque complète de composants React pour dashboards. +40 composants (charts, tables, cards, forms). TypeScript support. Styled avec Tailwind CSS. Documentation interactive. Compatible React 18+. Mise à jour régulière et support technique inclus.', 'Bibliothèque de 40+ composants React dashboard', 79.00, 129.00, '/uploads/products/react-dashboard.zip', 'application/zip', 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=800', 'extended', 'approved', true, 4.95, 89, 267, 3421, 267, NOW() - INTERVAL '5 days'),

(1, 3, 'PHP Authentication System', 'php-authentication-system', 'Système d authentification PHP complet et sécurisé. Inscription, connexion, reset mot de passe, 2FA, gestion sessions. Protection CSRF, XSS, SQL injection. Compatible PostgreSQL et MySQL. Code propre PSR-12. Documentation détaillée.', 'Système auth PHP sécurisé avec 2FA', 45.00, NULL, '/uploads/products/php-auth.zip', 'application/zip', 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=800', 'single', 'approved', false, 4.75, 44, 178, 1234, 178, NOW() - INTERVAL '22 days');

-- ================================================================
-- 5. COURSES (2 produits) - Category ID: 4
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(2, 4, 'UI/UX Design Masterclass 2026', 'ui-ux-design-masterclass-2026', 'Formation complète UI/UX Design (12h de vidéo). Apprenez Figma, design systems, prototypage, user research. 25 projets pratiques. Certificat inclus. Accès lifetime et mises à jour gratuites. Fichiers sources Figma inclus.', 'Formation UI/UX complète avec 25 projets pratiques', 89.00, 149.00, '/uploads/products/uiux-course.zip', 'application/zip', 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=800', 'single', 'approved', true, 4.92, 156, 423, 4567, 423, NOW() - INTERVAL '3 days'),

(3, 4, 'JavaScript Avancé - Concepts Modernes', 'javascript-avance-concepts-modernes', 'Maîtrisez JavaScript moderne (ES6+). Promises, async/await, modules, closures, prototypes. 8h de vidéo + exercices corrigés. Projets réels : API REST, SPA, tests unitaires. Code source complet fourni.', 'Formation JavaScript ES6+ avec projets réels', 69.00, 99.00, '/uploads/products/js-advanced-course.zip', 'application/zip', 'https://images.unsplash.com/photo-1579468118864-1b9ea3c0db4a?w=800', 'single', 'approved', false, 4.80, 67, 234, 2345, 234, NOW() - INTERVAL '14 days');

-- ================================================================
-- 6. PHOTOS (2 produits) - Category ID: 5
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(4, 5, 'Workspace Flat Lay Photography', 'workspace-flat-lay-photography', 'Collection de 40 photos flat lay professionnelles de bureaux créatifs. Haute résolution (6000x4000px). Parfait pour blogs, réseaux sociaux, sites web. Ambiances minimalistes et modernes. License commerciale étendue.', 'Pack de 40 photos flat lay workspace HD', 35.00, NULL, '/uploads/products/workspace-photos.zip', 'application/zip', 'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=800', 'extended', 'approved', false, 4.70, 38, 167, 1567, 167, NOW() - INTERVAL '19 days'),

(2, 5, 'Nature & Landscape Premium', 'nature-landscape-premium', 'Collection exclusive de 60 photos nature et paysages. Montagnes, forêts, océans, ciels dramatiques. Résolution 8K. Retouche professionnelle. Idéal pour sites voyage, magazines, impressions grand format. Formats RAW + JPG.', 'Collection de 60 photos nature et paysages 8K', 59.00, 89.00, '/uploads/products/nature-photos.zip', 'application/zip', 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800', 'extended', 'approved', true, 4.88, 91, 289, 2678, 289, NOW() - INTERVAL '7 days');

-- ================================================================
-- 7. AUDIO (2 produits) - Category ID: 6
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(3, 6, 'UI Sound Effects Pack', 'ui-sound-effects-pack', 'Pack de 150 effets sonores pour interfaces (clics, notifications, transitions, succès, erreurs). Format WAV 48kHz. Parfait pour apps, sites web, jeux. Sons discrets et professionnels. License commerciale illimitée.', 'Pack de 150 sons UI professionnels', 32.00, NULL, '/uploads/products/ui-sounds.zip', 'application/zip', 'https://images.unsplash.com/photo-1598488035139-bdbb2231ce04?w=800', 'extended', 'approved', false, 4.65, 42, 198, 1345, 198, NOW() - INTERVAL '16 days'),

(4, 6, 'Ambient Music for Content Creators', 'ambient-music-content-creators', 'Collection de 20 morceaux ambient/lo-fi (2-5min chacun). Parfait pour vlogs, podcasts, vidéos YouTube. Format MP3 320kbps + WAV. Atmosphères relaxantes et inspirantes. License YouTube et réseaux sociaux incluse.', 'Pack de 20 musiques ambient pour créateurs', 44.00, 69.00, '/uploads/products/ambient-music.zip', 'application/zip', 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?w=800', 'extended', 'approved', true, 4.82, 67, 312, 2234, 312, NOW() - INTERVAL '9 days');

-- ================================================================
-- 8. FONTS (2 produits) - Category ID: 7
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(1, 7, 'Modernist Sans - Font Family', 'modernist-sans-font-family', 'Famille de polices sans-serif moderne (9 graisses). Parfaite pour interfaces, branding, éditorial. Support multilingue complet. Formats OTF, TTF, WOFF, WOFF2. Ligatures et alternatives stylistiques. License desktop + web.', 'Police sans-serif moderne - 9 graisses', 55.00, 99.00, '/uploads/products/modernist-font.zip', 'application/zip', 'https://images.unsplash.com/photo-1456086272160-b28b0645b729?w=800', 'extended', 'approved', true, 4.78, 53, 234, 1987, 234, NOW() - INTERVAL '11 days'),

(2, 7, 'Handwritten Signature Font', 'handwritten-signature-font', 'Police manuscrite élégante style signature. Idéale pour logos, invitations, branding luxe. Caractères alternatifs et ligatures. Support accents français. Formats OTF et TTF. License commerciale complète.', 'Police manuscrite élégante pour branding', 28.00, NULL, '/uploads/products/signature-font.zip', 'application/zip', 'https://images.unsplash.com/photo-1455390582262-044cdead277a?w=800', 'single', 'approved', false, 4.60, 29, 145, 1123, 145, NOW() - INTERVAL '24 days');

-- ================================================================
-- 9. OTHER (2 produits) - Category ID: 8
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(3, 8, 'Social Media Templates Bundle', 'social-media-templates-bundle', 'Mega pack de 200+ templates pour Instagram, Facebook, LinkedIn. Stories, posts, carrousels. Formats Canva + PSD. Design moderne et professionnel. Entièrement personnalisable. Idéal pour entrepreneurs et agences.', 'Pack de 200+ templates réseaux sociaux', 42.00, 79.00, '/uploads/products/social-templates.zip', 'application/zip', 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=800', 'extended', 'approved', true, 4.75, 94, 378, 3234, 378, NOW() - INTERVAL '6 days'),

(4, 8, 'Business Presentation Slides', 'business-presentation-slides', 'Pack de 150 slides PowerPoint et Keynote professionnels. Layouts variés pour pitchs, rapports, formations. Graphiques, timelines, mockups. Design minimaliste et corporate. Facile à customiser.', 'Pack de 150 slides présentation business', 38.00, NULL, '/uploads/products/presentation-slides.zip', 'application/zip', 'https://images.unsplash.com/photo-1551818255-e6e10975bc17?w=800', 'single', 'approved', false, 4.68, 47, 201, 1678, 201, NOW() - INTERVAL '13 days');

-- ================================================================
-- 10. UI KITS (3 produits) - Category ID: 9
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(2, 9, 'iOS App UI Kit - Finance', 'ios-app-ui-kit-finance', 'UI Kit complet pour applications finance iOS. +60 écrans (onboarding, dashboard, transactions, analytics). Design system complet. Components Figma auto-layout. Dark mode inclus. Guide de style détaillé.', 'UI Kit iOS Finance avec 60+ écrans', 69.00, 119.00, '/uploads/products/ios-finance-ui.zip', 'application/zip', 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800', 'extended', 'approved', true, 4.92, 112, 345, 4123, 345, NOW() - INTERVAL '4 days'),

(4, 9, 'Mobile E-commerce UI Kit', 'mobile-ecommerce-ui-kit', 'Kit UI e-commerce mobile moderne. 50+ écrans pour app shopping complète. Catalogue, détails produits, panier, checkout, profil. Format Figma et Sketch. Design system cohérent. iOS et Android.', 'Kit UI e-commerce mobile - 50+ écrans', 59.00, 99.00, '/uploads/products/mobile-ecom-ui.zip', 'application/zip', 'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=800', 'extended', 'approved', true, 4.85, 87, 289, 2987, 289, NOW() - INTERVAL '10 days'),

(3, 9, 'Dashboard Admin UI Components', 'dashboard-admin-ui-components', 'Bibliothèque de composants pour dashboards admin. Tables avancées, charts interactifs, forms complexes, notifications, modals. Format Figma avec variants. Design system complet. Light et dark mode.', 'Composants dashboard admin Figma', 52.00, NULL, '/uploads/products/admin-dashboard-ui.zip', 'application/zip', 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=800', 'single', 'approved', false, 4.72, 63, 223, 1876, 223, NOW() - INTERVAL '17 days');

-- ================================================================
-- 11. ICÔNES (3 produits) - Category ID: 11
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(1, 11, 'Minimalist Line Icons 500+', 'minimalist-line-icons-500', 'Collection de 500+ icônes line minimalistes. 20 catégories (business, tech, social, e-commerce...). Stroke ajustable. Formats SVG, PNG, IconFont. Grid parfaite. Cohérence visuelle garantie.', 'Pack de 500+ icônes line minimalistes', 34.00, 59.00, '/uploads/products/line-icons.zip', 'application/zip', 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800', 'extended', 'approved', true, 4.88, 124, 467, 3876, 467, NOW() - INTERVAL '2 days'),

(2, 11, '3D Isometric Icons Pack', '3d-isometric-icons-pack', 'Pack de 120 icônes 3D isométriques. Style moderne et coloré. Parfait pour apps, presentations, sites web. Formats PNG 4K + fichiers 3D sources (Blender). 6 variations de couleurs par icône.', 'Pack de 120 icônes 3D isométriques', 48.00, NULL, '/uploads/products/3d-icons.zip', 'application/zip', 'https://images.unsplash.com/photo-1618761714954-0b8cd0026356?w=800', 'extended', 'approved', true, 4.80, 78, 312, 2654, 312, NOW() - INTERVAL '8 days'),

(4, 11, 'Animated Icons for Web', 'animated-icons-web', 'Collection de 80 icônes animées (Lottie JSON). Micro-interactions fluides. Categories : actions, status, navigation, social. Poids léger. Compatible tous frameworks. Fichiers After Effects sources inclus.', 'Pack de 80 icônes animées Lottie', 44.00, 69.00, '/uploads/products/animated-icons.zip', 'application/zip', 'https://images.unsplash.com/photo-1557804506-669a67965ba0?w=800', 'single', 'approved', false, 4.75, 56, 234, 1987, 234, NOW() - INTERVAL '15 days');

-- ================================================================
-- 12. ILLUSTRATIONS (3 produits) - Category ID: 12
-- ================================================================

INSERT INTO products (seller_id, category_id, title, slug, description, short_description, price, original_price, file_url, file_type, thumbnail_url, license_type, status, is_featured, rating_average, rating_count, downloads, views, sales, created_at) VALUES
(3, 12, 'Business Illustrations Pack', 'business-illustrations-pack', 'Collection de 40 illustrations business professionnelles. Teamwork, success, innovation, growth. Style flat moderne. Formats SVG + PNG. Couleurs personnalisables. Parfait pour presentations et landing pages.', 'Pack de 40 illustrations business flat', 39.00, 69.00, '/uploads/products/business-illustrations.zip', 'application/zip', 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800', 'extended', 'approved', true, 4.82, 89, 334, 2765, 334, NOW() - INTERVAL '6 days'),

(1, 12, 'Character Avatars Collection', 'character-avatars-collection', 'Set de 100 avatars de personnages divers et inclusifs. Styles variés, expressions multiples. Format SVG vectoriel. Idéal pour apps sociales, forums, dashboards. Facile à personnaliser dans Figma.', 'Collection de 100 avatars personnages', 32.00, NULL, '/uploads/products/character-avatars.zip', 'application/zip', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=800', 'single', 'approved', false, 4.68, 52, 198, 1654, 198, NOW() - INTERVAL '21 days'),

(2, 12, 'Abstract Geometric Shapes', 'abstract-geometric-shapes', 'Pack de 60 formes géométriques abstraites. Design moderne et minimaliste. Parfait pour backgrounds, headers, decorations. Formats AI, SVG, PNG. Couleurs tendance 2026. Haute qualité vectorielle.', 'Pack de 60 formes géométriques abstraites', 29.00, 49.00, '/uploads/products/geometric-shapes.zip', 'application/zip', 'https://images.unsplash.com/photo-1557672172-298e090bd0f1?w=800', 'extended', 'approved', false, 4.70, 41, 176, 1432, 176, NOW() - INTERVAL '12 days');

-- ================================================================
-- FIN DU FICHIER
-- Total : 3 vendeurs + 26 produits (2-3 par catégorie)
-- ================================================================