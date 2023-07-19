<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserNotificationTimerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserNotificationTimerRepository::class)]
#[ApiResource]
class UserNotificationTimer
{
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['read_user'])]
    private string $valueBeforeNotificationInHours = 'P0DT24H';

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EAGER', inversedBy: 'userNotificationTimers')]
    private User $user;

    public function __toString()
    {
        return (string) $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getValueBeforeNotificationInHours(): ?string
    {
        return $this->valueBeforeNotificationInHours;
    }

    public function setValueBeforeNotificationInHours(string $valueBeforeNotificationInHours): self
    {
        $this->valueBeforeNotificationInHours = $valueBeforeNotificationInHours;

        return $this;
    }
}
