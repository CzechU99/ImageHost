<?php 

  namespace App\Command;

  use Symfony\Component\Console\Command\Command;
  use Symfony\Component\Console\Input\InputInterface;
  use Symfony\Component\Console\Output\OutputInterface;
  use Symfony\Component\Console\Input\InputArgument;
  use App\Entity\Photo;
  use Doctrine\Persistence\ManagerRegistry;
  use Symfony\Component\Console\Attribute\AsCommand;

  #[AsCommand(name: 'app:photo-visible-false')]
  class DisablePhotosVisibilityCommand extends Command
  {
    protected static $defaultName = 'app:photo-visible-false';

    private $entityManager;

    public function __construct(ManagerRegistry $doctrine)
    {
      $this->entityManager = $doctrine->getManager();
      parent::__construct();
    }

    protected function configure(){
      $this
        ->setDescription('Ustawienie zdjęć danego użytkownika jako prywatne')
        ->addArgument('user', InputArgument::REQUIRED, 'Id użytkownika wymagane');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
      $em = $this->entityManager;
      $photoRepository = $em->getRepository(Photo::class);
      $photosToSetPrivate = $photoRepository->findBy(['is_public' => 1, 'user' => $input->getArgument('user')]);
      foreach ($photosToSetPrivate as $publicPhoto){
        $publicPhoto->setIsPublic(0);
        $em->persist($publicPhoto);
        $em->flush();
      }
      return Command::SUCCESS;
    }
  }

?>