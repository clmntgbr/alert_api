<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Store;

/**
 * @extends ServiceEntityRepository<Item>
 *
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

    public function add(Item $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Item $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Item[]
     */
    public function findItemsExpireSoon(int $limit, Store $store): array
    {
        $date = (new \DateTime('now'))->format('Y-m-d');

        return $this->createQueryBuilder('i')
            ->where('i.store = :store ')
            ->andWhere('i.expirationDate is not null')
            ->andWhere('i.expirationDate >= :date')
            ->setParameter('date', $date)
            ->setParameter('store', $store)
            ->orderBy('i.expirationDate', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Item[]
     */
    public function findItemsExpired(int $limit, Store $store): array
    {
        $date = (new \DateTime('now'))->format('Y-m-d');

        return $this->createQueryBuilder('i')
            ->where('i.store = :store ')
            ->andWhere('i.expirationDate is not null')
            ->andWhere('i.expirationDate < :date')
            ->setParameter('date', $date)
            ->setParameter('store', $store)
            ->orderBy('i.expirationDate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?Item
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}