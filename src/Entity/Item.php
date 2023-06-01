<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\ItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'patch' => ['denormalization_context' => ['groups' => ['patch_item']]], 'delete'],
    normalizationContext: ['groups' => ['get_items']],
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
    #[Groups(['get_items', 'patch_item'])]
    private ?\DateTimeInterface $expirationDate;

    #[ORM\Column(type: Types::BOOLEAN)]
    #[Groups(['get_items', 'patch_item'])]
    private bool $isLiked;

    #[ORM\ManyToOne(targetEntity: Product::class, fetch: 'EXTRA_LAZY')]
    #[Groups(['get_items'])]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: Store::class, fetch: 'EAGER', inversedBy: 'items')]
    #[Groups(['get_items'])]
    private Store $store;

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
}
