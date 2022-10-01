<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Api\Controller\GetItemsExpired;
use App\Api\Controller\GetItemsExpireSoon;
use App\Api\Controller\GetItemsLiked;
use App\Repository\ItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => ['normalization_context' => ['skip_null_values' => false, 'groups' => ['read_items']]],
        'post' => ['denormalization_context' => ['groups' => ['post_item']]],
        'get_items_by_expiration_date_soon' => [
            'method' => 'GET',
            'path' => '/items/expire_soon',
            'controller' => GetItemsExpireSoon::class,
            'pagination_enabled' => false,
            'deserialize' => false,
            'read' => false,
            'normalization_context' => ['skip_null_values' => false, 'groups' => ['read_items']],
        ],
        'get_items_by_expiration_date' => [
            'method' => 'GET',
            'path' => '/items/expired',
            'controller' => GetItemsExpired::class,
            'pagination_enabled' => false,
            'deserialize' => false,
            'read' => false,
            'normalization_context' => ['skip_null_values' => false, 'groups' => ['read_items']],
        ],
        'get_items_by_like' => [
            'method' => 'GET',
            'path' => '/items/liked',
            'controller' => GetItemsLiked::class,
            'pagination_enabled' => false,
            'deserialize' => false,
            'read' => false,
            'normalization_context' => ['skip_null_values' => false, 'groups' => ['read_items']],
        ],
    ],
    itemOperations: [
        'get' => ['normalization_context' => ['skip_null_values' => false, 'groups' => ['read_item']]],
        'delete',
        'put' => ['normalization_context' => ['skip_null_values' => false, 'groups' => ['put_item']]],
    ],
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'id' => 'exact', 'store.id' => 'exact', ]
)]
#[ApiFilter(
    BooleanFilter::class,
    properties: [
        'store.isActive',
    ]
)]
class Item
{
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['read_item', 'read_items'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['read_item', 'read_items', 'post_item'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'd/m/Y'])]
    private ?\DateTimeInterface $expirationDate;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups(['read_item', 'read_items', 'put_item'])]
    private bool $isLiked;

    #[ORM\ManyToOne(targetEntity: Product::class, fetch: 'EXTRA_LAZY')]
    #[Groups(['read_item', 'read_items', 'post_item'])]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: Store::class, fetch: 'EAGER', inversedBy: 'items')]
    #[Groups(['read_items'])]
    private Store $store;

    public function __construct()
    {
        $this->isLiked = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(Store $store): self
    {
        $this->store = $store;

        return $this;
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
}
