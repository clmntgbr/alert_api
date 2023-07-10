<?php

namespace App\Command;

use App\Entity\Item;
use App\Entity\Notification;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserNotificationTimer;
use App\Repository\ItemRepository;
use App\Repository\ProductRepository;
use App\Repository\UserNotificationTimerRepository;
use Doctrine\ORM\EntityManagerInterface;
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

        if ($files === false) {
            return Command::FAILURE;
        }

        $flipped = array_flip($files);
        unset($flipped['.']);
        unset($flipped['..']);
        unset($flipped['.DS_Store']);

        foreach ($flipped as $key => $value) {
            $fn = fopen(sprintf('%s/../../public/openfoodfacts_test/%s', __DIR__, $key), "r");

            while (!feof($fn)) {
                $result = json_decode(fgets($fn), true);
                $product = $this->createProduct($result);
            }

            fclose($fn);
        }

        return Command::SUCCESS;
    }

    private function createProduct(array $result): ?Product
    {
        $product = new Product();
        $product
            ->setEan($result['code'] ?? '')
            ->setName($this->getOpenFoodFactProductName($result));

        $errors = $this->validator->validate($product, groups: new GroupSequence(['soft']));
        if (count($errors) > 0) {
            return;
        }

        return $product;
    }

    public function getOpenFoodFactProductName(array $result): string
    {
        if (array_key_exists('product_name_fr', $result) && '' !== $result['product_name_fr']) {
            return ucwords(strtolower(trim(strip_tags($result['product_name_fr']))));
        }

        if (array_key_exists('product_name', $result) && '' !== $result['product_name']) {
            return ucwords(strtolower(trim(strip_tags($result['product_name']))));
        }

        return null;
    }
}
