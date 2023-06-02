<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ApiResource(
    collectionOperations: ['get', 'post' => ['denormalization_context' => ['groups' => ['post_item']]]],
    itemOperations: ['get', 'patch' => ['denormalization_context' => ['groups' => ['patch_item']]], 'delete'],
    normalizationContext: ['groups' => ['get_items', 'get_product']],
    order: ['expirationDate' => 'ASC'],
)]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'product.name' => 'partial',])]
#[ApiFilter(OrderFilter::class, properties: ['expirationDate',])]
#[ApiFilter(BooleanFilter::class, properties: ['isLiked',])]
class Item
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['get_items'])]
    private int $id;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y'])]
    #[Groups(['get_items', 'patch_item', 'post_item'])]
    private ?\DateTimeInterface $expirationDate = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups(['get_items', 'patch_item', 'post_item'])]
    private bool $isLiked = false;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['get_items', 'patch_item', 'post_item'])]
    private int $quantity = 1;

    #[ORM\ManyToOne(targetEntity: Product::class, fetch: 'EXTRA_LAZY')]
    #[Groups(['get_items', 'post_item'])]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: Store::class, fetch: 'EAGER', inversedBy: 'items')]
    #[Groups(['get_items'])]
    private Store $store;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $statuses;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_items', 'patch_item'])]
    private ?string $status;

    #[ORM\ManyToMany(targetEntity: Notification::class, mappedBy: 'items')]
    private Collection $notifications;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function isIsLiked(): ?bool
    {
        return $this->isLiked;
    }

    public function setIsLiked(bool $isLiked): self
    {
        $this->isLiked = $isLiked;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(?Store $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
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

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->addItem($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            $notification->removeItem($this);
        }

        return $this;
    }
}
