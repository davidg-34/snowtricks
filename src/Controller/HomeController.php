<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tricks;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tricks = $entityManager->getRepository(Tricks::class)->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'tricks' => $tricks
        ]);
    }

    /* #[Route('/tricks', name: 'app_tricks')]
    public function tricks(EntityManagerInterface $entityManager): Response
    {
        $tricks = $entityManager->getRepository(Tricks::class)->findAll();
        return $this->render('home/tricks.html.twig', [
            'tricks' => $tricks
        ]);
    } */
    
    /** 
     * 
     * TODO
     * 
     * Cette route et cette methode devraient bouger dans trickControler
     * /
     */
    #[Route('/tricks/{id}', name: 'app_show')]
    public function show(EntityManagerInterface $entityManager, $id)
    {
        $trick = $entityManager->getRepository(Tricks::class)->find($id);

        return $this->render('home/show.html.twig', [
            'trick' => $trick
        ]);
    }
}
