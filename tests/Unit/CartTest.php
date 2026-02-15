<?php
/**
 * TEST UNITAIRE - Calculs Panier
 * Vérifie que les calculs de prix et totaux sont corrects (logique pure)
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    /**
     * Test : Calcul du total panier
     * Vérifie que prix × quantité fonctionne correctement
     */
    public function testCalculateTotalPrice(): void
    {
        // Simule des articles dans le panier
        $items = [
            ['price' => 10.00, 'quantity' => 2],  // 20€
            ['price' => 15.50, 'quantity' => 3],  // 46.50€
            ['price' => 5.00, 'quantity' => 1]    // 5€
        ];

        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $this->assertEquals(71.50, $total, "Le total du panier doit être 71.50€");
    }

    /**
     * Test : Calcul avec TVA (20%)
     */
    public function testCalculateTotalWithTax(): void
    {
        $subtotal = 100.00;
        $taxRate = 0.20; // 20% TVA française
        
        $tax = $subtotal * $taxRate;
        $total = $subtotal + $tax;

        $this->assertEquals(20.00, $tax, "La TVA doit être 20€");
        $this->assertEquals(120.00, $total, "Le total TTC doit être 120€");
    }

    /**
     * Test : Panier vide
     */
    public function testEmptyCart(): void
    {
        $items = [];
        
        $total = array_reduce($items, function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $this->assertEquals(0, $total, "Un panier vide doit avoir un total de 0€");
    }

    /**
     * Test : Arrondi des prix (2 décimales)
     */
    public function testPriceRounding(): void
    {
        $price = 12.3456;
        $rounded = round($price, 2);

        $this->assertEquals(12.35, $rounded, "Le prix doit être arrondi à 2 décimales");
    }
}
