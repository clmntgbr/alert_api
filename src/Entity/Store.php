<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Api\Controller\GetActiveStore;
use App\Repository\StoreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: StoreRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get',
        'get_active_store' => [
            'method' => 'GET',
            'path' => '/store/active',
            'controller' => GetActiveStore::class,
            'pagination_enabled' => false,
            'deserialize' => false,
            'read' => false,
            'normalization_context' => ['skip_null_values' => false, 'groups' => ['read_store']]
        ],
        'post'
    ],
    itemOperations: [
        'get',
        'delete',
        'put'
    ],
    normalizationContext: [
        'skip_null_values' => false,
        'groups' => ['read_store'],
    ],
    denormalizationContext: [
        'skip_null_values' => false,
        'groups' => ['write_store'],
    ],
)]
class Store
{
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups('read_store')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['read_store', 'write_store'])]
    private string $name;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups(['read_store', 'write_store'])]
    private bool $isActive;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['read_store'])]
    private int $maxItemPerStore;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EAGER', inversedBy: 'stores')]
    private User $user;

    #[ORM\OneToMany(mappedBy: 'store', targetEntity: Item::class, cascade: ['persist', 'remove'])]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf('%s - %s', $this->id, $this->name);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    #[SerializedName('itemsInStore')]
    #[Groups('read_store')]
    public function getCountItems(): int
    {
        return $this->getItems()->count();
    }

    #[SerializedName('itemsExpiredInStore')]
    #[Groups('read_store')]
    public function getCountExpiredItems(): int
    {
        $date = new \DateTime('now');

        $items = $this->getItems()->filter(
            function (Item $item) use ($date) {
                return $item->getExpirationDate()?->format('Y-m-d') < $date->format('Y-m-d');
            }
        );

        return $items->count();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

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
            $item->setStore($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getStore() === $this) {
                $item->setStore(null);
            }
        }

        return $this;
    }

    public function getMaxItemPerStore(): ?int
    {
        return $this->maxItemPerStore;
    }

    public function setMaxItemPerStore(int $maxItemPerStore): self
    {
        $this->maxItemPerStore = $maxItemPerStore;

        return $this;
    }
}