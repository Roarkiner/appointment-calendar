<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Faker\Factory;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * Faker Generator
     *
     * @var Generator
     */
    private Generator $faker;
    
    /**
     * Class that hashs the password
     *
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $passHasher;

    public function __construct(UserPasswordHasherInterface $passHasher)
    {
        $this->faker = Factory::create('en_GB');
        $this->passHasher = $passHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail("emailerdemo8@gmail.com")
            ->setLastname("Emailer")
            ->setFirstname("Demo")
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($this->passHasher->hashPassword($admin, "admin"));
        $manager->persist($admin);

        $user = new User();
        $user->setEmail("sample_user@gmail.com")
            ->setLastname("Sample")
            ->setFirstname("User")
            ->setRoles(["ROLE_USER"])
            ->setPassword($this->passHasher->hashPassword($admin, "user"));
        $manager->persist($admin);

        $manager->flush();
    }
}
