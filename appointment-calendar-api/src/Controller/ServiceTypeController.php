<?php

namespace App\Controller;

use App\Entity\ServiceType;
use App\Model\ServiceTypeCreationRequestModel;
use App\Repository\ServiceTypeRepository;
use DateInterval;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints;

class ServiceTypeController extends AbstractController
{
    #[Route('/api/service-type/{id}', name: 'service_type.get', methods: ['GET'])]
    public function getServiceType(int $id, ServiceTypeRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $serviceType = $repository->findActive($id);

        if (!$serviceType || !$serviceType->isStatus()) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $context = SerializationContext::create()->setGroups(["getServiceType"]);
        $jsonServiceType = $serializer->serialize($serviceType, 'json', $context);
        return new JsonResponse($jsonServiceType, Response::HTTP_OK, [], true);

    }

    #[Route('/api/service-type', name: 'service_type.get_all', methods: ['GET'])]
    public function getAllServiceTypes(SerializerInterface $serializer, ServiceTypeRepository $repository): JsonResponse
    {
        $serviceTypes = $repository->findAllActive();

        $context = SerializationContext::create()->setGroups(["getServiceType"]);
        $jsonServiceTypes = $serializer->serialize($serviceTypes, 'json', $context);
        return new JsonResponse($jsonServiceTypes, Response::HTTP_OK, [], true);
    }

    #[Route('/api/service-type', name: 'service_type.create', methods: ['POST'])]
    public function createServiceType(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        /** @var ServiceTypeCreationRequestModel $requestModel */
        $requestModel = $serializer->deserialize(
            $request->getContent(),
            ServiceTypeCreationRequestModel::class, 
            'json'
        );
        
        $serviceType = new ServiceType();
        $serviceType->setName($requestModel->getName())
        ->setDuration(new DateInterval("PT{$requestModel->getDuration()}M"))
        ->setStatus(true);

        $durationInMinutes = $serviceType->getDuration()->days 
            * 1440
            + $serviceType->getDuration()->h 
            * 60
            + $serviceType->getDuration()->i;
            
        $constraint = new Constraints\Collection([
            'durationInMinutes' => [
                new Constraints\Range(['min' => 15, 'max' => 420]),
                new Constraints\DivisibleBy(15),
            ],
        ]);

        $durationViolation = $validator->validate(['durationInMinutes' => $durationInMinutes], $constraint);
        $errors = $validator->validate($serviceType);
        
        if (count($durationViolation) > 0 || count($errors) > 0) {
            $messages = [];
            foreach ($durationViolation as $violation) {
                $messages[] = $violation->getMessage();
            }
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $messages], Response::HTTP_BAD_REQUEST);
        }
        
        $entityManager->persist($serviceType);
        $entityManager->flush();
        
        $context = SerializationContext::create()->setGroups(["getServiceType"]);
        $jsonServiceType = $serializer->serialize($serviceType, 'json', $context);
        return new JsonResponse($jsonServiceType, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/service-type/{id}', name: 'service_type.update', methods: ['PATCH'])]
public function updateServiceType(int $id, Request $request, ServiceTypeRepository $repository, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
{
    $serviceType = $repository->findActive($id);
    if (!$serviceType) {
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    /** @var ServiceTypeCreationRequestModel $requestModel */
    $requestModel = $serializer->deserialize(
        $request->getContent(),
        ServiceTypeCreationRequestModel::class,
        'json'
    );
    
    $serviceType->setName($requestModel->getName() ?? $serviceType->getName())
    ->setDuration($requestModel->getDuration() != null ? 
        new DateInterval("PT{$requestModel->getDuration()}M") 
        : $serviceType->getDuration()
    )
    ->setStatus(true);

    $durationInMinutes = $serviceType->getDuration()->days 
        * 1440
        + $serviceType->getDuration()->h 
        * 60
        + $serviceType->getDuration()->i;

    $constraint = new Constraints\Collection([
        'durationInMinutes' => [
            new Constraints\Range(['min' => 15, 'max' => 420]),
            new Constraints\DivisibleBy(15),
        ],
    ]);

    $durationViolation = $validator->validate(['durationInMinutes' => $durationInMinutes], $constraint);
    $errors = $validator->validate($serviceType);
    
    if (count($durationViolation) > 0 || count($errors) > 0) {
        $messages = [];
        foreach ($durationViolation as $violation) {
            $messages[] = $violation->getMessage();
        }
        foreach ($errors as $error) {
            $messages[] = $error->getMessage();
        }
        return new JsonResponse(['errors' => $messages], Response::HTTP_BAD_REQUEST);
    }

    $entityManager->flush();

    $context = SerializationContext::create()->setGroups(["getServiceType"]);
    $jsonServiceType = $serializer->serialize($serviceType, 'json', $context);
    return new JsonResponse($jsonServiceType, Response::HTTP_OK, [], true);
}

    #[Route('/api/service-type/{id}', name: 'service_type.delete', methods: ['DELETE'])]
    public function deleteServiceType(int $id, ServiceTypeRepository $repository, EntityManagerInterface $entityManager): JsonResponse
    {
        $serviceType = $repository->findActive($id);

        if (!$serviceType) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $serviceType->setStatus(false);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
