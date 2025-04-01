<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Entity\User;
use App\Entity\Post;
use App\Repository\UserRepository;
use App\Repository\ImageRepository;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/image')]
final class ImageController extends AbstractController
{

    // version using filename
    #[Route('/' ,name: 'app_image_index', methods: ['GET'])]
    public function index(ImageRepository $imageRepository, LikeRepository $likeRepository): Response
    {
        // $imagesDirectory = $this->getParameter('images_directory'); 

        $images = $imageRepository->findBy([], ['created_at' => 'DESC']);

        $likeCount = 0;

        foreach ($images as $image) {
            $post = $image->getPost(); 
            if ($post) {
                // On utilise la même méthode que pour compter les likes d’un post
                $likeCount = $likeRepository->totalLike($post->getId());
            }
        }

        return $this->render('image/index.html.twig', [
            'images' => $images,
            'likeCount' => $likeCount,
        ]);
    }





    #[Route('/new', name: 'app_image_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // $user = $this->getUser();
        $image = new Image();
        

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);


        $titlePost = $request->query->get('title');

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('path')->getData();

            if ($file instanceof UploadedFile) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); 
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                // Déplacer le fichier vers le répertoire des images
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'exception, par exemple en enregistrant l'erreur ou en informant l'utilisateur
                    $this->addFlash('error', 'An error occurred while uploading the file.');
                    return $this->render('image/new.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }

                // Enregistrer le chemin partiel dans la base de données
                $image->setPath('uploads/images/' . $newFilename);
                $image->setTitle($form->get('title')->getData());
                $image->setCreatedAt(new \DateTimeImmutable()); 


                $user = $this->getUser();
                if ($user) {
                    $image->setIdUser($user);
                }

                $entityManager->persist($image);
                $entityManager->flush();

                // return $this->redirectToRoute('app_image_index', [], Response::HTTP_SEE_OTHER);
                // Rediriger vers la création de post avec l'ID de l'image
                return $this->redirectToRoute('app_post_new', ['imageId' => $image->getId(), 'title' => $titlePost,], Response::HTTP_SEE_OTHER);
            } else {
                $this->addFlash('error', 'Invalid file upload.');
            }
        }

        return $this->render('image/new.html.twig', [
            
            'form' => $form->createView(),
        ]);
    }




    #[Route('/{id}', name: 'app_image_show', methods: ['GET'])]
    public function show(Image $image, LikeRepository $likeRepository): Response
    {
        
        $post = $image->getPost();
        $likeCount = 0;
        if ($post) {
            // On récupère le nombre de likes du Post
            $likeCount = $likeRepository->totalLike($post->getId());
        }
        return $this->render('image/show.html.twig', [
            'image' => $image,
            'likeCount' => $likeCount,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_image_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Image $image, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_image_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('image/edit.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/{id}/delete', name: 'app_image_delete', methods: ['POST'])]
    public function delete(Request $request, Image $image, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->getPayload()->getString('_token'))) {

            // $imagePath = $this->getParameter('images_directory') . '/' . ltrim($image->getPath(), '/');
            $imagePath = $this->getParameter('kernel.project_dir') . '/public/' . ltrim($image->getPath(), '/');
            
            dump("Chemin du fichier: ", $imagePath);


            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_image_index', [], Response::HTTP_SEE_OTHER);
    }
}
