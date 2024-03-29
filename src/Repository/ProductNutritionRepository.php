<?php

namespace App\Repository;

use App\Entity\ProductNutrition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductNutrition>
 *
 * @method ProductNutrition|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductNutrition|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductNutrition[]    findAll()
 * @method ProductNutrition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductNutritionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductNutrition::class);
    }

    public function save(ProductNutrition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProductNutrition $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
