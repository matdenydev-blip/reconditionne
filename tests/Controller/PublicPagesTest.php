<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublicPagesTest extends WebTestCase
{
    public function testLAccueilSAffiche(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue');
    }

    public function testLaListeDesProduitsSAffiche(): void
    {
        $client = static::createClient();
        $client->request('GET', '/produits');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Nos produits');
    }

    public function testLaListeDesCategoriesSAffiche(): void
    {
        $client = static::createClient();
        $client->request('GET', '/categories');

        $this->assertResponseIsSuccessful();
    }

    public function testUneFicheProduitInexistanteRenvoieUne404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/produits/999999');

        $this->assertResponseStatusCodeSame(404);
    }
}
