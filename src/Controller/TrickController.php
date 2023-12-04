<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tricks;
use App\Form\Trick;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;

class TrickController extends AbstractController
{

    #[Route('/tricks', name: 'app_tricks')]
    public function tricks(EntityManagerInterface $entityManager): Response
    {
        $tricks = $entityManager->getRepository(Tricks::class)->findAll();
        return $this->render('home/tricks.html.twig', [
            'tricks' => $tricks
        ]);
    }
    
    #[Route('/tricks/{id}', name: 'app_show')]
    public function show(EntityManagerInterface $entityManager, $id)
    {
        $trick = $entityManager->getRepository(Tricks::class)->find($id);
        /* echo 'toto';
        die; */
        return $this->render('home/show.html.twig', [
            'trick' => $trick
        ]);
    }
    
    #[Route('/tricks/new', name: 'trick_add')]
    #[Route('/tricks/{id}/edit', name:'trick_edit', requirements: ['id' => '\d+'])]
    public function form(?Trick $trick = null, Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        // on édite une figure si elle n'est pas créée
        if (!$trick) {
            $trick = new Trick();
        }

        $trick = $entityManager->getRepository(Tricks::class)->find($id);

        // on crée le formulaire
        $form = $this->createForm(Trick::class, $trick);

        // On traite/soummet la requête du formulaire
        $form->handleRequest($request);

        //On vérifie si le formulaire est soumis ET valide
        if ($form->isSubmitted() && $form->isValid()) {
            //si la figure n'a pas d'id, on crée une nouvelle figure
            if (!$trick->getId()){
                // On stocke
                $entityManager->persist($trick);
            }
            $entityManager->flush();
                // On redirige
            return $this->redirectToRoute('trick_edit', [
                'id' => $trick->getId()
            ]);
        }



        // symfony 6.2+
        /* return $this->render('trick/edit.html.twig', [            
            'trick' => $trick,
            'form' => $form
        ]); */
        return $this->renderForm('trick/edit.html.twig', [
            'form' => $form,
            'trick' => $trick
        ]);
    }

}
