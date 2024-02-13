<?php 

  namespace App\Event;

  use Symfony\Contracts\EventDispatcher\Event;

  class LoginErrorEvent extends Event
  {
    
    const NAME = 'login.error';

    private $user;

    public function __construct($user)
    {
      $this->user = $user;
    }

    public function getUser()
    {
      return $this->user;
    }

  }

?>