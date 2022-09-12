<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use App\Api\Controller\GetItemsExpireSoonLimited;
use App\Api\Controller\GetItemsExpiredLimited;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => ['normalization_context' => ['skip_null_values' => false, 'groups' => ['read_items']]],
        'post' => ['denormalization_context' => ['groups' => ['post_item']]],
        'get_items_by_expiration_date_soon_limited' => [
            'method' => 'GET',
            'path' => '/items/expire_soon/limited',
            'controller' => GetItemsExpireSoonLimited::class,
            'pagination_enabled' => false,
            'deserialize' => false,
            'read' => false,
            'normalization_context' => ['skip_null_values' => false, 'groups' => ['read_items']]
        ],
        'get_items_by_expiration_date_limited' => [
            'method' => 'GET',
            'path' => '/items/expired/limited',
            'controller' => GetItemsExpiredLimited::class,
            'pagination_enabled' => false,
            'deserialize' => false,
            'read' => false,
            'normalization_context' => ['skip_null_values' => false, 'groups' => ['read_items']]
        ]
    ],
    itemOperations: [
        'get' => ['normalization_context' => ['skip_null_values' => false, 'groups' => ['read_item']]],
        'delete',
        'put'
    ],
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
    'id' => 'exact', 'store.id' => 'exact']
)]
#[ApiFilter(
    BooleanFilter::class,
    properties: [
        'store.isActive'
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

    #[ORM\ManyToOne(targetEntity: Product::class, fetch: 'EXTRA_LAZY')]
    #[Groups(['read_item', 'read_items', 'post_item'])]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: Store::class, fetch: 'EAGER', inversedBy: 'items')]
    private Store $store;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }
}