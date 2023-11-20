<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tricks;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use App\Form\Trick;

class TrickController extends AbstractController
{
    #[Route('/tricks/{id}/edit', name: 'trick_edit')]
    public function edit(EntityManagerInterface $entityManager, $id): Response
    {
        $trick = $entityManager->getRepository(Tricks::class)->find($id);
        
        $form = $this->createForm(Trick::class, $trick);

        // symfony 6.2+
        /* return $this->render('trick/edit.html.twig', [            
            'trick' => $trick,
            'form' => $form
        ]); */
        return $this->renderForm('trick/edit.html.twig', [
            'form' => $form,
        ]);
    }

}
