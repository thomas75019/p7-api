<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Product;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $product = new Product();
        $product->setBrand('Apple');
        $product->setModel('Iphone 8');
        $product->setPrice(500);
        $manager->persist($product);

        $manager->flush();
    }
}
