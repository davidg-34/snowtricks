<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Tricks;
use App\Entity\Users;
use App\Entity\Picture;
use App\Entity\Video;
use App\Entity\Comments;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    private SluggerInterface $slugger;

    public function __construct(UserPasswordHasherInterface $hasher, SluggerInterface $slugger)
    {
        $this->hasher = $hasher;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        // Create User
        $user = new Users();
        $user->setEmail('user@exemple.com')
            ->setUsername('TestUser')
            ->setIsVerified(true);
        $password = $this->hasher->hashPassword($user, 'pass_1234');
        $user->setPassword($password);
        $user->setAvatar('https://via.placeholder.com/64');
        $manager->persist($user);

        // Create Categories
        $categories = [];
        foreach (['grab', 'butter', 'spins', 'rails'] as $value) {
            $category = new Category();
            $category->setName($value);
            $manager->persist($category);
            $categories[$value] = $category;
        }

        // Create Tricks
        $tricksData = [
            [
                'name' => 'Le Indy',
                'description' => 'Attrape le carré des orteils de ta planche, entre les fixations, avec ta main arrière.',
                'coverPhoto' => 'indy.jpg',
                'category' => 'grab'
            ],
            [
                'name' => 'Le Stalefish',
                'description' => 'Passe la main derrière ton genou arrière et attrape le carre de ta planche entre les fixations, côté talon, avec ta main arrière.',
                'coverPhoto' => 'stalefish.jpg',
                'category' => 'grab'
            ],
            [
                'name' => 'Le Weddle',
                'description' => '(anciennement appelé Mute Grab) - Du nom de Chris Weddle, l\'inventeur, attrape le carre des orteils entre les fixations avec ta main avant.',
                'coverPhoto' => 'weddle.jpg',
                'category' => 'grab'
            ],
            [
                'name' => 'Le Ollie',
                'description' => 'Consiste à faire décoller votre planche du sol, pour effectuer un petit saut.',
                'coverPhoto' => 'ollie.jpg',
                'category' => 'butter'
            ],
            [
                'name' => 'Le Tail Press',
                'description' => 'Le Tail Press est initié en déplaçant ton poids vers l\'arrière de ta planche tout en restant droit et en soulevant le Nose de la neige.',
                'coverPhoto' => 'tailpress.jpg',
                'category' => 'butter'
            ],
            [
                'name' => 'Le Nose Press',
                'description' => 'C\'est l\’opposé du Tail Press. Le Nose Press exige que ton poids soit sur l\'avant de la planche, avec l\'arrière décollé de la neige.',
                'coverPhoto' => 'nosepress.jpg',
                'category' => 'butter'
            ],
            [
                'name' => 'Le Backflip',
                'description' => 'Un Backflip fait tourner la planche perpendiculairement à la neige, tu fais donc un Flip directement en arrière, en stabilisant la planche lors de l\'atterrissage.',
                'coverPhoto' => 'backflip.jpg',
                'category' => 'spins'
            ],
            [
                'name' => 'Le Rodéo',
                'description' => 'Un Rodéo est un Frontflip avec un twist. Littéralement. Lorsque tu arrives sur le rebord du saut, déclenche un virage Frontside. Puis, décolle la carre des orteils de ta planche, en continuant la rotation, de façon à effectuer un Frontflip avec un Frontside 180, puis atterris en Switch.',
                'coverPhoto' => 'rodeo.jpg',
                'category' => 'spins'
            ],
            [
                'name' => 'Le 50-50',
                'description' => 'Il s\'agit de chevaucher un rail ou un box avec ta planche en ligne droite sur la structure.',
                'coverPhoto' => '50-50.jpg',
                'category' => 'rails'
            ],
            [
                'name' => 'Le Frontside',
                'description' => 'Le frontside 180 est un ollie combiné d\'un demi tour, soit une rotation de 180 degrés. Comme pour un ollie, l\'objectif est d\'utiliser le flex de la board pour décoller et effectuer la rotation.',
                'coverPhoto' => 'frontside.jpg',
                'category' => 'rails'
            ]
        ];

        $tricks = [];

        foreach ($tricksData as $data) {
            $trick = new Tricks();
            $trick->setName($data['name']);
            $trick->setSlug($this->slugger->slug($data['name'])->toString());
            $trick->setDescription($data['description']);
            $trick->setCoverPhoto($data['coverPhoto']);
            $trick->setCategory($categories[$data['category']]);
            $trick->setCreatedAt(new \DateTimeImmutable());
            $trick->setUsers($user);
            $manager->persist($trick);
            $tricks[] = $trick;
        }

        // Add pictures to each trick
        foreach ($tricks as $trick) {
            for ($m = 0; $m < 4; $m++) {
                $picture = new Picture();
                $picture->setName('snow' . rand(1, 6) . '.png');
                $picture->setTrick($trick);
                $manager->persist($picture);
                $trick->addPicture($picture);
            }
        }

        // Define videos
        $videos = [
            '<iframe src="https://www.youtube.com/embed/1AAF9QBaogk?si=s1bAdnHl7J3L9ivB&amp;start=6" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
            '<iframe src="https://www.youtube.com/embed/_b85ToCAufU?si=uoXIxOMTbfYkMPGu&amp;start=7" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
            '<iframe src="https://www.youtube.com/embed/W853WVF5AqI?si=eQG-u0gya-oDaJw0&amp;start=10" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
            '<iframe src="https://www.youtube.com/embed/Bj3EmTrlEbw?si=pJUhduYdRBQaHeVK&amp;start=5" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'
        ];

        // Add videos to each trick
        foreach ($tricks as $trick) {
            foreach ($videos as $value) {
                $video = new Video();
                $video->setName($value);
                $video->setTrick($trick);
                $manager->persist($video);
                $trick->addVideo($video);
            }
            $trick->setCreatedAt(new \DateTimeImmutable('now - ' . rand(0, 365) . ' days'));
            $trick->setUpdatedAt(new \DateTimeImmutable('now - ' . rand(0, 365) . ' days'));
            $manager->persist($trick);
        }

        // Create comments
        $commentsContent = [
            "Belle figure.", "La figure semble difficile.", "Je l'ai essayé, ce n'est pas facile", "Pas mal.", "Belle combinaison", "Wow, j'aime bien celle là.",
            "J'espère y arriver un jour", "Cest impressionnant", "La technique est difficile", "Je l'ai réussi ce week-end", "Je n'y arrive pas encore", "J'aime bien cette figure"
        ];

        foreach ($tricks as $trick) {
            $numberOfComments = rand(1, 12);
            for ($i = 0; $i < $numberOfComments; $i++) {
                $comment = new Comments();
                $comment->setContent($commentsContent[array_rand($commentsContent)]);
                $comment->setCreatedAt(new \DateTimeImmutable());
                $comment->setUser($user);
                $comment->setTricks($trick);
                $manager->persist($comment);
            }
        }
        /* dd(['users'=> $user, 'categories' => $category, 'video' => $video, 'picture' => $picture, 'tricks' => $tricks]); */
        $manager->flush();
    }
}
