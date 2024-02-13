<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UploadPhotoType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Photo;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\File;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {

        $form = $this->createForm(UploadPhotoType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $em = $doctrine->getManager();

            if($this->getUser()){
                $pictureFileName = $form->get('filename')->getData();
                if($pictureFileName){
                    try{
                        $originalFilename = pathinfo($pictureFileName->getClientOriginalName(), PATHINFO_FILENAME);
                        $safeFileName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                        $newFileName = $safeFileName.'-'.uniqid().'.'.$pictureFileName->guessExtension();
                        $pictureFileName->move('images/hosting', $newFileName);
    
                        $entityPhotos = new Photo();
                        $entityPhotos->setFilename($newFileName);
                        $entityPhotos->setIsPublic($form->get('is_public')->getData());
                        $entityPhotos->setUploadedAt(new \DateTime());
                        $entityPhotos->setUser($this->getUser());
    
                        $this->addFlash('success', 'Dodano zdjęcie!');

                        $em->persist($entityPhotos);
                        $em->flush();
                    }catch(\Exception $e){
                        $this->addFlash('error', 'Nie udało się dodać zdjęcia!');
                    }
                }
            }
        }

        return $this->render('index/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
