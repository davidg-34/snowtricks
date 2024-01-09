<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tricks;
use App\Form\TrickForm;
use App\Entity\Comments;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\RedirectResponse;

//use Doctrine\ORM\EntityManager;
//use Doctrine\ORM\Mapping\Entity;

class TrickController extends AbstractController
{

    #[Route('/tricks', name: 'app_tricks')]
    public function tricks(EntityManagerInterface $entityManager): Response
    {
        $tricks = $entityManager->getRepository(Tricks::class)->findAll();
        return $this->render('home/index.html.twig', [
            'tricks' => $tricks
        ]);
    }

    // Ajout d'une figure / Mise à jour d'une figure
    #[Route('/tricks/new', name: 'trick_add')]
    #[Route('/tricks/{id}/edit', name: 'trick_edit', requirements: ['id' => '\d+'])]
    public function form(Request $request, EntityManagerInterface $entityManager, ?int $id = null): Response
    {
        if ($id) {
            $trick = $entityManager->getRepository(Tricks::class)->find($id);
        } else {
            $trick = new Tricks();
        }
        // on crée le formulaire
        $form = $this->createForm(TrickForm::class, $trick);
        // On traite/soumet la requête du formulaire
        $form->handleRequest($request);
        //On vérifie si le formulaire est soumis ET valide
        if ($form->isSubmitted() && $form->isValid()) {
            //si la figure n'a pas d'id, on crée une nouvelle figure
            if (!$trick->getId()) {
                // On stocke - Prépare à l'enregistrement de l'objet en BDD
                $entityManager->persist($trick);
            }
            // On met à jour la base de données - insertion
            $entityManager->flush();
            // flash message
            $this->addFlash('success', 'Les données sont enregistrées!');
            // On redirige
            return $this->redirectToRoute('app_home', ['id' => $trick->getId()]);
        }
        // symfony 6.2+
        //return $this->render('trick/edit.html.twig', []
        return $this->renderForm('trick/edit.html.twig', [
            'formTrick' => $form,
            'trick' => $trick,
            'editMode' => $trick->getId() !== null
        ]);
    }

    // Suppression d'une figure
    #[Route('/tricks/{id}/delete', name: 'trick_delete')]
    public function delete(EntityManagerInterface $entityManager, $id):RedirectResponse
    {
        $trick = $entityManager->getRepository(Tricks::class)->find($id);
        $entityManager->remove($trick);
        $entityManager->flush();

        $this->addFlash('success', 'Figure supprimée !');
        return $this->redirectToRoute('app_tricks', ['id' => $trick->getId()]);
    }     

    // Commentaire sur les figures
    #[Route('/tricks/{slug}', name: 'app_show')]
    public function show(EntityManagerInterface $entityManager,$slug, Request $request):Response
    {
        $trick = $entityManager->getRepository(Tricks::class)->findOneBy(['slug' => $slug]);
        
        $comment = new Comments();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime());
            $comment->setTricks($trick)
                    ->setUser($this->getUser());
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_show', ['id' => $trick->getId()]);
        }

        return $this->render('home/show.html.twig', [
            'trick' => $trick,
            'commentForm' => $form->createView(),
            'currentPage' => $request->query->get('currentpage') ?: 1 
        ]);
    }
    
 

}
