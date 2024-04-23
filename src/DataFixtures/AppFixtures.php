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
            $user->setAvatar("none");
            $manager->persist($user);
            $users[] = $user;
        }

        // Create categories        
        /* $categories = ['grab', 'butter', 'spin'];
        for ($i = 0; $i < 3 ; $i++) {
            $catName = $this->faker->Words(1, true);
            $category = new Category();
            $category->setName($catName);
            $manager->persist($category);
            $categories[] = $category;
        } */        
        $categories = [];
        foreach (['grab', 'butter', 'spins'] as $values) {
            $category = new Category();
            $category->setName($values);
            //$catName = $this->faker->Words(1, true);
            //$category->setName($catName);
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
            for ($m = 0; $m < 3 ; $m++) {            
                $media = new Medias();
                if ($media->setType('picture')) {
                    $media->setMedia('snow' . rand(1,6) . '.png');
                    $media->setType('picture');
                    //$media->setType($media[array_rand($trick_medias)]);
                    //$media->setMedia('snow' . rand(1,6) . '.png');
                    //$media->setMedia('snow' . rand(1,4) . '.mp4');
                    //$media->setType('snow' . rand(1, 6) . (rand(0, 1) ? '.mp4' : '.png'));
                    //$media->setType('picture' . (rand (0, 1) ? '.png' : 'video' . 'mp4') );
                    $manager->persist($media);
                    $trick_medias[] = $media;
                    $trick->addMedia($trick_medias[$m]);
                } else {
                    $media->setMedia('snow' . rand(1,4) . '.mp4');
                    $media->setType('video');
                    $manager->persist($media);
                    $trick_medias[] = $media;
                    $trick->addMedia($trick_medias[$m]);
                }
            }
            $trick->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTime));
            $trick->setUpdatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTime));
            $manager->persist($trick);
            $tricks[] = $trick;
        }
        /* dd(['users'=> $users, 'categories' => $categories, 'medias' => $trick_medias, 'tricks' => $tricks]);*/
        $manager->flush(); 

    }

}