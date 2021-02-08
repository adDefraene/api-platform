<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Invoice;
use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        //ADMIN MANAGEMENT

        $adminUser = new User();

        $adminUser->setFirstName('Adrien')
            ->setLastName('Defraene')
            ->setEmail('adriendefraene@gmail.com')
            ->setPassword($this->passwordEncoder->encodePassword($adminUser, 'password'))
            ->setRoles(['ROLE_ADMIN'])
        ;

        $manager->persist($adminUser);


        //USERS MANAGEMENT

        for($u=0;$u<10;$u++)
        {
            $chrono = 1;
            $user = new User();
            $user->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setEmail($faker->email())
                ->setPassword($this->passwordEncoder->encodePassword($user, 'password'))
            ;
            $manager->persist($user);

            //USER'S CUSTOMERS MANAGEMENT
            for($c=0 ; $c<rand(5,20) ; $c++)
            {
                $customer = new Customer();
                $customer->setFirstName($faker->firstName())
                    ->setLastName($faker->lastName())
                    ->setEmail($faker->email())
                    ->setCompany($faker->company())
                    ->setUser($user)
                ;
                $manager->persist($customer);

                //BILLs
                for($f=0;$f<rand(3,10);$f++)
                {
                    $invoice = new Invoice();
                    $invoice->setAmount($faker->randomFloat(2,250,5000))
                        ->setSentAt($faker->dateTimeBetween('-6 months'))
                        ->setStatus($faker->randomElement(['SENT','PAID','CANCELLED']))
                        ->setCustomer($customer)
                        ->setChrono($chrono)
                    ;
                    $chrono++;
                    $manager->persist($invoice);
                }
            }
        }
        

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
