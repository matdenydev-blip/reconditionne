<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function testUneCategorieConserveSonNomEtSaDescription(): void
    {
        // Arrange + Act : on fabrique une catégorie
        $category = new Category();
        $category->setName('Smartphones');
        $category->setDescription('Catégorie des smartphones');

        // Assert : on vérifie
        $this->assertSame('Smartphones', $category->getName());
        $this->assertSame('Catégorie des smartphones', $category->getDescription());
    }

    public function testUneCategorieNAAucunProduit(): void
    {
        // Arrange + Act : on fabrique une catégorie
        $category = new Category();


        // Assert : la catégorie n'a aucun produit
        $this->assertCount(0, $category->getProducts());
    }

    public function testAjouterUnProduitLieLesDeuxCotesDeLaRelation(): void
{
    $category = new Category();
    $product = new Product();

    $category->addProduct($product);

    $this->assertCount(1, $category->getProducts());
    $this->assertSame($category, $product->getCategory());
}

}
