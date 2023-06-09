<?php

namespace App\Subscriber;

use App\Entity\Store;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly Security $security
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
            $user
                ->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()))
                ->eraseCredentials();
        }

        $store = new Store();
        $store
            ->setIsActive(true)
            ->setName('Default Store')
            ->setUser($user);

        $user->getImage()->setName('kdi02939Idjdk102.png');
        $user->getImage()->setDimensions(["251","400"]);
        $user->getImage()->setSize(16000);
        $user->getImage()->setMimeType('image/png');
        $user->getImage()->setOriginalName('kdi02939Idjdk102.png');

        $user
            ->addStore($store)
            ->setIsEnable(true);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        if (!$this->security->getToken()?->getUser() instanceof User) {
            return;
        }

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
