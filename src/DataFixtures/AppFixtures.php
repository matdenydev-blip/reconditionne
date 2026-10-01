<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $donnees = [
            'Smartphones' => [
                ['iPhone 12', 'Écran 6,1 pouces, 128 Go', '349.00', 'bon'],
                ['Galaxy S21', 'Écran 6,2 pouces, 256 Go', '299.90', 'moyen'],
            ],
            'Ordinateurs' => [
                ['MacBook Air 2020', 'Puce M1, 8 Go de RAM', '699.00', 'bon'],
                ['ThinkPad T480', 'Intel i5, 16 Go de RAM', '259.00', 'mauvais'],
            ],
            'Tablettes' => [
                ['iPad 9', 'Écran 10,2 pouces, 64 Go', '229.00', 'bon'],
            ],
        ];

        foreach ($donnees as $nomCategorie => $produits) {
            $category = new Category();
            $category->setName($nomCategorie);
            $category->setDescription('Appareils reconditionnés : ' . $nomCategorie);
            $manager->persist($category);

            foreach ($produits as [$nom, $description, $prix, $etat]) {
                $product = new Product();
                $product->setName($nom);
                $product->setDescription($description);
                $product->setPrice($prix);
                $product->setState($etat);
                $product->setCreateAt(new \DateTimeImmutable());
                $product->setCategory($category);
                $manager->persist($product);
            }
        }

        $manager->flush();
    }
}
