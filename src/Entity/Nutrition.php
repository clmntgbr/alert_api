<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\NutritionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Blameable\Traits\BlameableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NutritionRepository::class)]
#[ApiResource(
    collectionOperations: [],
    itemOperations: ['get'],
)]
class Nutrition
{
    use TimestampableEntity;
    use BlameableEntity;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['read_item'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['read_item'])]
    private ?string $ecoscoreGrade;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['read_item'])]
    private ?string $ecoscoreScore;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['read_item'])]
    private ?string $ingredientsText;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['read_item'])]
    private ?string $nutriscoreGrade;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['read_item'])]
    private ?string $nutriscoreScore;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['read_item'])]
    private ?string $quantity;

    public function __toString()
    {
        return (string) $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEcoscoreGrade(): ?string
    {
        return $this->ecoscoreGrade;
    }

    public function setEcoscoreGrade(?string $ecoscoreGrade): self
    {
        $this->ecoscoreGrade = $ecoscoreGrade;

        return $this;
    }

    public function getEcoscoreScore(): ?string
    {
        return $this->ecoscoreScore;
    }

    public function setEcoscoreScore(?string $ecoscoreScore): self
    {
        $this->ecoscoreScore = $ecoscoreScore;

        return $this;
    }

    public function getIngredientsText(): ?string
    {
        return $this->ingredientsText;
    }

    public function setIngredientsText(?string $ingredientsText): self
    {
        $this->ingredientsText = $ingredientsText;

        return $this;
    }

    public function getNutriscoreGrade(): ?string
    {
        return $this->nutriscoreGrade;
    }

    public function setNutriscoreGrade(?string $nutriscoreGrade): self
    {
        $this->nutriscoreGrade = $nutriscoreGrade;

        return $this;
    }

    public function getNutriscoreScore(): ?string
    {
        return $this->nutriscoreScore;
    }

    public function setNutriscoreScore(?string $nutriscoreScore): self
    {
        $this->nutriscoreScore = $nutriscoreScore;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(?string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
