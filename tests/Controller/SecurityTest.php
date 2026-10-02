<?php

namespace App\Tests\Controller;

use App\Tests\DatabaseWebTestCase;

class SecurityTest extends DatabaseWebTestCase
{
    public function testUnVisiteurNePeutPasAccederAuBackoffice(): void
    {
        $this->client->request('GET', '/admin/produits');

        $this->assertResponseRedirects('/login');
    }

    public function testUnUtilisateurSansRoleAdminEstRefuse(): void
    {
        $user = $this->creerUtilisateur('simple@test.fr');
        $this->client->loginUser($user);

        $this->client->request('GET', '/admin/produits');

        $this->assertResponseStatusCodeSame(403);
    }

    public function testUnAdministrateurAccedeAuBackoffice(): void
    {
        $admin = $this->creerUtilisateur('admin@test.fr', ['ROLE_ADMIN']);
        $this->client->loginUser($admin);

        $this->client->request('GET', '/admin/produits');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des produits');
    }

    public function testLaConnexionRedirigeVersLaListeDesProduits(): void
    {
        $this->creerUtilisateur('admin@test.fr', ['ROLE_ADMIN'], 'secret123');

        $this->client->request('GET', '/login');
        $this->client->submitForm('Se connecter', [
            '_username' => 'admin@test.fr',
            '_password' => 'secret123',
        ]);

        $this->assertResponseRedirects('/admin/produits');
    }

    public function testUnMauvaisMotDePasseRameneSurLaPageDeConnexion(): void
    {
        $this->creerUtilisateur('admin@test.fr', ['ROLE_ADMIN'], 'secret123');

        $this->client->request('GET', '/login');
        $this->client->submitForm('Se connecter', [
            '_username' => 'admin@test.fr',
            '_password' => 'mauvais',
        ]);

        $this->assertResponseRedirects('/login');
    }

    public function testLaDeconnexionRedirigeVersLAccueil(): void
    {
        $admin = $this->creerUtilisateur('admin@test.fr', ['ROLE_ADMIN']);
        $this->client->loginUser($admin);

        $this->client->request('GET', '/logout');

        $this->assertResponseRedirects('/');
    }
}
