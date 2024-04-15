<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker\Factory;
use Faker\Generator;
use App\Entity\Tricks;
use App\Entity\Users;
use App\Entity\Medias;
use App\Entity\TrickGroup;
use App\Entity\TrickImage;
use App\Entity\Comments;

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
        // $this->faker->addProvider('imageUrl');
        // $this->faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($this->faker));
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
            $user->setAvatar("none");
            $manager->persist($user);
            $users[] = $user;
        }

        // Create categories
        $categories = [];
        for ($i = 0; $i < 3 ; $i++) {
            $catName = $this->faker->Words(1, true);
            $category = new Category();
            $category->setName($catName);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Create random medias
        $medias = [];
        for ($i = 0; $i < 3 ; $i++) {
            $media = new Medias();
            $media->setMedia($this->faker->imageUrl());
            $media->setType('picture');
            $manager->persist($media);
            $medias[] = $media;
        }

        // Create tricks
        for ($i=1; $i <= 10; $i++) {
            $trick = new Tricks();
            $name = $this->faker->Words(1, true);
            $description = $this->faker->Text();
            $media = $this->faker->imageUrl();
            $trick->setName($name);
            $trick->setDescription($description);
            $trick->setSlug($this->slugger->slug($name));
            $trick->setCategory($categories[array_rand($categories)]);
            $trick->addMedia($medias[array_rand($medias)]);
            $trick->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTime));
            $trick->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTime));
            $manager->persist($trick);            
            $tricks[] = $trick;
        }        
        /* dd(['users'=> $users, 'categories' => $categories, 'medias' => $medias, 'tricks' => $tricks]); */
        $manager->flush();

    }

}
