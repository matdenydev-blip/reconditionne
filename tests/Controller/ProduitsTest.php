<?php

namespace App\Tests\Controller;

use App\Tests\DatabaseWebTestCase;

class ProduitsTest extends DatabaseWebTestCase
{

    public function testLaListeAfficheLesProduits(): void
    {
        $category = $this->creerCategorie();
        $this->creerProduit($category, 'iPhone 12');
        $this->creerProduit($category, 'Samsung Galaxy S21');

        $this->visiter('/produits');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Nos produits');
        $this->assertSelectorTextContains('body', 'iPhone 12');
        $this->assertSelectorTextContains('body', 'Samsung Galaxy S21');
    }

    public function testLaFicheAfficheTouteLesInformationsDuProduit(): void
    {
        $category = $this->creerCategorie();
        $product = $this->creerProduit($category);

        $this->client->request('GET', '/produits/' . $product->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $product->getName());
        $this->assertSelectorTextContains('.product-description', $product->getDescription());
        $this->assertSelectorTextContains('.product-price', (string) $product->getPrice());
    }

    public function testLaCategorieAfficheLesProduitsAssocies(): void
    {
        $category = $this->creerCategorie();
        $product = $this->creerProduit($category);

        $this->visiter('/categories/' . $category->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', $category->getName());
        $this->assertSelectorTextContains('body', 'iPhone 12');
    }

    public function testAccueilAfficheLesTroisDerniersProduitsSeulement(): void
    {
        $category = $this->creerCategorie();
        foreach (['P1', 'P2', 'P3', 'P4'] as $i => $nom) {
            $product = $this->creerProduit($category, $nom);
            $product->setCreateAt(new \DateTimeImmutable("+$i days"));
            $this->em->flush();
        }

        $this->visiter('/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorCount(3, '.product-name');
        $this->assertSelectorTextContains('body', 'P2');
        $this->assertSelectorTextContains('body', 'P3');
        $this->assertSelectorTextContains('body', 'P4');
        $this->assertSelectorTextNotContains('body', 'P1');
    }
}
