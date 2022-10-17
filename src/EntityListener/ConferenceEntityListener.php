<?php

namespace App\EntityListener;

use App\Entity\Conference;

use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ConferenceEntityListener
{
      private $slugger;
      public function __construct(SluggerInterface $slugger)
      {
            $this->slugger = $slugger;
      }
      public function prePersist(Conference $conf, LifecycleEventArgs $event)
      {
            $conf->computSlug($this->slugger);
      }
      public function preUpdate(Conference $conf, LifecycleEventArgs $event)
      {
            $conf->computSlug($this->slugger);
      }
}