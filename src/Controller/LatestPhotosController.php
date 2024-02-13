<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Photo;

class LatestPhotosController extends AbstractController
{
    #[Route('/latest', name: 'latest_photos')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $latestPhotosPublic = $em->getRepository(Photo::class)->findBy(['is_public' => true]);

        return $this->render('latest_photos/index.html.twig', [
            'photos' => $latestPhotosPublic,
        ]);
    }
}

?>