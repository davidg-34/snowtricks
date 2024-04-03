<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use App\Entity\Tricks;
use App\Entity\Users;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\TrickGroup;
use App\Entity\TrickImage;
use App\Entity\comments;


class AppFixtures extends Fixture
{
    private Generator $faker;
    private UserPasswordHasherInterface $hasher;
    private $slugger;

    public function __construct(UserPasswordHasherInterface $hasher, SluggerInterface $slugger)
    {
        $this->hasher = $hasher;
        $this->slugger = $slugger;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {

        // Create Users
        for ($i = 0; $i < 10; $i++) {
            $user = new Users();
            $user->setEmail($this->faker->email())
                 ->setUsername($this->faker->userName())
                 ->setIsVerified(true);
            $password = $this->hasher->hashPassword($user, 'pass_1234');
            $user->setPassword($password);
                
            $manager->persist($user);

            $users[] = $user;
        }

        // Create tricks
        for ($i=1; $i <= 10; $i++) {
            $trick = new Tricks();
            $trick->setName($this-> faker->Words(1, true));
            //$trick->setSlug($faker->Slug());
            //$trick->setSlug($faker->words(4, true));
            // Générer un slug à partir d'un tableau de 2 mots
            //$nbwords = $faker->words(2);
            //$slug = implode('-', $nbwords);                        
            //$trick->setSlug($slug);
            $trick->setDescription($description);
            //$trick->setDescription($faker->Text());
            $trick->setSlug($this->slugger->slug($description));
            $trick->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTime));
            $trick->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTime));
            $manager->persist($trick);
            $tricks[] = $trick;
        }
        dd($tricks);

        $manager->flush();

    }

}
