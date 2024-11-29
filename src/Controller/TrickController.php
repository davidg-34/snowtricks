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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    // Ajout d'une figure / Mise à jour d'une figure
    #[Route('/tricks/new', name: 'trick_add', methods: ['GET', 'POST'])]
    #[Route('/tricks/{slug}/edit', name: 'trick_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
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
    #[IsGranted('ROLE_USER')]
    public function delete(EntityManagerInterface $entityManager, int $id): RedirectResponse
    {
        $trick = $entityManager->getRepository(Tricks::class)->find($id);
        if ($trick !== null) {
            $entityManager->remove($trick);
            $entityManager->flush();
        } else {
            throw $this->createNotFoundException('Tricks not found.');
        }

        $this->addFlash('success', 'Figure supprimée !');
        return $this->redirectToRoute('home');
    }
    // Suppression d'un media de figure - Picture
    #[Route('/tricks/{id}/picture/{pictureId}/delete', name: 'trick_picture_delete')]
    public function deletePicture(EntityManagerInterface $entityManager, int $id, int $pictureId): RedirectResponse
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
    public function deleteVideo(EntityManagerInterface $entityManager, int $id, int $videoId): RedirectResponse
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
    public function show(EntityManagerInterface $entityManager, string $slug, Request $request): Response
    {
        // Pagination des commentaires
        $currentPage = $request->query->get('page') ?: 1;
        $trick = $entityManager->getRepository(Tricks::class)->findOneBy(['slug' => $slug]);

        // Créer deux instances du formulaire
        $commentLarge = new Comments();
        $commentTiny = new Comments();

        $formLarge = $this->createForm(CommentType::class, $commentLarge);
        $formTiny = $this->createForm(CommentType::class, $commentTiny);

        // Traiter les soumissions des formulaires
        $formLarge->handleRequest($request);
        $formTiny->handleRequest($request);

        if ($formLarge->isSubmitted() && $formLarge->isValid()) {
            $this->processComment($commentLarge, $trick, $entityManager);
            return $this->redirectToRoute('app_show', ['slug' => $trick->getSlug()]);
        }

        if ($formTiny->isSubmitted() && $formTiny->isValid()) {
            $this->processComment($commentTiny, $trick, $entityManager);
            return $this->redirectToRoute('app_show', ['slug' => $trick->getSlug()]);
        }

        $commentCount = $trick->getCommentCount();
        $commentPerPage = 10;
        $commentPageCount = ceil($commentCount / $commentPerPage);

        return $this->render('home/show.html.twig', [
            'trick' => $trick,
            'commentFormLarge' => $formLarge->createView(),
            'commentFormTiny' => $formTiny->createView(),
            'currentPage' => $currentPage,
            'commentPerPage' => $commentPerPage,
            'commentCount' => $commentCount,
            'commentPageCount' => $commentPageCount
        ]);
    }

    private function processComment(Comments $comment, Tricks $trick, EntityManagerInterface $entityManager)
    {
        $comment->setCreatedAt(new \DateTime());
        $comment->setTricks($trick)
                ->setUser($this->getUser());
        $entityManager->persist($comment);
        $entityManager->flush();
    }

    // loadMore tricks
    #[Route('/ajax/tricks/{page}', name: 'tricks_load_more')]
    public function loadMore(EntityManagerInterface $entityManager, int $page): Response
    {
        $limit = 10; // Limite de tricks par page
        $tricks = $entityManager->getRepository(Tricks::class)->findWithPagination($page, $limit);
        return $this->render('trick/tricks.html.twig', [
            'tricks' => $tricks
        ]);
    }

    // loadMore commentaire
    #[Route('/ajax/comments/{trickSlug}/{page}', name: 'comments_load_more')]
    public function loadMoreComments(EntityManagerInterface $entityManager, string $trickSlug, int $page): Response
    {
        // Ajouter un log pour vérifier si la méthode est appelée
        error_log('Loading more comments for trick: ' . $trickSlug . ' on page: ' . $page);

        $limit = 10; // Nombre de commentaires à charger par page
        $comments = $entityManager->getRepository(Comments::class)->findCommentsByTrickSlug($trickSlug, $page, $limit);

        // Créer le formulaire de commentaire
        $comment = new Comments();
        $commentForm = $this->createForm(CommentType::class, $comment);

        // Renvoyer les commentaires et le formulaire en HTML
        return $this->render('trick/comments.html.twig', [
            'comments' => $comments,
            'commentForm' => $commentForm->createView(),
        ]);
    }
}
