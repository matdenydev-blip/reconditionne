<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Tests\DatabaseWebTestCase;

class AdminCrudTest extends DatabaseWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $admin = $this->creerUtilisateur('admin@test.fr', ['ROLE_ADMIN']);
        $this->client->loginUser($admin);
    }

    public function testAjouterUnProduit(): void
    {

        $category = $this->creerCategorie('Smartphones');

        $this->client->request('GET', '/admin/produits/new');
        $this->client->submitForm('Enregistrer', [
            'product[name]' => 'Pixel 7',
            'product[description]' => 'Android pur',
            'product[price]' => '250.00',
            'product[state]' => 'moyen',
            'product[category]' => $category->getId(),
        ]);


        $this->assertResponseRedirects('/admin/produits');

        $this->em->clear();
        $product = $this->em->getRepository(Product::class)->findOneBy(['name' => 'Pixel 7']);
        $this->assertNotNull($product);
        $this->assertSame('moyen', $product->getState());
        $this->assertSame('Smartphones', $product->getCategory()->getName());
        $this->assertNotNull($product->getCreateAt());
    }

    public function testModifierUnProduit(): void
    {
        $product = $this->creerProduit($this->creerCategorie(), 'iPhone 12');
        $id = $product->getId();

        $this->client->request('GET', '/admin/produits/' . $id . '/edit');
        $this->client->submitForm('Mettre à jour', [
            'product[name]' => 'iPhone 12 Modifié',
            'product[description]' => 'Description modifiée',
            'product[price]' => '999.99',
            'product[state]' => 'bon',
        ]);

        $this->assertResponseRedirects('/admin/produits');

        $this->em->clear();
        $product = $this->em->getRepository(Product::class)->find($id);
        $this->assertNotNull($product);
        $this->assertSame('iPhone 12 Modifié', $product->getName());
        $this->assertSame('Description modifiée', $product->getDescription());
        $this->assertSame('999.99', (string) $product->getPrice());
        $this->assertSame('bon', $product->getState());
    }

    public function testSupprimerUnProduit(): void
    {
        $product = $this->creerProduit($this->creerCategorie(), 'Produit à supprimer');
        $id = $product->getId();

        $this->client->request('GET', '/admin/produits/' . $id . '/edit');
        $this->client->submitForm('Supprimer');

        $this->assertResponseRedirects('/admin/produits');

        $this->em->clear();
        $product = $this->em->getRepository(Product::class)->find($id);
        $this->assertNull($product);
    }
}
