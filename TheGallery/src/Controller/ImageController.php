<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/image')]
final class ImageController extends AbstractController
{
    #[Route(name: 'app_image_index', methods: ['GET'])]
    public function index(ImageRepository $imageRepository): Response
    {
        $imagesDirectory = $this->getParameter('images_directory');  

        $images = scandir($imagesDirectory);

        // Filtrer les fichiers pour n'afficher que les images
        $imageFiles = array_filter($images, function($file) {
            return in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']);
        });

        $imageFiles = array_values($imageFiles);

        return $this->render('image/index.html.twig', [
            'imageFiles' => $imageFiles,
            'images' => $imageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_image_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // $user = $this->getUser();
        $image = new Image();
        $image->setIdUser($this->getUser());

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();

            if ($file) {
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
                $image->setPath('*/uploads/images/' . $newFilename);

                $user = $this->getUser();
                if ($user) {
                    $image->setIdUser($user);
                }

                $entityManager->persist($image);
                $entityManager->flush();

                return $this->redirectToRoute('app_image_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('image/new.html.twig', [
            
            'form' => $form->createView(),
        ]);
    }
    // public function new(Request $request, EntityManagerInterface $entityManager): Response
    // {
    //     $user = $this->getUser();
    //     $image = new Image();
    //     $form = $this->createForm(ImageType::class, $image);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->persist($image);
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_image_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('image/new.html.twig', [
    //         'image' => $image,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}', name: 'app_image_show', methods: ['GET'])]
    public function show(Image $image): Response
    {
        return $this->render('image/show.html.twig', [
            'image' => $image,
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
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_image_delete', methods: ['POST'])]
    public function delete(Request $request, Image $image, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->getPayload()->getString('_token'))) {

            $imagePath = $this->getParameter('kernel.project_dir') . '/public' . $image->getPath();
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_image_index', [], Response::HTTP_SEE_OTHER);
    }
}
