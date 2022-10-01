<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Api\Controller\GetProductByEan;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Safe\DateTime;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

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
            'normalization_context' => ['skip_null_values' => false, 'groups' => ['read_product']],
        ],
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

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['read_item', 'read_items', 'read_product'])]
    private string $name;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['read_item', 'read_items', 'read_product'])]
    private string $brand;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['read_item', 'read_items', 'read_product'])]
    private ?string $categories;

    #[ORM\ManyToOne(targetEntity: Nutrition::class, cascade: ['persist', 'remove'], fetch: 'LAZY')]
    #[Groups(['read_item'])]
    private Nutrition $nutrition;

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

    #[ORM\ManyToOne(targetEntity: ProductStatus::class, cascade: ['persist']), ORM\JoinColumn(nullable: true)]
    private ProductStatus $productStatus;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductStatusHistory::class, cascade: ['remove'])]
    private Collection $productStatusHistories;

    public function __construct()
    {
        $this->image = new EmbeddedFile();
        $this->imageIngredients = new EmbeddedFile();
        $this->imageNutrition = new EmbeddedFile();
        $this->productStatusHistories = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf('%s - %s', $this->id, $this->name);
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

    public function setNutrition(Nutrition $nutrition): self
    {
        $this->nutrition = $nutrition;

        return $this;
    }

    #[Groups(['read_item', 'read_items', 'read_product'])]
    public function getImagePath(): string
    {
        return sprintf('/images/products/%s', $this->getImage()->getName());
    }

    #[Groups(['read_item'])]
    public function getImageIngredientsPath(): string
    {
        return sprintf('/images/products/%s', $this->getImageIngredients()->getName());
    }

    #[Groups(['read_item'])]
    public function getImageNutritionPath(): string
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

    public function getImageIngredientsFile(): ?File
    {
        return $this->imageIngredientsFile;
    }

    public function setImageIngredientsFile(?File $imageFile = null): self
    {
        $this->imageIngredientsFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new DateTime();
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
            $this->updatedAt = new DateTime();
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
            $this->updatedAt = new DateTime();
        }

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

    public function getCategories(): ?string
    {
        return $this->categories;
    }

    public function setCategories(?string $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function getProductStatus(): ?ProductStatus
    {
        return $this->productStatus;
    }

    public function setProductStatus(ProductStatus $productStatus): self
    {
        $this->productStatus = $productStatus;

        return $this;
    }

    public function addProductStatusHistory(ProductStatusHistory $productStatusHistory): self
    {
        if (!$this->productStatusHistories->contains($productStatusHistory)) {
            $this->productStatusHistories->add($productStatusHistory);
            $productStatusHistory->setProduct($this);
        }

        return $this;
    }

    public function removeProductStatusHistory(ProductStatusHistory $productStatusHistory): self
    {
        $this->productStatusHistories->removeElement($productStatusHistory);

        return $this;
    }

    /**
     * @return Collection<int, ProductStatusHistory>
     */
    public function getProductStatusHistories(): Collection
    {
        return $this->productStatusHistories;
    }
}
