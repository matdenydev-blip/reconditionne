<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Tests\DatabaseWebTestCase;
use App\Entity\Category;

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

    public function testUnEtatInvalideEstRefuse(): void
    {
        $category = $this->creerCategorie();

        $crawler = $this->client->request('GET', '/admin/produits/new');
        $form = $crawler->selectButton('Enregistrer')->form();
        $form['product[name]'] = 'Test';
        $form['product[description]'] = 'Test';
        $form['product[price]'] = '10.00';
        $form['product[category]'] = $category->getId();
        $form['product[state]']->disableValidation()->setValue('Excellent');
        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        $this->em->clear();
        $this->assertCount(0, $this->em->getRepository(Product::class)->findAll());
    }

    public function testAjouterUneCategorie(): void
    {
        $crawler = $this->client->request('GET', '/admin/category/new');
        $form = $crawler->selectButton('Enregistrer')->form();
        $form['category[name]'] = 'Nouvelle Catégorie';
        $form['category[description]'] = 'Description de la nouvelle catégorie';
        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/category');

        $this->em->clear();
        $category = $this->em->getRepository(Category::class)->findOneBy(['name' => 'Nouvelle Catégorie']);
        $this->assertNotNull($category);
    }

    public function testSupprimerUneCategorie(): void
    {
        $category = $this->creerCategorie('Categorie à supprimer');
        $id = $category->getId();

        $this->client->request('GET', '/admin/category/' . $id . '/edit');
        $this->client->submitForm('Supprimer');

        $this->assertResponseRedirects('/admin/category');

        $this->em->clear();
        $category = $this->em->getRepository(Category::class)->find($id);
        $this->assertNull($category);
    }

    public function testSupprimerUneCategorieQuiContientDesProduitsEstRefuse(): void
    {
        $category = $this->creerCategorie('Smartphones');
        $this->creerProduit($category, 'iPhone 12');
        $id = $category->getId();

        $this->client->request('GET', '/admin/category/' . $id . '/edit');
        $this->client->submitForm('Supprimer');

        $this->assertResponseRedirects('/admin/category');
        $this->client->followRedirect();
        $this->assertSelectorTextContains('body', 'contient encore des produits');

        $this->em->clear();
        $this->assertNotNull($this->em->getRepository(Category::class)->find($id));
    }

    public function testUneCategorieSansDescriptionEstRefusee(): void
{
    $crawler = $this->client->request('GET', '/admin/category/new');
    $form = $crawler->selectButton('Enregistrer')->form();
    $form['category[name]'] = 'Sans description';
    $this->client->submit($form);

    $this->assertResponseStatusCodeSame(422);
    $this->em->clear();
    $this->assertCount(0, $this->em->getRepository(Category::class)->findAll());
}

public function testUnPrixNegatifEstRefuse(): void
{
    $category = $this->creerCategorie();

    $crawler = $this->client->request('GET', '/admin/produits/new');
    $form = $crawler->selectButton('Enregistrer')->form();
    $form['product[name]'] = 'Prix négatif';
    $form['product[description]'] = 'Description';
    $form['product[price]'] = '-50';
    $form['product[state]'] = 'bon';
    $form['product[category]'] = $category->getId();
    $this->client->submit($form);

    $this->assertResponseStatusCodeSame(422);
    $this->em->clear();
    $this->assertCount(0, $this->em->getRepository(Product::class)->findAll());
}

}
