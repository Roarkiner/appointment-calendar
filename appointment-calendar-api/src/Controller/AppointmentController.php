<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Repository\AppointmentRepository;
use App\Repository\ServiceTypeRepository;
use App\Repository\SlotRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class AppointmentController extends AbstractController
{
    #[Route('/api/appointment/{id}', name: 'appointment.get', methods: ['GET'])]
    public function getAppointment(int $id,
    AppointmentRepository $repository, 
    SerializerInterface $serializer, 
    Request $request, 
    CacheInterface $cache): JsonResponse
    {
        $cacheKey = "appointment.get/{$id}";

        $appointment = $cache->get($cacheKey, function (ItemInterface $item) use ($repository, $id) {
            $item->expiresAfter(3600);

            return $repository->findActive($id);
        });

        if (!$appointment || !$appointment->isStatus()) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $context = SerializationContext::create()->setGroups(["getAppointment"]);
        $jsonAppointment = $serializer->serialize($appointment, 'json', $context);

        //Client-side cache
        $etag = md5($jsonAppointment);

        if ($request->headers->get('If-None-Match') === $etag) {
            return new JsonResponse(null, Response::HTTP_NOT_MODIFIED);
        }        

        return new JsonResponse($jsonAppointment, Response::HTTP_OK, ['ETag' => $etag], true);

    }

    #[Route('/api/appointment', name: 'appointment.get_all', methods: ['GET'])]
    public function getAllAppointments(SerializerInterface $serializer, 
        AppointmentRepository $repository, 
        Request $request, 
        TagAwareCacheInterface $cache): JsonResponse
    {
        $requestStartDate = $request->query->get('start_date');
        $requestEndDate = $request->query->get('end_date');
        if ($requestStartDate != null && $requestEndDate != null)
        {
            $startDate = new \DateTime($requestStartDate);
            $endDate = new \DateTime($requestEndDate);
    
            $cacheKey = "appointment.get_all.{$startDate->format('Y-m-d H:m')}.{$endDate->format('Y-m-d H:m')}";

            $appointments = $cache->get($cacheKey, function (ItemInterface $item) use ($repository, $startDate, $endDate) {
                $item->expiresAfter(3600);
    
                $item->tag(['appointment_get_all']);
        
                return $repository->findActiveBetweenDates($startDate, $endDate);
            });
        } else {
            $cacheKey = "appointment.get_all";

            $appointments = $cache->get($cacheKey, function (ItemInterface $item) use ($repository) {
                $item->expiresAfter(3600);
    
                $item->tag(['appointment_get_all']);
        
                return $repository->findAllActive();
            });
        }


        $context = SerializationContext::create()->setGroups(["getAppointment"]);
        $jsonAppointments = $serializer->serialize($appointments, 'json', $context);

        //Client-side cache
        $etag = md5($jsonAppointments);

        if ($request->headers->get('If-None-Match') === $etag) {
            return new JsonResponse(null, Response::HTTP_NOT_MODIFIED);
        }

        return new JsonResponse($jsonAppointments, Response::HTTP_OK, ['ETag' => $etag], true);
    }

    #[Route('/api/appointment', name: 'appointment.create', methods: ['POST'])]
    public function createAppointment(Request $request, 
        EntityManagerInterface $entityManager, 
        SerializerInterface $serializer, 
        ValidatorInterface $validator,
        AppointmentRepository $appointmentRepository,
        UserRepository $userRepository, 
        ServiceTypeRepository $serviceTypeRepository,
        SlotRepository $slotRepository, 
        TagAwareCacheInterface $cache): JsonResponse
    {
        /** @var Appointment $appointment */
        $appointment = $serializer->deserialize(
            $request->getContent(),
            Appointment::class, 
            'json'
        );
        
        $appointment->setStatus(true);
    
        $errors = $validator->validate($appointment);
        
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $messages], Response::HTTP_BAD_REQUEST);
        }

        //Check if one of multiple appointment already exist for the period of teh new appointment
        $doesOverlap = $appointmentRepository->doesAppointmentOverlap($appointment->getStartDate(), $appointment->getEndDate());
        if ($doesOverlap) {
            return new JsonResponse(['errors' => 'Appointment overlaps with another appointment'], Response::HTTP_CONFLICT);
        }

        //Check that the service sype id provided exists
        $content = $request->toArray();
        
        $serviceType = $serviceTypeRepository->findActive($content['service_type_id'] ?? 0);
        if (!$serviceType) {
            return new JsonResponse(['errors' => 'ServiceType not found or is inactive'], Response::HTTP_BAD_REQUEST);
        }

        //Check that the user id provided exists
        $user = $userRepository->findActive($content['user_id'] ?? 0);
        if (!$user) {
            return new JsonResponse(['errors' => 'User not found or is inactive'], Response::HTTP_BAD_REQUEST);
        }
        
        //Check that the appointment is created inside an existing slot
        $slot = $slotRepository->findValidSlotForAppointment($appointment->getStartDate(), $appointment->getEndDate());
        if (!$slot) {
            return new JsonResponse(['errors' => 'The appointment should be within a valid slot'], Response::HTTP_BAD_REQUEST);
        }

        $appointment->setServiceType($serviceType)
        ->setUser($user);

        $entityManager->persist($appointment);
        $entityManager->flush();

        $cache->invalidateTags(['appointment_get_all']);
        $cache->delete("user.get/{$appointment->getUser()->getId()}");
        
        $context = SerializationContext::create()->setGroups(["getAppointment"]);
        $jsonAppointment = $serializer->serialize($appointment, 'json', $context);
        return new JsonResponse($jsonAppointment, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/appointment/{id}', name: 'appointment.update', methods: ['PATCH'])]
    public function updateAppointment(int $id, 
        Request $request, 
        AppointmentRepository $repository, 
        EntityManagerInterface $entityManager, 
        SerializerInterface $serializer, 
        ValidatorInterface $validator, 
        AppointmentRepository $appointmentRepository,
        UserRepository $userRepository, 
        ServiceTypeRepository $serviceTypeRepository,
        SlotRepository $slotRepository, 
        TagAwareCacheInterface $cache): JsonResponse
    {
        $appointment = $repository->findActive($id);
        if (!$appointment) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $currentUser = $this->getUser();

        if (in_array("ROLE_ADMIN", $currentUser->getRoles()) && $currentUser->getUserIdentifier() != $appointment->getUser()->getEmail())
        {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }

        /** @var Appointment $requestAppointment */
        $requestAppointment = $serializer->deserialize(
            $request->getContent(),
            Appointment::class,
            'json'
        );
        
        $appointment->setStartDate($requestAppointment->getStartDate() ?? $appointment->getStartDate())
        ->setEndDate($requestAppointment->getEndDate() ?? $appointment->getEndDate())
        ->setStatus(true);

        $errors = $validator->validate($appointment);
        
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $messages], Response::HTTP_BAD_REQUEST);
        }

        //Check if one of multiple appointment already exist for the period of teh new appointment
        $doesOverlap = $appointmentRepository->doesAppointmentOverlap($appointment->getStartDate(), $appointment->getEndDate(), $appointment->getId());
        if ($doesOverlap) {
            return new JsonResponse(['errors' => 'Appointment overlaps with another appointment'], Response::HTTP_CONFLICT);
        }

        $content = $request->toArray();

        $serviceType = $appointment->getServiceType();

        if (isset($content['service_type_id']))
        {
            //Check that the service sype id provided exists
            $serviceType = $serviceTypeRepository->findActive($content['service_type_id'] ?? 0);
            if (!$serviceType) {
                return new JsonResponse(['errors' => 'ServiceType not found or is inactive'], Response::HTTP_BAD_REQUEST);
            }
        }
        
        $user = $appointment->getUser();

        if (isset($content['user_id']))
        {
            //Check that the user id provided exists
            $user = $userRepository->findActive($content['user_id'] ?? 0);
            if (!$user) {
                return new JsonResponse(['errors' => 'User not found or is inactive'], Response::HTTP_BAD_REQUEST);
            }
        }

        //Check that the appointment is created inside an existing slot
        $slot = $slotRepository->findValidSlotForAppointment($appointment->getStartDate(), $appointment->getEndDate());
        if (!$slot) {
            return new JsonResponse(['errors' => 'The appointment should be within a valid slot'], Response::HTTP_BAD_REQUEST);
        }

        $appointment->setUser($user)
        ->setServiceType($serviceType);

        $entityManager->persist($appointment);
        $entityManager->flush();
        
        $cache->invalidateTags(['appointment_get_all']);
        $cache->delete("appointment.get/{$id}");
        $cache->delete("user.get/{$appointment->getUser()->getId()}");

        $context = SerializationContext::create()->setGroups(["getAppointment"]);
        $jsonAppointment = $serializer->serialize($appointment, 'json', $context);
        return new JsonResponse($jsonAppointment, Response::HTTP_OK, [], true);
    }

    #[Route('/api/appointment/{id}', name: 'appointment.delete', methods: ['DELETE'])]
    public function deleteAppointment(int $id, 
        AppointmentRepository $repository, 
        EntityManagerInterface $entityManager, 
        TagAwareCacheInterface $cache): JsonResponse
    {
        $appointment = $repository->findActive($id);

        if (!$appointment) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $appointment->setStatus(false);
        $entityManager->flush();

        $cache->invalidateTags(['appointment_get_all']);
        $cache->delete("appointment.get/{$id}");
        $cache->delete("user.get/{$appointment->getUser()->getId()}");

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
