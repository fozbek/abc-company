<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $manager = null;
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->loadProducts();
        $this->loadUsers();

        $manager->flush();
    }

    public function loadUsers()
    {
        foreach (range(1, 3) as $i) {
            $user = new User();
            $emailAsString = sprintf("customer%s@domain.com", $i);
            $user->setEmail($emailAsString);

            $password = $this->encoder->encodePassword($user, 'verylongpassword');
            $user->setPassword($password);

            $this->manager->persist($user);
        }
    }

    /**
     */
    public function loadProducts(): void
    {
        foreach (range(1, 100) as $i) {
            $product = new Product();
            $productName = sprintf("Product Name %s", $i);
            $product->setName($productName);

            $this->manager->persist($product);
        }
    }
}
