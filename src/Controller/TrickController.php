<?php

namespace App\Controller;


use App\Entity\Video;

use App\Entity\Tricks;
use App\Entity\Picture;
use App\Form\TrickForm;
use App\Entity\Comments;
use App\Form\CommentType;
use App\Service\FileUploader;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    #[Route('/tricks', name: 'app_tricks')]
    public function tricks(EntityManagerInterface $entityManager): Response
    {
        $tricks = $entityManager->getRepository(Tricks::class)->findAll();
        return $this->render('home/tricks.html.twig', [
            //'tricks' => $tricks
        ]);
    }

    // Ajout d'une figure / Mise à jour d'une figure
    #[Route('/tricks/new', name: 'trick_add')]
    #[Route('/tricks/{slug}/edit', name: 'trick_edit')]
    public function form(Request $request, EntityManagerInterface $entityManager, ?string $slug = null, FileUploader $fileUploader): Response
    {
        // Vérifier si on est en mode création ou modification
        $isNew = !$slug; // Si $slug est null, c'est une création

        // Récupération du Trick existant ou création d'un nouveau Trick
        if (!$this->getUser()) {
            return $this->redirectToRoute('home');
        }
        
        if ($slug) {
            $trick = $entityManager->getRepository(Tricks::class)->findOneBy(['slug' => $slug]);
        } else {
            $trick = new Tricks();
            $trick->setUsers($this->getUser());
        }

        // Création du formulaire et passer l'option is_new pour coverPhoto
        $form = $this->createForm(TrickForm::class, $trick, ['is_new' => $isNew]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Gestion de l'upload de la photo à la une (coverPhoto)
            /** @var UploadedFile $coverPhoto */
            $coverPhoto = $form->get('coverPhoto')->getData();
            if ($coverPhoto) {
                $coverPhoto = $fileUploader->upload($coverPhoto);
                $trick->setCoverPhoto($coverPhoto);
            }

            // Gestion de l'upload des images
            $images = $form->get('pictures')->getData();

            foreach ($images as $image) {
                if ($image instanceof UploadedFile) {
                    $imgName = $fileUploader->upload($image);
                    $picture = new Picture();
                    $picture->setName($imgName);
                    $trick->addPicture($picture);
                }
            }
            // Gestion de l'url des vidéos
            $videos = $form->get('videos')->getData();
            foreach ($videos as $videoData) {
                $video = new Video();
                $video->setName($videoData->getName());
                $trick->addVideo($video);
            }
            
            // Mise à jour du slug et autres propriétés du Trick
            $slugger = new AsciiSlugger();
            $trick->setSlug($slugger->slug($trick->getName()));
            
            // Persist et flush des données
            $entityManager->persist($trick);
            $entityManager->flush();

            // Flash message de succès
            $this->addFlash('success', 'Le trick a été enregistré avec succès !');

            // Redirection vers la page d'accueil ou autre page
            return $this->redirectToRoute('home', ['slug' => $trick->getSlug()]);
        }

        // Affichage du formulaire
        return $this->render('trick/edit.html.twig', [
            'formTrick' => $form->createView(),
            'trick' => $trick,
            'editMode' => $trick->getId() !== null,
        ]);
    }

    // Suppression d'une figure
    #[Route('/tricks/{id}/delete', name: 'trick_delete')]
    public function delete(EntityManagerInterface $entityManager, $id): RedirectResponse
    {
        $trick = $entityManager->getRepository(Tricks::class)->find($id);
        $entityManager->remove($trick);
        $entityManager->flush();

        $this->addFlash('success', 'Figure supprimée !');
        return $this->redirectToRoute('app_tricks');
    }
    // Suppression d'un media de figure - Picture
    #[Route('/tricks/{id}/picture/{pictureId}/delete', name: 'trick_picture_delete')]
    public function deletePicture(EntityManagerInterface $entityManager, $id, $pictureId): RedirectResponse
    {
        $trick = $entityManager->getRepository(Tricks::class)->find($id);
        $picture = $entityManager->getRepository(Picture::class)->find($pictureId);

        if ($picture) {
            // Suppression de l'image sur le HD 
            unlink($this->getParameter('medias_directory') . '/' . $picture->getName());
            // Suppression de l'image en BDD
            $entityManager->remove($picture);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Image supprimée !');
        return $this->redirectToRoute('trick_edit', ['slug' => $trick->getSlug()]);
    }

    // Suppression d'un media de figure - Video
    #[Route('/tricks/{id}/video/{videoId}/delete', name: 'trick_video_delete')]
    public function deleteVideo(EntityManagerInterface $entityManager, $id, $videoId): RedirectResponse
    {
        $trick = $entityManager->getRepository(Tricks::class)->find($id);
        $video = $entityManager->getRepository(Video::class)->find($videoId);
       
        if ($video) {
            $entityManager->remove($video);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Vidéo supprimée !');
        return $this->redirectToRoute('trick_edit', ['slug' => $trick->getSlug()]);
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
        $commentPerPage = 10;
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

    // loadMore
    #[Route('/ajax/tricks/{page}', name: 'tricks_load_more')]
    public function loadMore(EntityManagerInterface $entityManager, $page):response
    {
        $currentPage = intval($page);
        $tricks = $entityManager->getRepository(Tricks::class)->findAll($currentPage);
        return $this->render('trick/tricks.html.twig', [
            'tricks' => $tricks
        ]);

    }
}
