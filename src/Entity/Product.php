<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    collectionOperations: [], // TEMPORARY
    itemOperations: ['get'],
)]
class Product
{
    use TimestampableEntity;
    use BlameableEntity;
    
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['read_item', 'read_items'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, unique: true)]
    private string $ean;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['read_item', 'read_items'])]
    private string $name;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['read_item', 'read_items'])]
    private string $brand;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['read_item', 'read_items'])]
    private ?string $country;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $categories;
    
    #[ORM\ManyToOne(targetEntity: Nutrition::class, cascade: ['persist', 'remove'], fetch: 'LAZY')]
    private Nutrition $nutrition;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEan(): ?string
    {
        return $this->ean;
    }

    public function setEan(string $ean): self
    {
        $this->ean = $ean;

        return $this;
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

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCategories(): ?string
    {
        return $this->categories;
    }

    public function setCategories(?string $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getNutrition(): ?Nutrition
    {
        return $this->nutrition;
    }

    public function setNutrition(?Nutrition $nutrition): self
    {
        $this->nutrition = $nutrition;

        return $this;
    }
}