<?php

namespace App\Subscriber;

use App\Entity\Store;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class StoreSubscriber implements EventSubscriber
{
    public function __construct(
        private readonly int $maxItemPerStore
    )
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $store = $args->getObject();

        if (!$store instanceof Store) {
            return;
        }

        $store->setMaxItemPerStore($this->maxItemPerStore);
    }
}
