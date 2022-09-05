<?php

namespace App\Subscriber;

use App\Entity\Store;
use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserSubscriber implements EventSubscriber
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $user = $args->getObject();

        if (!$user instanceof User) {
            return;
        }

        if (null !== $user->getPlainPassword()) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()));
        }

        $user
            ->setIsEnable(true)
            ->eraseCredentials()
        ;

        if ($user->getStores()->count() == 0) {
            $store = new Store();
            $store
                ->setIsActive(true)
                ->setName('Default')
                ->setUser($user);
            $user->addStore($store);
        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $user = $args->getObject();

        if (!$user instanceof User) {
            return;
        }

        if (null !== $user->getPlainPassword()) {
            $user
                ->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()))
                ->eraseCredentials();
        }
    }
}