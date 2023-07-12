<?php

namespace App\Command;

use App\Entity\Product;
use App\Message\CreateProductMessage;
use App\Repository\ProductRepository;
use App\Repository\UserNotificationTimerRepository;
use App\Service\OpenFoodFactApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
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
        private readonly MessageBusInterface $messageBus,
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
        $files = scandir(sprintf('%s/../../public/openfoodfacts_test', __DIR__), SCANDIR_SORT_ASCENDING);

        if (false === $files) {
            return Command::FAILURE;
        }

        $flipped = array_flip($files);
        unset($flipped['.']);
        unset($flipped['..']);
        unset($flipped['.DS_Store']);

        $progressBar = new ProgressBar($output, count($flipped));
        $progressBar->setFormat('verbose');

        foreach ($flipped as $key => $value) {
            $fn = fopen(sprintf('%s/../../public/openfoodfacts_test/%s', __DIR__, $key), 'r');
            $progressBar->advance();

            while (!feof($fn)) {
                $result = json_decode(fgets($fn), true);
                $this->createProduct($result ?? []);
            }

            fclose($fn);
        }

        $progressBar->finish();

        return Command::SUCCESS;
    }

    private function createProduct(array $result)
    {
        $product = $this->productRepository->findOneBy(['ean' => $result['code'] ?? '']);
        if ($product instanceof Product) {
            return;
        }

        $this->messageBus->dispatch(
            new CreateProductMessage($result['code'] ?? 'unknown', $result)
        );

        return;
    }
}
