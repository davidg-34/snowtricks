<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Tricks;
use App\Entity\Medias;
use App\Form\TrickForm;
use Symfony\Component\Filesystem\Filesystem;
use App\Entity\Comments;
use App\Form\CommentType;
use App\Repository\TricksRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
    #[Route('/tricks/{slug}/edit', name: 'trick_edit'/* , requirements: ['id' => '\d+'] */)]
    public function form(Request $request, EntityManagerInterface $entityManager, ?string $slug = null): Response
    {
        if ($slug) {
            $trick = $entityManager->getRepository(Tricks::class)->findOneBy(['slug' => $slug]);
        } else {
            $trick = new Tricks();
        }
        // CREATION DU FORMULAIRE****************
        $form = $this->createForm(TrickForm::class, $trick);
        // On traite/soumet la requête du formulaire
        $form->handleRequest($request);
        //On vérifie si le formulaire est soumis ET valide
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if (!$trick->getId()) {
                    // creation
                    $user = $this->getUser();
                    $trick->setUsers($user);
                }
                // UPLOAD DE FICHIER*************
                // Récupère toutes les données 'médias' à partir du formulaire
                $medias = $form->get('medias')->getData();
                if ($medias) {
                    // Inclus le nom du fichier dans l'url en modifiant le nom de l'image récupérée
                    $fileName = md5(uniqid()) . '.' . $medias->guessExtension();
                    // Déplace l'image récupérée, dans le dossier public/assets/uploads
                    $medias->move(
                        $this->getParameter('medias_directory'),
                        $fileName
                    );
                    // Insère l'image avec le nom de l'image
                    $media = new Medias();
                    $media->setMedia($fileName);
                    $media->setType('picture');
                    $entityManager->persist($media);
                    $trick->addMedia($media);
                }
                // GENERATION DE SLUG***************
                $slugger = new AsciiSlugger();
                $trick->setSlug($slugger->slug($trick->getName()));
                $entityManager->persist($trick);
                $entityManager->flush();
                // flash message
                $this->addFlash('success', 'Les données sont enregistrées!');
                // redirection
                return $this->redirectToRoute('app_home', ['slug' => $trick->getSlug()]);
            }
        }
        return $this->renderForm('trick/edit.html.twig', [
            'formTrick' => $form,
            'trick' => $trick,
            'editMode' => $trick->getId() !== null
        ]);
    }

    // Suppression d'une figure
    #[Route('/tricks/{id}/delete', name: 'trick_delete')]
    public function delete(EntityManagerInterface $entityManager, $id): RedirectResponse
    {
        // Je récupère l'image dans l'array collection à partir de de l'entité tricks
        /* $pictures = $tricks->getMedias();
        $pictures = new Medias();
        foreach($pictures as $picture) {
            // Je reconstitue l'image téléchargée stockée dans la base de données
            $filePicture = $this->getParameter('media_directory').'/'.$picture->getMedia();
            // Je supprime physiquement l'image
            if (file_exists($filePicture)) {
                unlink($filePicture);
            }
        }
        */

        $trick = $entityManager->getRepository(Tricks::class)->find($id);
        $entityManager->remove($trick);
        $entityManager->flush();

        $this->addFlash('success', 'Figure supprimée !');
        return $this->redirectToRoute('app_tricks', ['id' => $trick->getId()]);
    }

    // Commentaire sur les figures
    #[Route('/tricks/{slug}', name: 'app_show')]
    public function show(EntityManagerInterface $entityManager, $slug, Request $request): Response
    {
        // pagination des commentaires
        $currentPage = $request->query->get('page') ?: 1;
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

            return $this->redirectToRoute('app_show', ['slug' => $trick->getSlug()]);
        }
        $commentCount = $trick->getCommentCount();
        $commentPerPage = 3;
        $commentPageCount = ceil($commentCount / $commentPerPage);
        return $this->render('home/show.html.twig', [
            'trick' => $trick,
            'commentForm' => $form->createView(),
            'currentPage' => $currentPage,
            'commentPerPage' => $commentPerPage,
            'commentCount' => $commentCount,
            'commentPageCount' => $commentPageCount
        ]);
    }


    // Suppression d'un media de figure
    #[Route('/tricks/{id}/medias/{mediaId}/delete', name: 'trick_media_delete')]
    public function deleteMedia(EntityManagerInterface $entityManager, $id, $mediaId): RedirectResponse
    {
        $trick = $entityManager->getRepository(Tricks::class)->find($id);        
        $form = $this->createForm(TrickForm::class, $trick);
        
        $media = $entityManager->getRepository(Medias::class)->find($mediaId);
        
        // Suppression de l'image sur le HD
        unlink($this->getParameter('medias_directory') . '/' . $media->getMedia());
        // Suppression de l'image en BDD
        $entityManager->remove($media);
        $entityManager->flush();

        return $this->redirectToRoute('trick_edit', ['slug' => $trick->getSlug()]);        
    }

    #[Route('/load-more', name: 'home/index.html.twig')]
    public function loadMore(EntityManagerInterface $entityManager):response
    {
        // Récupérer les figures à afficher
        $tricks = $this->$entityManager->getRepository(Tricks::class)->getData();
        
        // Retourner les données au format JSON
        return new JsonResponse($tricks);
    }

   
}
