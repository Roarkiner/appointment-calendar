<?php

namespace App\Repository;

use App\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointment>
 *
 * @method Appointment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Appointment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Appointment[]    findAll()
 * @method Appointment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    public function findActive($id): ?Appointment
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
            ->leftJoin('e.serviceType', 'service_type')
            ->addSelect('service_type')  
            ->setParameter('status', true);

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function doesAppointmentOverlap(\DateTimeInterface $start, \DateTimeInterface $end, int $appointmentId = null): bool
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.startDate between :start and :end')
            ->orWhere('e.endDate between :start and :end')
            ->orWhere(':start between e.startDate and e.endDate')
            ->andWhere('e.status = :status')
            ->setParameter('end', $end)
            ->setParameter('start', $start)
            ->setParameter('status', true);

        if ($appointmentId !== null) 
        {
            $qb->andWhere('e.id != :appointmentId')
                ->setParameter('appointmentId', $appointmentId);
        }

        $query = $qb->getQuery();
        $result = $query->getResult();

        return count($result) > 0;
    }

    public function findAppointmentsInNext24Hours(): array
    {
        $now = new \DateTime();
        
        $startTime = (clone $now)->modify('+24 hours');
    
        $endTime = (clone $now)->modify('+24 hours 15 minutes');
        
        $qb = $this->createQueryBuilder('e');
    
        $qb->where('e.startDate >= :startTime')
        ->andWhere('e.startDate <= :endTime')
        ->setParameter('startTime', $startTime)
        ->setParameter('endTime', $endTime);
        
        return $qb->getQuery()->getResult();
    }
}
