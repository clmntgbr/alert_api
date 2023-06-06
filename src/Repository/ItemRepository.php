<?php

namespace App\Repository;

use App\Entity\Item;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function save(Item $entity, bool $flush = false): void
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
     * @return Item[] Returns an array of Item objects
     *
     * @throws \Exception
     */
    public function findItemsByStoreAndExpireDate(User $user, string $timer): array
    {
        $date = (new \DateTime('now'))->add(new \DateInterval($timer));

        return $this->createQueryBuilder('i')
            ->where('i.expirationDate BETWEEN :date1 AND :date2')
            ->andWhere('i.store IN (:stores)')
            ->setParameters([
                'date1' => sprintf('%s 00:00:00', $date->format('Y-m-d')),
                'date2' => sprintf('%s 23:59:00', $date->format('Y-m-d')),
                'stores' => $user->getStores(),
            ])
            ->orderBy('i.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
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
