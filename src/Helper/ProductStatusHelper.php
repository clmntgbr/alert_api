<?php

namespace App\Helper;

use App\Entity\Product;
use App\Entity\ProductStatusHistory;
use App\Repository\ProductStatusRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductStatusHelper
{
    public function __construct(
        private EntityManagerInterface  $em,
        private ProductStatusRepository $productStatusRepository
    )
    {
    }

    public function setStatus(string $reference, Product $product): void
    {
        $productStatus = $this->productStatusRepository->findOneBy(['reference' => $reference]);

        if (null === $productStatus) {
            throw new \Exception(sprintf('Product Status don\'t exist (reference : %s', $reference));
        }

        $product->setProductStatus($productStatus);

        $productStatusHistory = new ProductStatusHistory();
        $productStatusHistory
            ->setProductStatus($productStatus)
            ->setProduct($product);

        $this->em->persist($product);
        $this->em->persist($productStatusHistory);

        $this->em->flush();
    }
}