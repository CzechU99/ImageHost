<?php 

namespace App\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;

class EventErrorSubscriber implements EventSubscriberInterface
{

  private $logger;

  public function __construct(LoggerInterface $logger)
  {
    $this->logger = $logger;
  }

  public static function getSubscribedEvents()
  {
    return [
      LoginErrorEvent::NAME => 'onLoginError',
    ];
  }

  public function onLoginError(LoginErrorEvent $event)
  {
    $this->logger->info('Nieudana próba logowania użytkownika: ' . $event->getUser()->getUsername());
  }
}

?>