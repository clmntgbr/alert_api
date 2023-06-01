<?php

namespace App\Service;

use App\Entity\Nutrition;
use App\Entity\Product;
use App\Entity\ProductNutrition;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Safe\Exceptions\JsonException;

class PostProductByEanService
{
    public function __construct(
        private readonly ProductRepository      $productRepository,
        private readonly OpenFoodFactApiService $openFoodFactApi,
        private readonly EntityManagerInterface $em
    )
    {
    }

    /**
     * @throws \HttpException
     * @throws JsonException
     */
    public function find(?string $ean): Product
    {
        if (null === $ean) {
            throw new \HttpException('Ean should\'nt be null.', 404);
        }

        $product = $this->productRepository->findOneBy(['ean' => $ean]);

        if ($product instanceof Product) {
            return $product;
        }

        $response = $this->openFoodFactApi->getProduct($ean);
        $product = new Product();
        $product->setResponse($response);

        if (isset($response['status_verbose']) && in_array($response['status_verbose'], ['no code or invalid code', 'product not found'])) {
            return $this->createUnfoundedProduct($ean, $product);
        }

        return $this->createFoundedProduct($response, $product);
    }

    private function createFoundedProduct(array $response, Product $product): Product
    {
        $nutrition = new ProductNutrition();

        $nutrition
            ->setEcoscoreGrade($response['product']['ecoscore_grade'] ?? null)
            ->setEcoscoreScore($response['product']['ecoscore_score'] ?? null)
            ->setNutriscoreGrade($response['product']['nutriscore_grade'] ?? null)
            ->setNutriscoreScore($response['product']['nutriscore_score'] ?? null)
            ->setQuantity($response['product']['quantity'] ?? null)
            ->setIngredientsText($this->openFoodFactApi->getOpenFoodFactProductNutritionIngredients($response));

        $product
            ->setEan($response['code'])
            ->setLink($this->openFoodFactApi->getOpenFoodFactProductLink($response))
            ->setOrigin($this->openFoodFactApi->getOpenFoodFactProductOrigin($response))
            ->setManufacturingPlace($this->openFoodFactApi->getOpenFoodFactProductManufacturingPlace($response))
            ->setName($this->openFoodFactApi->getOpenFoodFactProductName($response))
            ->setBrand($response['product']['brands'])
            ->setCategories($this->openFoodFactApi->getOpenFoodFactProductCategories($response))
            ->setProductNutrition($nutrition)
            ->setStatus(Product::WAITING_VALIDATION);

        $file = $this->openFoodFactApi->getOpenFoodFactProductImage($response['product']['image_url'] ?? null);
        if (null !== $file) {
            $product->setImage($file);
        }

        $file = $this->openFoodFactApi->getOpenFoodFactProductImage($response['product']['image_ingredients_url'] ?? null);
        if (null !== $file) {
            $product->setImageIngredients($file);
        }

        $file = $this->openFoodFactApi->getOpenFoodFactProductImage($response['product']['image_nutrition_url'] ?? null);
        if (null !== $file) {
            $product->setImageNutrition($file);
        }

        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }

    private function createUnfoundedProduct(string $ean, Product $product): Product
    {
        $product
            ->setEan($ean)
            ->setProductNutrition(new ProductNutrition())
            ->setStatus(Product::NOT_FOUND);

        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }
}