<?php

namespace App\ApiResource;

use App\Entity\Product;
use App\Service\PostProductByEanService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
class PostProductByEan extends AbstractController
{
    public function __construct(
        private readonly PostProductByEanService $productByEanService,
        private readonly ValidatorInterface $validator
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request, Product $data): Product
    {
        /** @var Product $product */
        $product = $request->attributes->get('data');

        $errors = $this->validator->validate($product, groups: new GroupSequence(['strict']));
        if (count($errors) > 0) {
            throw new \Exception('Ean is not valid.', 404);
        }

        if ($product instanceof Product) {
            return $this->productByEanService->find($product->getEan(), $product->getGeography());
        }

        throw new \Exception('Ean should\'nt be null.', 404);
    }
}
