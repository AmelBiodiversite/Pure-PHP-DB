# MarketFlow Pro - Replit Configuration

## Overview

MarketFlow Pro is a premium multi-vendor digital marketplace platform built with pure PHP. It enables sellers to commercialize digital products (templates, ebooks, courses, etc.) with an automated commission system. The platform features a complete MVC architecture, Stripe payment integration, and a modern UI design inspired by Stripe/Linear.

**Core Purpose:** A turnkey marketplace solution for digital products with buyer/seller/admin roles, payment processing, and comprehensive dashboards.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Backend Architecture
- **Framework:** Pure PHP 8.0+ with custom MVC architecture
- **Routing:** Custom RESTful router with `.htaccess` URL rewriting (Apache) or Nginx configuration
- **Database:** MySQL 5.7+/MariaDB 10.2+ with PDO for database abstraction
- **Autoloading:** PSR-4 compatible autoloader
- **Configuration:** Centralized config in `config/config.php`

### Frontend Architecture
- **Styling:** Custom CSS design system with CSS variables for theming
- **JavaScript:** Vanilla JS components (no framework)
- **Features:** Dark mode toggle, responsive design, modal system
- **Design:** Modern UI inspired by Stripe/Linear/Vercel aesthetics

### Authentication & Security
- **Password Hashing:** BCrypt with cost factor 12
- **Session Management:** PHP sessions with CSRF token protection
- **Remember Me:** 30-day cookie-based persistence
- **Role System:** Three roles - buyer, seller, admin
- **Protection:** CSRF, XSS, and SQL injection prevention

### File Structure
- `/config` - Configuration files
- `/css` - Stylesheets and design system
- `/public` - Public assets (JS, uploads)
- `/public/uploads` - User-uploaded files (requires 755 permissions)

### Database Schema
- 17 tables covering users, products, orders, reviews, wishlists, and admin functionality
- Optimized queries with proper indexing

### Development Server
Run with: `php -S 0.0.0.0:5000 -t .` (configured in package.json)

## External Dependencies

### Payment Processing
- **Stripe:** Primary payment gateway for secure transactions
  - Requires API keys (publishable and secret)
  - Webhook configuration needed for payment events
  - Test mode available with card `4242 4242 4242 4242`

### Database
- **MySQL/MariaDB:** Primary data storage
  - Connection via PDO
  - Schema provided via SQL script

### PHP Extensions Required
- `pdo_mysql` - Database connectivity
- `mbstring` - String handling
- `json` - JSON processing
- `curl` - External API calls
- `gd` - Image processing
- `fileinfo` - File type detection

### Web Server
- **Apache 2.4+** with mod_rewrite enabled, or
- **Nginx 1.18+** with appropriate rewrite rules