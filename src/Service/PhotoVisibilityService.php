<?php 

namespace App\Service;

use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Photo;

class PhotoVisibilityService
{

  private $photoRepository;
  private $entityManager;
  private $security;

  public function __construct(PhotoRepository $photoRepository, EntityManagerInterface $entityManager, Security $security)
  {
    $this->photoRepository = $photoRepository;
    $this->entityManager = $entityManager;
    $this->security = $security;
  }
  
  public function makeVisible(int $id, bool $visibility){
    $em = $this->entityManager;
    $photo = $this->photoRepository->find($id);

    if($this->isPhotoBelongToCurrentUser($photo)){
      $photo->setIsPublic($visibility);
      $em->persist($photo);
      $em->flush();
      return true;
    }else{
      return false;
    }
  }

  private function isPhotoBelongToCurrentUser(Photo $photo){
    if($photo->getUser() === $this->security->getUser()){
      return true;
    }
    else{
      return false;
    }
  }

}

?>