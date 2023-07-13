<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\ProductNutrition;
use App\Repository\ProductRepository;
use Safe\Exceptions\JsonException;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostProductByEanService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly OpenFoodFactApiService $openFoodFactApi,
        private readonly ValidatorInterface $validator
    ) {
    }

    /**
     * @throws \Exception
     * @throws JsonException
     */
    public function find(?string $ean, string $geography): Product
    {
        if (null === $ean) {
            throw new \Exception('Ean should\'nt be null.', 404);
        }

        $product = $this->productRepository->findOneBy(['ean' => $ean]);
        if ($product instanceof Product) {
            return $product;
        }

        $response = $this->openFoodFactApi->getProduct($ean, $geography);


        $product = new Product();
        $product->setEan($ean);
        $product->setResponse($response);

        if (isset($response['status_verbose']) && in_array($response['status_verbose'], ['no code or invalid code', 'product not found'])) {
            return $this->createUnfoundedProduct($product);
        }

        return $this->createFoundedProduct($response, $product);
    }

    private function getProductNutrition(array $response, ProductNutrition $nutrition): ProductNutrition
    {
        $nutrition
            ->setEcoscoreGrade($response['product']['ecoscore_grade'] ?? null)
            ->setEcoscoreScore($response['product']['ecoscore_score'] ?? null)
            ->setNutriscoreGrade($response['product']['nutriscore_grade'] ?? null)
            ->setNutriscoreScore($response['product']['nutriscore_score'] ?? null)
            ->setIngredientsText($this->openFoodFactApi->getOpenFoodFactProductNutritionIngredients($response['product']));

        return $nutrition;
    }

    private function createFoundedProduct(array $response, Product $product): Product
    {
        $nutrition = $this->getProductNutrition($response, new ProductNutrition());

        $product
            ->setLink($this->openFoodFactApi->getOpenFoodFactProductLink($response['product']))
            ->setOrigin($this->openFoodFactApi->getOpenFoodFactProductOrigin($response['product']))
            ->setManufacturingPlace($this->openFoodFactApi->getOpenFoodFactProductManufacturingPlace($response['product']))
            ->setName($this->openFoodFactApi->getOpenFoodFactProductName($response['product']))
            ->setBrand($response['product']['brands'])
            ->setCategories($this->openFoodFactApi->getOpenFoodFactProductCategories($response['product']))
            ->setProductNutrition($nutrition)
            ->setStatus(Product::PENDING);

        $errors = $this->validator->validate($product, groups: new GroupSequence(['soft']));
        if (count($errors) > 0) {
            throw new \Exception((string) $errors, 404);
        }

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
        
        $this->productRepository->save($product, true);

        return $product;
    }

    private function createUnfoundedProduct(Product $product): Product
    {
        $product
            ->setProductNutrition(new ProductNutrition())
            ->setStatus(Product::NOT_FOUND);

        $this->productRepository->save($product, true);

        return $product;
    }
}
