<?php

namespace App\Repository;

use App\Entity\Slot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @extends ServiceEntityRepository<Slot>
 *
 * @method Slot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Slot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Slot[]    findAll()
 * @method Slot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slot::class);
    }

    public function findActive($id): ?Slot
    {
        $qb = $this->createQueryBuilder('slot')
            ->where('slot.id = :id')
            ->andWhere('slot.status = :status')
            ->setParameter('id', $id)
            ->setParameter('status', true);

        $query = $qb->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findAllActive(): array
    {
        $qb = $this->createQueryBuilder('slot')
            ->where('slot.status = :status')
            ->setParameter('status', true);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function findValidSlotForAppointment(\DateTimeInterface $appointmentStartDate, \DateTimeInterface $appointmentEndDate): ?Slot
    {
        $qb = $this->createQueryBuilder('slot');

        try {
            $slot = $qb->where('slot.startDate <= :appointmentStartDate')
                       ->andWhere('slot.endDate >= :appointmentEndDate')
                       ->andWhere('slot.status = true')
                       ->setParameter('appointmentStartDate', $appointmentStartDate)
                       ->setParameter('appointmentEndDate', $appointmentEndDate)
                       ->getQuery()
                       ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }

        return $slot;
    }

    public function findOverlappingSlots(\DateTimeInterface $start, \DateTimeInterface $end, int $slotId = null): array
    {
        $qb = $this->createQueryBuilder('slot');
        $qb->where('slot.endDate > :start AND slot.startDate < :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end);

        if ($slotId !== null) 
        {
            $qb->andWhere('slot.id != :slotId')
                ->setParameter('slotId', $slotId);
        }

        return $qb->getQuery()->getResult();
    }

    
    public function findActiveBetweenDates(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.status = :status')
            ->andWhere('e.startDate >= :startDate')
            ->andWhere('e.endDate <= :endDate')
            ->orderBy('e.startDate', 'ASC')
            ->setParameter('status', true)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        return $qb->getQuery()->getResult();
    }
}
