<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Customer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setModel('Produit_ '.$i);
            $product->setDescription('Description produit_'.$i);
            $product->setPrice(mt_rand(10, 100));
            $product->setBrand('Marque produit_'.$i);
            $manager->persist($product);
        }
        
        $customer = new Customer();
        $customer->setName('testnom');
        $customer->setFirstName('testprenom');
        $customer->setPassword(
            $this->passwordEncoder->encodePassword(
            $customer,
            'Test1234?'
            )
        );
        $customer->setMail('mail@gmail.com');
        $manager->persist($customer);

        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setName('Nom_'.$i);
            $user->setFirstName('Prenom_'.$i);
            $user->setMail('mail_'.$i.'@gmail.com');
            $user->setCustomer($customer);
            $manager->persist($user);
        }
        
        $manager->flush();
    }
}
