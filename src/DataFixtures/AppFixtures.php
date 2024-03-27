<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use App\Entity\Tricks;
use App\Entity\Users;
use App\Entity\TrickGroup;
use App\Entity\TrickImage;
use App\Entity\comments;


class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');

    }

    public function load(ObjectManager $manager): void
    {
        // Create Users
        for ($i = 0; $i < 10; $i++) {
            $user = new Users();
            $user->setEmail($faker->email())
                ->setPassword($this->encoder->encodePassword($user, '00000000'))
                ->setUsername($faker->userName())
                ->setIsVerified(true);

            $manager->persist($user);

            $users[] = $user;
        }

    }

}
