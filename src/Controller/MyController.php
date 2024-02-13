<?php 

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Photo;
use Symfony\Component\Filesystem\Filesystem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Service\PhotoVisibilityService;

class MyController extends AbstractController
{

  #[Route('/my/photos', name: 'my_photos')]
  public function index(ManagerRegistry $doctrine)
  {
    $em = $doctrine->getManager();
    $myPhotos = $em->getRepository(Photo::class)->findBy(['user' => $this->getUser()]);

    return $this->render('my/index.html.twig', [
      'myPhotos' => $myPhotos
    ]);
  }

  #[Route('/my/photos/set_visibility/{id}/{visibility}', name: 'my_photos_set_visibility')]
  public function myPhotoChangeVisibility(PhotoVisibilityService $photoVisibilityService, int $id, bool $visibility)
  {

    $messages = [
      '1' => 'publiczne',
      '0' => 'prywatne'
    ];

    if($photoVisibilityService->makeVisible($id, $visibility)){
      $this->addFlash('success', 'Ustawiono jako ' . $messages[$visibility] . '!');
    }else{
      $this->addFlash('error', 'Wystąpił problem przy uswianiu jako ' . $messages[$visibility] . '!');
    }

    return $this->redirectToRoute('my_photos');
  }

  #[Route('/my/photos/delete/{id}', name: 'my_photos_delete')]
  //#[IsGranted('ROLE_USER')]
  public function myPhotosDelete(int $id, ManagerRegistry $doctrine)
  {
    $em = $doctrine->getManager();
    $myPhoto = $em->getRepository(Photo::class)->find($id);

    $this->denyAccessUnlessGranted('ROLE_USER');
    if($this->getUser() == $myPhoto->getUser()){
      $file = new Filesystem();
      $file->remove('images/hosting/' . $myPhoto->getFilename());
      if($file->exists('images/hosting/' . $myPhoto->getFilename())){
        $this->addFlash('error', 'Błąd podczas usuwania zdjęcia!');
      }
      else{
        $em->remove($myPhoto);
        $em->flush();
        $this->addFlash('success', 'Usunięto zdjęcie!');      
      }
    }
    else{
      $this->addFlash('error', 'Nie masz uprawnień do tego zdjęcia!');
    }

    return $this->redirectToRoute('my_photos');
  }
}

?>