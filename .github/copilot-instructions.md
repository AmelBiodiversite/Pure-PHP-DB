# Copilot Instructions — MarketFlow Pro

## Commands

```bash
# Development server (web root = public/)
composer dev         # php -S 0.0.0.0:5000 -t public

# Tests
composer test                          # Full PHPUnit suite
vendor/bin/phpunit tests/Unit/CSRFTest.php   # Single test file
vendor/bin/phpunit --filter testGenerateToken  # Single test method

# Static analysis & linting
composer analyse     # PHPStan level 5 on app/ and core/
composer lint        # PHPCS PSR-12 on app/ and core/
composer lint-fix    # PHPCBF auto-fix
```

Environment: copy `.env.example` → `.env`. The app reads `DATABASE_URL` (PostgreSQL) first, then falls back to individual `DB_*` variables.

---

## Architecture

This is a **custom MVC framework** (no Symfony/Laravel). The request lifecycle:

```
public/index.php → index.php (bootstrap)
  → config/config.php     (constants, URL detection)
  → config/session.php    (session_start + security headers)
  → config/security_headers.php
  → config/routes.php     (registers routes + calls $router->dispatch())
      → Router::dispatch() resolves URL → App\Controllers\XxxController@method
          → Controller::render('view/path', $data)
              → app/views/layouts/header.php
              → app/views/<view>.php
              → app/views/layouts/footer.php
```

**Key structural rules:**
- Controllers contain **only PHP logic** — never HTML output.
- Views contain **only HTML + display PHP** — no DB queries.
- All DB access goes through PDO prepared statements (singleton `Core\Database::getInstance()`).
- Routes are defined in `config/routes.php` — **static routes must be declared before dynamic ones** (e.g., `/products/search` before `/products/{slug}`), and `$router->dispatch()` must always be last.

---

## Key Conventions

### Namespaces & Autoloading
- Controllers: `App\Controllers\` → `app/controllers/`
- Models: `App\Models\` → `app/models/`
- Core classes: `Core\` → `core/`
- Helpers (`app/helpers/*.php`) are auto-loaded as plain files (no namespace), always available globally.

### Output escaping
Always use `e($value)` (alias for `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`) in views. Never echo raw user data.

### CSRF protection
Every POST form must include a CSRF token. Two equivalent APIs:
```php
// In controller (render data):
'csrf_token' => \Core\CSRF::generateToken()

// In view:
<?= \Core\CSRF::field() ?>
// or
<input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
```
Validate in controller with `\Core\CSRF::validateToken($_POST['csrf_token'] ?? '')`.

### Authentication guards
Use these `Controller` base methods — do not re-implement role checks:
```php
$this->requireLogin();   // redirects to /login if not authenticated
$this->requireSeller();  // requireLogin() + role === 'seller'
$this->requireAdmin();   // requireLogin() + role === 'admin'
```
Session keys after login: `$_SESSION['user_id']`, `['user_role']` (`admin`/`seller`/`buyer`), `['logged_in']`.

### Rate limiting
Wrap sensitive POST endpoints with:
```php
if (!\Core\RateLimiter::check('action_key', $identifier)) {
    // show error
}
// on failure:
\Core\RateLimiter::attempt('action_key', $identifier, $maxAttempts, $windowMinutes);
// on success:
\Core\RateLimiter::clear('action_key', $identifier);
```

### Security logging
Log security events via `$this->securityLogger->logXxx()` helpers, **not** `error_log()`. The `SecurityLogger` writes to the `security_logs` PostgreSQL table and never throws — a failed log never crashes the app.

### Models
Models extend `Core\Model` and declare `protected $table`. The base class provides `find()`, `findAll()`, `create()`, `update()`, `delete()`, `count()`, `paginate()`. PostgreSQL `INSERT` statements must use `RETURNING id` to retrieve the new row ID. Use `ILIKE` instead of `LIKE` for case-insensitive string matching.

### Flash messages
```php
// Set (controller):
$this->redirectWithMessage('/path', 'Message text', 'success'); // types: success|error|warning|info

// Read (view/layout):
$flash = getFlashMessage(); // returns ['message' => '...', 'type' => '...'] or null
```

### JSON responses (API/AJAX)
```php
$this->jsonResponse($data);           // 200
$this->jsonResponse($data, 422);      // with status code
```

### Environment & configuration
All secrets come from environment variables (via `Env::get()`). Constants defined in `config/config.php`: `APP_URL`, `CSS_URL`, `JS_URL`, `UPLOAD_URL`, `STRIPE_*`, `PLATFORM_COMMISSION` (10%), `MAX_FILE_SIZE` (5 MB).

### Database schema
- `database/schema.sql` — main tables
- `database/security-schema.sql` — `security_logs`, `blocked_ips`, `whitelisted_ips`

### Deployment
Deployed on Railway. `railway.json` and `Dockerfile` are configured. The web root is `/public`. Asset URLs are `/css/`, `/js/`, `/img/`, `/uploads/` (no `/public/` prefix in URLs).
