<?php

namespace App\Service;

use App\Entity\Nutrition;
use App\Entity\Product;
use Safe;
use App\Helper\ProductStatusHelper;
use App\Lists\ProductStatusReference;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OpenFoodFactApiService
{
    private array $response = [];

    public function __construct(
        private GetImage               $getImage,
        private EntityManagerInterface $em,
        private ProductStatusHelper    $productStatusHelper
    )
    {
    }

    public function find(string $productEan): self
    {
        $url = 'https://fr.openfoodfacts.org/api/v0/produit/' . $productEan . '.json';

        $response = file_get_contents($url);
        $response = Safe\json_decode($response, true);

        if (isset($response['status_verbose']) && $response['status_verbose'] === 'product not found') {
            $this->createUnfoundProduct($productEan);
            throw new HttpException(404, 'product not found.');
        }

        $this->response = $response;

        return $this;
    }

    public function createUnfoundProduct(string $productEan): void
    {
        $product = new Product();
        $product
            ->setEan($productEan)
            ->setNutrition(new Nutrition());

        $this->em->persist($product);
        $this->em->flush();

        $this->productStatusHelper->setStatus(
            ProductStatusReference::NOT_FOUND,
            $product
        );
    }

    public function createProduct(): Product
    {
        $nutrition = new Nutrition();

        $nutrition
            ->setEcoscoreGrade($this->response['product']['ecoscore_grade'] ?? null)
            ->setEcoscoreScore($this->response['product']['ecoscore_score'] ?? null)
            ->setNutriscoreGrade($this->response['product']['nutriscore_grade'] ?? null)
            ->setNutriscoreScore($this->response['product']['nutriscore_score'] ?? null)
            ->setQuantity($this->response['product']['quantity'] ?? null)
            ->setIngredientsText($this->response['product']['ingredients_text'] ?? null);

        $product = new Product();
        $product
            ->setEan($this->response['code'])
            ->setName($this->getProductName())
            ->setBrand($this->response['product']['brands'])
            ->setCategories($this->getCategories())
            ->setNutrition($nutrition);

        $file = $this->getImage->get($this->response['product']['image_url'] ?? null);
        if (null !== $file) {
            $product->setImage($file);
        }

        $file = $this->getImage->get($this->response['product']['image_ingredients_url'] ?? null);
        if (null !== $file) {
            $product->setImageIngredients($file);
        }

        $file = $this->getImage->get($this->response['product']['image_nutrition_url'] ?? null);
        if (null !== $file) {
            $product->setImageNutrition($file);
        }

        $this->em->persist($product);
        $this->em->flush();

        $this->productStatusHelper->setStatus(
            ProductStatusReference::WAITING_VALIDATION,
            $product
        );

        return $product;
    }

    private function getCategories(): ?string
    {
        if (array_key_exists('categories_imported', $this->response['product'])) {
            return $this->response['product']['categories_imported'];
        }

        if (array_key_exists('categories_old', $this->response['product'])) {
            return $this->response['product']['categories_old'];
        }

        if (array_key_exists('categories', $this->response['product'])) {
            return $this->response['product']['categories'];
        }

        return null;
    }

    private function getProductName(): string
    {
        if (array_key_exists('product_name_fr_imported', $this->response['product'])) {
            return ucwords(strtolower(trim($this->response['product']['product_name_fr_imported'])));
        }

        if (array_key_exists('product_name_fr', $this->response['product'])) {
            return ucwords(strtolower(trim($this->response['product']['product_name_fr'])));
        }

        if (array_key_exists('product_name', $this->response['product'])) {
            return ucwords(strtolower(trim($this->response['product']['product_name'])));
        }

        throw new HttpException(404, 'product not found.');
    }
}
