<?php

namespace App\Tests;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class DatabaseWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);

        $this->em->createQuery('DELETE FROM App\Entity\Product')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\Category')->execute();
        $this->em->createQuery('DELETE FROM App\Entity\User')->execute();
    }

    protected function visiter(string $url): void
    {
        $this->em->clear();
        $this->client->request('GET', $url);
    }

    protected function creerCategorie(string $nom = 'Smartphones'): Category
    {
        $category = new Category();
        $category->setName($nom);
        $category->setDescription('Description de ' . $nom);
        $this->em->persist($category);
        $this->em->flush();

        return $category;
    }

    protected function creerProduit(Category $category, string $nom = 'iPhone 12'): Product
    {
        $product = new Product();
        $product->setName($nom);
        $product->setDescription('Description de ' . $nom);
        $product->setPrice('349.00');
        $product->setState('bon');
        $product->setCategory($category);
        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }

    protected function creerUtilisateur(string $email, array $roles = [], string $motDePasse = 'secret123'): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setFirstName('Test');
        $user->setLastName('Utilisateur');
        $user->setRoles($roles);
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $user->setPassword($hasher->hashPassword($user, $motDePasse));
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
