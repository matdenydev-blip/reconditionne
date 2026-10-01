<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testUnNouveauProduitADejaUneDateDAjout(): void
    {
        // Arrange + Act : on fabrique un produit
        $product = new Product();

        // Assert : on vérifie
        $this->assertNotNull($product->getCreateAt());
    }

    public function testLeProduitConserveSesInformations(): void
    {
        $category = new Category();
        $category->setName('Smartphones');

        $product = new Product();
        $product->setName('iPhone 12');
        $product->setPrice('349.00');
        $product->setState('bon');
        $product->setCategory($category);

        $this->assertSame('iPhone 12', $product->getName());
        $this->assertSame('349.00', $product->getPrice());
        $this->assertSame('bon', $product->getState());
        $this->assertSame('Smartphones', $product->getCategory()->getName());
    }
}
