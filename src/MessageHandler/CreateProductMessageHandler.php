<?php

namespace App\MessageHandler;

use App\Entity\Product;
use App\Entity\ProductNutrition;
use App\Message\CreateProductMessage;
use App\Repository\ProductRepository;
use App\Repository\UserNotificationTimerRepository;
use App\Service\OpenFoodFactApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler()]
class CreateProductMessageHandler
{
    public function __construct(
        private readonly UserNotificationTimerRepository $userNotificationTimerRepository,
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $em,
        private readonly ValidatorInterface $validator,
        private readonly OpenFoodFactApiService $openFoodFactApiService,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    public function __invoke(CreateProductMessage $message)
    {
        $ean = $message->getEan();
        $result = $message->getData();

        $product = new Product();
        $product
            ->setEan($result['code'] ?? '')
            ->setName($this->openFoodFactApiService->getOpenFoodFactProductName($result));

        $errors = $this->validator->validate($product, groups: new GroupSequence(['soft']));
        if (count($errors) > 0) {
            return;
        }

        $nutrition = new ProductNutrition();

        $nutrition
            ->setEcoscoreGrade($result['ecoscore_grade'] ?? null)
            ->setEcoscoreScore($result['ecoscore_score'] ?? null)
            ->setNutriscoreGrade($result['nutriscore_grade'] ?? null)
            ->setIngredientsText($this->openFoodFactApiService->getOpenFoodFactProductNutritionIngredients($result))
            ->setNutriscoreScore($result['nutriscore_score'] ?? null);

        $product
            ->setBrand($result['brands'] ?? null)
            ->setProductNutrition($nutrition)
            ->setLink($this->openFoodFactApiService->getOpenFoodFactProductLink($result))
            ->setOrigin($this->openFoodFactApiService->getOpenFoodFactProductOrigin($result))
            ->setManufacturingPlace($this->openFoodFactApiService->getOpenFoodFactProductManufacturingPlace($result, 'manufacturing_places'))
            ->setName($this->openFoodFactApiService->getOpenFoodFactProductName($result))
            ->setCategories($this->openFoodFactApiService->getOpenFoodFactProductCategories($result))
            ->setStatus(Product::PENDING);

        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }
}
