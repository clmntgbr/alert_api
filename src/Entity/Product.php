<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Symfony\Component\HttpFoundation\File\File;
use App\Api\Controller\GetProductByEan;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'get' => ['normalization_context' => ['skip_null_values' => false, 'groups' => ['read_product']]],
        'get_product_by_ean' => [
            'method' => 'GET',
            'path' => '/product/{ean}/ean',
            'controller' => GetProductByEan::class,
            'pagination_enabled' => false,
            'deserialize' => false,
            'read' => false,
            'normalization_context' => ['skip_null_values' => false, 'groups' => ['read_product']]
        ]
    ],
)]
#[Vich\Uploadable]
class Product
{
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['read_item', 'read_items', 'read_product'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Groups(['read_item', 'read_product'])]
    private string $ean;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['read_item', 'read_items', 'read_product'])]
    private string $name;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['read_item', 'read_items', 'read_product'])]
    private string $brand;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['read_item', 'read_items', 'read_product'])]
    private ?array $categories;

    #[ORM\ManyToOne(targetEntity: Nutrition::class, cascade: ['persist', 'remove'], fetch: 'LAZY')]
    #[Groups(['read_item'])]
    private Nutrition $nutrition;

    #[Vich\UploadableField(mapping: 'product_image', fileNameProperty:'image.name', size:'image.size', mimeType:'image.mimeType', originalName:'image.originalName', dimensions:'image.dimensions')]
    private ?File $imageFile = null;

    #[ORM\Embedded(class:'Vich\UploaderBundle\Entity\File')]
    private EmbeddedFile $image;

    #[Vich\UploadableField(mapping: 'product_image', fileNameProperty:'imageIngredients.name', size:'imageIngredients.size', mimeType:'imageIngredients.mimeType', originalName:'imageIngredients.originalName', dimensions:'imageIngredients.dimensions')]
    private ?File $imageIngredientsFile = null;

    #[ORM\Embedded(class:'Vich\UploaderBundle\Entity\File')]
    private EmbeddedFile $imageIngredients;

    #[Vich\UploadableField(mapping: 'product_image', fileNameProperty:'imageNutrition.name', size:'imageNutrition.size', mimeType:'imageNutrition.mimeType', originalName:'imageNutrition.originalName', dimensions:'imageNutrition.dimensions')]
    private ?File $imageNutritionFile = null;

    #[ORM\Embedded(class:'Vich\UploaderBundle\Entity\File')]
    private EmbeddedFile $imageNutrition;

    public function __construct()
    {
        $this->categories = [];
        $this->image = new \Vich\UploaderBundle\Entity\File();
        $this->imageIngredients = new \Vich\UploaderBundle\Entity\File();
        $this->imageNutrition = new \Vich\UploaderBundle\Entity\File();
    }

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

    public function getNutrition(): ?Nutrition
    {
        return $this->nutrition;
    }

    public function setNutrition(?Nutrition $nutrition): self
    {
        $this->nutrition = $nutrition;

        return $this;
    }

    #[Groups(['read_item', 'read_items', 'read_product'])]
    public function getImagePath()
    {
        return sprintf('/images/products/%s', $this->getImage()->getName());
    }

    #[Groups(['read_item'])]
    public function getImageIngredientsPath()
    {
        return sprintf('/images/products/%s', $this->getImageIngredients()->getName());
    }

    #[Groups(['read_item'])]
    public function getImageNutritionPath()
    {
        return sprintf('/images/products/%s', $this->getImageNutrition()->getName());
    }

    public function getImage(): EmbeddedFile
    {
        return $this->image;
    }

    public function setImage(EmbeddedFile $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function setImageFile(?File $imageFile = null)
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function setImageIngredientsFile(?File $imageFile = null)
    {
        $this->imageIngredientsFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageIngredientsFile(): ?File
    {
        return $this->imageIngredientsFile;
    }

    public function setImageNutritionFile(?File $imageFile = null)
    {
        $this->imageNutritionFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageNutritionFile(): ?File
    {
        return $this->imageNutritionFile;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImageIngredients(): EmbeddedFile
    {
        return $this->imageIngredients;
    }

    public function setImageIngredients(EmbeddedFile $imageIngredients): self
    {
        $this->imageIngredients = $imageIngredients;

        return $this;
    }

    public function getImageNutrition(): EmbeddedFile
    {
        return $this->imageNutrition;
    }

    public function setImageNutrition(EmbeddedFile $imageNutrition): self
    {
        $this->imageNutrition = $imageNutrition;

        return $this;
    }

    public function getCategories(): ?array
    {
        return $this->categories;
    }

    public function setCategories(?array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }
}