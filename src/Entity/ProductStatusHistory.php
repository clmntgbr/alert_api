<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProductStatusHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ProductStatusHistoryRepository::class)]
#[ApiResource(
    collectionOperations: ['get'],
    itemOperations: ['get']
)]
class ProductStatusHistory
{
    use TimestampableEntity;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: Types::INTEGER)]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: Product::class, cascade: ['persist'], inversedBy: 'productStatusHistories'), ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: ProductStatus::class, cascade: ['persist']), ORM\JoinColumn(nullable: false)]
    private ProductStatus $productStatus;

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

    public function getProductStatus(): ?ProductStatus
    {
        return $this->productStatus;
    }

    public function setProductStatus(?ProductStatus $productStatus): self
    {
        $this->productStatus = $productStatus;

        return $this;
    }
}
