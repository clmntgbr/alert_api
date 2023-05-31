<?php

namespace App\Entity;


use ApiPlatform\Core\Annotation\ApiResource;
use App\ApiResource\PostProductByEan;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'get' => ['normalization_context' => ['skip_null_values' => false, 'groups' => ['get_product']]],
        'post_products' => [
            'method' => 'POST',
            'path' => '/products',
            'controller' => PostProductByEan::class,
            'read' => false,
            'normalization_context' => ['skip_null_values' => false, 'groups' => ['get_product']],
        ],
    ],
)]
#[Vich\Uploadable]
class Product
{
    public const WAITING_VALIDATION = 'waiting_validation';
    public const VALIDATED = 'validated';
    public const NOT_FOUND = 'not_found';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['get_items', 'get_product'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, unique: true)]
    #[Groups(['get_product'])]
    private string $ean;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_items', 'get_product'])]
    private string $name;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_items', 'get_product'])]
    private string $brand;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_items', 'get_product'])]
    private ?string $manufacturingPlace;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_items', 'get_product'])]
    private ?string $link;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_items', 'get_product'])]
    private ?string $origin;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_items', 'get_product'])]
    private ?string $categories;

    #[ORM\ManyToOne(targetEntity: ProductNutrition::class, cascade: ['persist', 'remove'], fetch: 'LAZY')]
    #[Groups(['get_product'])]
    private ProductNutrition $productNutrition;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $statuses;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['get_product'])]
    private ?string $status;

    #[Vich\UploadableField(mapping: 'product_image', fileNameProperty: 'image.name', size: 'image.size', mimeType: 'image.mimeType', originalName: 'image.originalName', dimensions: 'image.dimensions')]
    private ?File $imageFile = null;

    #[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
    private EmbeddedFile $image;

    #[Vich\UploadableField(mapping: 'product_image', fileNameProperty: 'imageIngredients.name', size: 'imageIngredients.size', mimeType: 'imageIngredients.mimeType', originalName: 'imageIngredients.originalName', dimensions: 'imageIngredients.dimensions')]
    private ?File $imageIngredientsFile = null;

    #[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
    private EmbeddedFile $imageIngredients;

    #[Vich\UploadableField(mapping: 'product_image', fileNameProperty: 'imageNutrition.name', size: 'imageNutrition.size', mimeType: 'imageNutrition.mimeType', originalName: 'imageNutrition.originalName', dimensions: 'imageNutrition.dimensions')]
    private ?File $imageNutritionFile = null;

    #[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
    private EmbeddedFile $imageNutrition;

    public function __construct()
    {
        $this->image = new EmbeddedFile();
        $this->imageIngredients = new EmbeddedFile();
        $this->imageNutrition = new EmbeddedFile();
    }

    #[Groups(['get_items', 'get_product'])]
    public function getImagePath(): string
    {
        return sprintf('/images/products/%s', $this->getImage()->getName());
    }

    #[Groups(['get_product'])]
    public function getImageIngredientsPath(): string
    {
        return sprintf('/images/products/%s', $this->getImageIngredients()->getName());
    }

    #[Groups(['get_product'])]
    public function getImageNutritionPath(): string
    {
        return sprintf('/images/products/%s', $this->getImageNutrition()->getName());
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

    public function getImage(): EmbeddedFile
    {
        return $this->image;
    }

    public function setImage(EmbeddedFile $image): self
    {
        $this->image = $image;

        return $this;
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
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

    public function getImageIngredientsFile(): ?File
    {
        return $this->imageIngredientsFile;
    }

    public function setImageIngredientsFile(?File $imageFile = null): self
    {
        $this->imageIngredientsFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTime();
        }

        return $this;
    }

    public function getImageNutritionFile(): ?File
    {
        return $this->imageNutritionFile;
    }

    public function setImageNutritionFile(?File $imageFile = null): self
    {
        $this->imageNutritionFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTime();
        }

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTime();
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

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

    public function getProductNutrition(): ?ProductNutrition

    {
        return $this->productNutrition;
    }

    public function setProductNutrition(?ProductNutrition $nutrition): self
    {
        $this->productNutrition = $nutrition;

        return $this;
    }

    public function getManufacturingPlace(): ?string
    {
        return $this->manufacturingPlace;
    }

    public function setManufacturingPlace(?string $manufacturingPlace): self
    {
        $this->manufacturingPlace = $manufacturingPlace;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): self
    {
        $this->origin = $origin;

        return $this;
    }
}