<?php

namespace App\Command;

use App\Entity\Item;
use App\Entity\Notification;
use App\Entity\Product;
use App\Entity\ProductNutrition;
use App\Entity\User;
use App\Entity\UserNotificationTimer;
use App\Repository\ItemRepository;
use App\Repository\ProductRepository;
use App\Repository\UserNotificationTimerRepository;
use App\Service\OpenFoodFactApiService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Hautelook\AliceBundle\Functional\TestBundle\Entity\Prod;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:create-products',
    description: 'Creating products from OpenFoodFacts CSV',
)]
class CreateProductsCommand extends Command
{
    public function __construct(
        private readonly UserNotificationTimerRepository $userNotificationTimerRepository,
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $em,
        private readonly ValidatorInterface $validator,
        private readonly OpenFoodFactApiService $openFoodFactApiService,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $files = scandir(sprintf('%s/../../public/openfoodfacts_test', __DIR__), SCANDIR_SORT_ASCENDING);

        if ($files === false) {
            return Command::FAILURE;
        }

        $flipped = array_flip($files);
        unset($flipped['.']);
        unset($flipped['..']);
        unset($flipped['.DS_Store']);

        $io->createProgressBar(count($flipped));
        $io->progressStart();

        foreach ($flipped as $key => $value) {
            $fn = fopen(sprintf('%s/../../public/openfoodfacts_test/%s', __DIR__, $key), "r");

            while (!feof($fn)) {
                $result = json_decode(fgets($fn), true);
                try {
                    $this->createProduct($result);
                } catch (Exception $e) {
                    continue;
                }
            }

            fclose($fn);
            $io->progressAdvance();
        }

        $io->progressFinish();

        return Command::SUCCESS;
    }

    private function createProduct(array $result)
    {
        $product = $this->productRepository->findOneBy(['ean' => $result['code'] ?? '']);
        if ($product instanceof Product) {
            return;
        }

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
