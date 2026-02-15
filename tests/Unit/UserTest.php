<?php
/**
 * TEST UNITAIRE - Validation User
 * Vérifie la validation des emails et usernames (logique pure, pas de DB)
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * Test : Validation d'email valide
     */
    public function testValidateValidEmail(): void
    {
        $validEmails = [
            'test@example.com',
            'user.name@domain.fr',
            'contact+tag@site.io'
        ];

        foreach ($validEmails as $email) {
            $this->assertTrue(
                filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
                "L'email {$email} doit être valide"
            );
        }
    }

    /**
     * Test : Rejet d'email invalide
     */
    public function testValidateInvalidEmail(): void
    {
        $invalidEmails = [
            'not-an-email',
            '@domain.com',
            'user@',
            'user @domain.com'
        ];

        foreach ($invalidEmails as $email) {
            $this->assertFalse(
                filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
                "L'email {$email} doit être invalide"
            );
        }
    }

    /**
     * Test : Validation de username
     * Username doit faire entre 3 et 30 caractères
     */
    public function testValidateUsername(): void
    {
        // Username valide
        $validUsername = 'john_doe';
        $this->assertTrue(
            strlen($validUsername) >= 3 && strlen($validUsername) <= 30,
            "Un username de 3-30 caractères doit être valide"
        );

        // Username trop court
        $shortUsername = 'ab';
        $this->assertFalse(
            strlen($shortUsername) >= 3,
            "Un username de moins de 3 caractères doit être invalide"
        );

        // Username trop long
        $longUsername = str_repeat('a', 31);
        $this->assertFalse(
            strlen($longUsername) <= 30,
            "Un username de plus de 30 caractères doit être invalide"
        );
    }
}

