<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[\ApiPlatform\Core\Annotation\ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get', 'patch' => ['denormalization_context' => ['groups' => ['patch_notification']]]],
    normalizationContext: ['groups' => ['get_notification', 'get_items']],
)]
class Notification
{
    use TimestampableEntity;

    public const UNREAD = 'UNREAD';
    public const READ = 'READ';
    public const PENDING = 'PENDING';
    public const EXPIRED = 'EXPIRED';
    public const SENT = 'SENT';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[Groups(['get_notification'])]
    private User $user;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $statuses;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['patch_notification', 'get_notification'])]
    private string $type = 'item';

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['patch_notification', 'get_notification'])]
    private string $timer = 'P0DT24H';

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['patch_notification', 'get_notification'])]
    private ?string $status;

    #[ORM\ManyToMany(targetEntity: Item::class, inversedBy: 'notifications')]
    #[Groups(['patch_notification', 'get_notification'])]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        $this->setStatuses($status);

        return $this;
    }

    public function getStatuses(): ?array
    {
        return $this->statuses;
    }

    public function setStatuses(string $status): self
    {
        $this->statuses[] = $status;

        return $this;
    }

    public function setInitStatuses(array $status): self
    {
        $this->statuses = $status;

        return $this;
    }

    public function getPreviousStatus(): ?string
    {
        if (count($this->statuses) <= 1) {
            return end($this->statuses);
        }

        return $this->statuses[count($this->statuses) - 2];
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        $this->items->removeElement($item);

        return $this;
    }

    public function getTimer(): ?string
    {
        return $this->timer;
    }

    public function setTimer(string $timer): self
    {
        $this->timer = $timer;

        return $this;
    }
}
