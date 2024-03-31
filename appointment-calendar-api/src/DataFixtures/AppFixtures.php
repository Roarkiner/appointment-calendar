<?php

namespace App\DataFixtures;

use App\Entity\Appointment;
use App\Entity\ServiceType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Slot;
use DateTime;
use DateInterval;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * Faker Generator
     *
     * @var Generator
     */
    private Generator $faker;
    
    /**
     * Class that hashs the password
     *
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $passHasher;

    public function __construct(UserPasswordHasherInterface $passHasher)
    {
        $this->faker = Factory::create('en_GB');
        $this->passHasher = $passHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail("emailerdemo8@gmail.com")
        ->setLastname("Emailer")
        ->setFirstname("Demo")
        ->setRoles(["ROLE_ADMIN"])
        ->setPassword($this->passHasher->hashPassword($admin, "admin"))
        ->setStatus(true);
        $manager->persist($admin);
            
        $user = new User();
        $user->setEmail("sample_user@gmail.com")
        ->setLastname("Sample")
        ->setFirstname("User")
        ->setRoles(["ROLE_USER"])
        ->setPassword($this->passHasher->hashPassword($user, "user"))
        ->setStatus(true);
        $manager->persist($user);

        $serviceTypes = [];

        for ($i=0; $i < 5; $i++) { 
            $serviceType = $this->GenerateServiceType();
            array_push($serviceTypes, $serviceType);

            $manager->persist($serviceType);
        }
        
        $currentDate = new DateTime();
            
        for ($i=1; $i <= 50; $i++) { 
            $slot = $this->GenerateSlot($currentDate);

            $manager->persist($slot);

            if ($i % 5 === 0) {
                $appointment = $this->GenerateAppointment($slot, $serviceTypes, $user);
                if ($appointment != null) 
                {
                    $manager->persist($appointment);
                }
            }
        }

        $manager->flush();
    }

    private function GenerateServiceType(): ServiceType
    {
        $serviceType = new ServiceType();

        $randomQuarters = rand(1, 8);
        $randomMinutes = $randomQuarters * 15;
        $intervalSpec = "PT" . $randomMinutes . "M";

        $dateInterval = new DateInterval($intervalSpec);

        $serviceType->setName($this->faker->words(2, true))
        ->setDuration($dateInterval)
        ->setStatus(true);

        return $serviceType;
    }

    private function GenerateSlot(DateTime $currentDate): Slot
    {
        $slot = new Slot();

        $startHour = mt_rand(9, 14);
        $startTime = clone $currentDate->modify("+1 day")->setTime($startHour, mt_rand(0, 3) * 15);
        $endHour = mt_rand($startHour + 2, 18);
        $endTime = clone $currentDate->setTime($endHour, mt_rand(0, 3) * 15);

        $slot->setStartDate($startTime)
        ->setEndDate($endTime)
        ->setStatus(true);
        
        return $slot;
    }

    private function GenerateAppointment(Slot $slot, array $serviceTypes, User $user): ?Appointment
    {
        $appointment = new Appointment();
        $randomServiceType = $this->faker->randomElement($serviceTypes); 

        // I want to get a random starting point for my appointment in my slot, and be sure it can't end after slot's end
        $diffBetweenDates = (int) (($slot->getEndDate()->getTimestamp() - $slot->getStartDate()->getTimestamp()) / 60);
        $serviceTypeDuration = $randomServiceType->getDuration()->i;

        $availableRange = $diffBetweenDates - $serviceTypeDuration;

        if ($availableRange < 0) {
            return null;
        }

        $randomStartMinutes = rand(0, $availableRange);
        $randomStartDate = DateTime::createFromInterface($slot->getStartDate());
        $randomStartDate->add(new DateInterval("PT{$randomStartMinutes}M"));

        $randomHourMinutes = rand(0, 3) * 15;
        $randomStartDate->setTime((int)$randomStartDate->format('H'), $randomHourMinutes, 0);
        $randomEndDate = clone $randomStartDate;
        $randomEndDate->add(new DateInterval("PT{$serviceTypeDuration}M"));

        $appointment->setStartDate($randomStartDate)
        ->setEndDate($randomEndDate)
        ->setServiceType($randomServiceType)
        ->setUser($user)
        ->setStatus(true);

        return $appointment;
    }
}
