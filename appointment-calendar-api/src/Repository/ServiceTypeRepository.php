<?php

namespace App\Repository;

use App\Entity\ServiceType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServiceType>
 *
 * @method ServiceType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceType[]    findAll()
 * @method ServiceType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceType::class);
    }

    public function findActive($id): ?ServiceType
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.id = :id')
            ->andWhere('e.status = :status')
            ->setParameter('id', $id)
            ->setParameter('status', true);

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findAllActive(): array
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.status = :status')
            ->setParameter('status', true);

        $query = $qb->getQuery();

        return $query->getResult();
    }
}
