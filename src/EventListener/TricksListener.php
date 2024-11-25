<?php

namespace App\EventListener;

use App\Entity\Tricks;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Tricks::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Tricks::class)]

class TricksListener
{

    public function prePersist(Tricks $trick, PrePersistEventArgs $event): void
    {
        $slugger = new AsciiSlugger();
        $trick->setSlug($slugger->slug($trick->getName()));
        
        // Si createdAt est null, créer la date avec sa mise à jour
        if ($trick->getCreatedAt() === null) {
            $trick->setCreatedAt(new \DateTimeImmutable());
        }
        $trick->setUpdatedAt(new \DateTimeImmutable());
    }

    public function preUpdate(Tricks $trick, PreUpdateEventArgs $event): void
    {
        $slugger = new AsciiSlugger();
        if ($event->hasChangedField('name')) {
            $trick->setSlug($slugger->slug($trick->getName()));
        }
        $trick->setUpdatedAt(new \DateTimeImmutable());
    }
}