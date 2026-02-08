<?php
/**
 * MARKETFLOW PRO - ROUTES
 * Toutes les routes AVANT $router->dispatch() !
 */

$router = new Core\Router();

// ================================================================
// ROUTES PUBLIQUES
// ================================================================

$router->get('/', 'HomeController@index');

// Authentification
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// Pages info
$router->get('/sellers', 'HomeController@sellers');
$router->get('/seller/{username}/products', 'ProductController@sellerProducts');
$router->get('/about', 'HomeController@about');
$router->get('/contact', 'HomeController@contact');
$router->post('/contact', 'HomeController@contactSubmit');
$router->get('/terms', 'HomeController@terms');
$router->get('/privacy', 'HomeController@privacy');
$router->get('/help', 'HomeController@help');

// ================================================================
// ROUTES UTILISATEURS (Auth requise)
// ================================================================

$router->get('/profile', 'AuthController@profile');
$router->post('/profile/update', 'AuthController@updateProfile');
$router->post('/profile/password', 'AuthController@changePassword');
$router->post('/profile/seller', 'AuthController@updateSellerProfile');
$router->get('/account', 'AccountController@index');
$router->get('/account/downloads', 'AccountController@downloads');

// ================================================================
// ROUTES PRODUITS
// ================================================================

// Routes fixes AVANT routes dynamiques
$router->get('/products', 'ProductController@index');
$router->get('/products/search', 'ProductController@search');

// Routes catégories (AVANT /products/{slug} pour éviter conflit)
$router->get('/category', 'ProductController@categories');
$router->get('/category/{slug}', 'ProductController@category');

// Route dynamique produit (EN DERNIER)
$router->get('/products/{slug}', 'ProductController@show');

// ================================================================
// WISHLIST
// ================================================================

$router->get('/wishlist', 'WishlistController@index');
$router->post('/wishlist/add', 'WishlistController@add');
$router->post('/wishlist/remove', 'WishlistController@remove');
$router->get('/wishlist/count', 'WishlistController@count');

// ================================================================
// PANIER & COMMANDES
// ================================================================

// Panier
$router->get('/cart', 'CartController@index');
$router->post('/cart/add', 'CartController@add');
$router->post('/cart/remove', 'CartController@remove');
$router->post('/cart/update', 'CartController@update');
$router->post('/cart/clear', 'CartController@clear');
$router->post('/cart/apply-promo', 'CartController@applyPromo');
$router->get('/cart/remove-promo', 'CartController@removePromo');

// Checkout
$router->get('/checkout', 'CartController@checkout');
$router->post('/checkout/create-session', 'CartController@processCheckout');
$router->get('/checkout/success', 'PaymentController@success');
$router->get('/checkout/success', 'StripeController@success');
$router->post('/checkout/create-session', 'StripeController@createCheckoutSession');
$router->post('/stripe/create-checkout-session', 'StripeController@createCheckoutSession');
$router->get('/checkout/cancel', 'CartController@index');

// Paiement
$router->get('/payment/success', 'PaymentController@success');
$router->get('/payment/cancel', 'PaymentController@cancel');
$router->post('/webhooks/stripe', 'PaymentController@stripeWebhook');

// Commandes
$router->get('/orders', 'OrderController@index');
$router->get('/orders/{orderNumber}', 'OrderController@show');
$router->get('/orders/{orderNumber}/download/{itemId}', 'OrderController@download');

// ================================================================
// VENDEURS (Auth seller/admin)
// ================================================================

$router->get('/seller/dashboard', 'SellerController@dashboard');
$router->get('/seller/products', 'SellerController@products');
$router->get('/seller/products/create', 'SellerController@createProduct');
$router->post('/seller/products/store', 'SellerController@storeProduct');
$router->get('/seller/products/{id}/edit', 'SellerController@editProduct');
$router->post('/seller/products/{id}/update', 'SellerController@updateProduct');
$router->post('/seller/products/{id}/delete', 'SellerController@deleteProduct');
$router->get('/seller/sales', 'SellerController@sales');
$router->get('/seller/earnings', 'SellerController@earnings');
$router->post('/seller/payout', 'SellerController@requestPayout');
$router->get('/seller/analytics', 'SellerController@analytics');

// ================================================================
// ADMIN (Auth admin)
// ================================================================

$router->get('/admin', 'AdminController@index');
$router->get('/admin/dashboard', 'AdminController@index');
$router->get('/admin/users', 'AdminController@users');
$router->post('/admin/users/{id}/toggle', 'AdminController@toggleUser');
$router->post('/admin/users/{id}/suspend', 'AdminController@suspendUser');
$router->post('/admin/users/{id}/activate', 'AdminController@activateUser');
$router->post('/admin/users/{id}/delete', 'AdminController@deleteUser');
$router->get('/admin/products', 'AdminController@products');
$router->get('/admin/products/approve/{id}', 'AdminController@approveProduct');
$router->post('/admin/products/approve/{id}', 'AdminController@approveProduct');
$router->get('/admin/stats', 'AdminController@stats');

// Exports CSV
$router->get('/admin/export/users', 'ExportController@users');
$router->get('/admin/export/products', 'ExportController@products');
$router->get('/admin/export/orders', 'ExportController@orders');

// ================================================================
// API REST
// ================================================================

$router->get('/api', 'ApiController@index');
$router->get('/api/products', 'ApiController@products');
$router->get('/api/products/{slug}', 'ApiController@product');
$router->get('/api/categories', 'ApiController@categories');

// ================================================================
// SÉCURITÉ (Auth admin)
// ================================================================

$router->get('/admin/security', 'SecurityController@index');
$router->get('/admin/security/api/events', 'SecurityController@apiEvents');
$router->get('/admin/security/download/{date}', 'SecurityController@downloadLog');
$router->get('/licence-fondateur', 'HomeController@licenceFondateur');

// ================================================================
// DISPATCHER - TOUJOURS EN DERNIER !
// ================================================================

$router->dispatch();

