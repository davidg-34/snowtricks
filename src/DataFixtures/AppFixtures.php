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
        $users = [];
        for ($i = 0; $i < 5; $i++) {
            $user = new Users();
            $user->setEmail($this->faker->email())
                 ->setUsername($this->faker->userName())
                 ->setIsVerified(true);
            $password = $this->hasher->hashPassword($user, 'pass_1234');
            $user->setPassword($password);
            $user->setAvatar($this->faker->imageUrl(64, 64, 'people'));
            $manager->persist($user);
            $users[] = $user;
        }

        $categories = [];
        foreach (['grab', 'butter', 'spins', 'rails'] as $values) {
            $category = new Category();
            $category->setName($values);
            $manager->persist($category);
            $categories[] = $category;
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
            $trick->setUsers($users[array_rand($users)]);
            // Create random medias for this trick
            $trick_medias = [];
            // First, we add an image to the trick
            $media = new Medias();
            $media->setMedia('snow' . rand(1,6) . '.png');
            $media->setType('picture');
            $manager->persist($media);
            $trick_medias[] = $media;
            $trick->addMedia($trick_medias[0]);
            // then we add two more medias : image or video
            for ($m = 1; $m < 3 ; $m++) {
                $media = new Medias();
                // Générer un nombre aléatoire pour déterminer le type de média
                if (rand(0, 1) == 0) {
                    $media->setMedia('snow' . rand(1,6) . '.png');
                    $media->setType('picture');
                } else {
                    $media->setMedia('snow' . rand(1,4) . '.mp4');
                    $media->setType('video');
                }
                $manager->persist($media);
                $trick_medias[] = $media;
                $trick->addMedia($trick_medias[$m]);
            }
            $trick->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTime));
            $trick->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTime));
            $manager->persist($trick);
            $tricks[] = $trick;
        }

        // create comments
        $comments = [];
        foreach ($users as $user) {
            for ($i = 0; $i < 10; $i++) {
                $comment = new Comments();
                $comment->setContent($this->faker->sentence);
                $comment->setCreatedAt(new \DateTimeImmutable());
                $comment->setUser($users[array_rand($users)]);
                $comment->setTricks($tricks[array_rand($tricks)]);
                $manager->persist($comment);
                $comments[] = $comment;
            }
        }
        /* dd(['users'=> $users, 'categories' => $categories, 'medias' => $trick_medias, 'tricks' => $tricks]);*/
        $manager->flush();

    }

}