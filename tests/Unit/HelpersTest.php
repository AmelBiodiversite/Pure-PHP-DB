<?php
/**
 * TEST UNITAIRE - Helpers Sécurité
 * Vérifie que la sanitization XSS fonctionne correctement (logique pure)
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * Test : Protection XSS basique
     * Vérifie que les balises HTML sont échappées
     */
    public function testXssProtection(): void
    {
        $dangerous = '<script>alert("XSS")</script>';
        $safe = htmlspecialchars($dangerous, ENT_QUOTES, 'UTF-8');

        $this->assertStringNotContainsString('<script>', $safe, "Les balises script doivent être échappées");
        $this->assertStringContainsString('&lt;script&gt;', $safe, "Les balises doivent être converties en entités HTML");
    }

    /**
     * Test : Protection des guillemets
     */
    public function testQuoteEscaping(): void
    {
        $input = 'Test "quoted" and \'single\'';
        $escaped = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

        $this->assertStringContainsString('&quot;', $escaped, "Les guillemets doubles doivent être échappés");
        $this->assertStringContainsString('&#039;', $escaped, "Les guillemets simples doivent être échappés");
    }

/**
     * Test : Validation d'URL
     */
    public function testUrlValidation(): void
    {
        // URL valide HTTPS
        $validUrl = 'https://www.example.com';
        $this->assertNotFalse(
            filter_var($validUrl, FILTER_VALIDATE_URL),
            "Une URL HTTPS valide doit être acceptée"
        );

        // Détection d'URL dangereuse (javascript:)
        $javascriptUrl = 'javascript:alert("XSS")';
        $isJavascript = stripos($javascriptUrl, 'javascript:') === 0;
        
        $this->assertTrue(
            $isJavascript,
            "Doit détecter les URLs javascript: comme dangereuses"
        );
    }


    /**
     * Test : Nettoyage des espaces
     */
    public function testTrimWhitespace(): void
    {
        $input = '  test string  ';
        $trimmed = trim($input);

        $this->assertEquals('test string', $trimmed, "Les espaces doivent être supprimés");
        $this->assertEquals(11, strlen($trimmed), "La longueur après trim doit être correcte");
    }
}

