<?php
/**
 * TEST UNITAIRE - Classe CSRF
 * Vérifie que la génération et validation de tokens CSRF fonctionne correctement
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\CSRF;

class CSRFTest extends TestCase
{
    /**
     * setUp : Exécuté AVANT chaque test
     * Nettoie la session pour isoler les tests
     */
    protected function setUp(): void
    {
        // Démarre une session propre pour chaque test
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Nettoie le token CSRF de la session précédente
        unset($_SESSION['csrf_token']);
    }

    /**
     * tearDown : Exécuté APRÈS chaque test
     * Nettoie la session
     */
    protected function tearDown(): void
    {
        // Nettoie le token après chaque test
        unset($_SESSION['csrf_token']);
    }

    /**
     * Test : Génération de token CSRF
     * Vérifie qu'un token est bien créé et stocké en session
     */
    public function testGenerateToken(): void
    {
        // La session est déjà démarrée et propre grâce à setUp()

        // Génère un token avec random_bytes(32) + bin2hex()
        $token = CSRF::generateToken();

        // Assertions : vérifie que le token est valide
        $this->assertNotEmpty($token, "Le token CSRF ne doit pas être vide");
        $this->assertIsString($token, "Le token doit être une chaîne");
        $this->assertEquals(64, strlen($token), "Le token doit faire 64 caractères (bin2hex de 32 bytes)");
        $this->assertArrayHasKey('csrf_token', $_SESSION, "Le token doit être en session");
    }

    /**
     * Test : Validation de token CSRF valide
     */
    public function testValidateValidToken(): void
    {
        // La session est déjà démarrée et propre grâce à setUp()
        
        // Génère un token
        $token = CSRF::generateToken();

        // Valide ce token avec hash_equals (timing-safe)
        $isValid = CSRF::validateToken($token);

        $this->assertTrue($isValid, "Un token valide doit être accepté");
    }

    /**
     * Test : Rejet de token CSRF invalide
     * setUp() a nettoyé la session, donc pas de token valide en session
     */
    public function testValidateInvalidToken(): void
    {
        // La session est propre, pas de token valide
        // On teste directement avec un faux token
        $isValid = CSRF::validateToken('fake_token_12345');

        $this->assertFalse($isValid, "Un faux token doit être rejeté (aucun token valide en session)");
    }
    
    /**
     * Test : Rejet de token CSRF incorrect (mais un token valide existe)
     * Vérifie que hash_equals() rejette bien un mauvais token
     */
    public function testValidateWrongToken(): void
    {
        // Génère un token valide avec random_bytes()
        $validToken = CSRF::generateToken();
        
        // Essaye de valider un AUTRE token (pas celui généré)
        // hash_equals() doit rejeter en temps constant
        $isValid = CSRF::validateToken('different_fake_token_67890');

        $this->assertFalse($isValid, "Un token différent du token en session doit être rejeté");
        
        // Vérifie que le bon token fonctionne toujours
        $this->assertTrue(CSRF::validateToken($validToken), "Le vrai token doit toujours fonctionner");
    }
    
    /**
     * Test : Génération du champ HTML
     * Vérifie que field() génère bien un input hidden avec le token
     */
    public function testFieldGeneration(): void
    {
        // Génère le champ HTML
        $field = CSRF::field();
        
        // Vérifie la structure HTML
        $this->assertStringContainsString('<input type="hidden"', $field, "Doit contenir un input hidden");
        $this->assertStringContainsString('name="csrf_token"', $field, "Doit avoir name='csrf_token'");
        $this->assertStringContainsString('value="', $field, "Doit avoir un attribut value");
        
        // Vérifie que le token est échappé (htmlspecialchars)
        $this->assertStringNotContainsString('<script>', $field, "Le token doit être échappé contre XSS");
    }
}
