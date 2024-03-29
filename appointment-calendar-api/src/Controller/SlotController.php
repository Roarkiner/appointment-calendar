<?php

namespace App\Controller;

use App\Entity\Slot;
use App\Repository\SlotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SlotController extends AbstractController
{
    #[Route('/api/slot/{id}', name: 'slot.get', methods: ['GET'])]
    public function getSlot(int $id, SlotRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $slot = $repository->findActive($id);

        if (!$slot || !$slot->isStatus()) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $context = SerializationContext::create()->setGroups(["getSlot"]);
        $jsonSlot = $serializer->serialize($slot, 'json', $context);
        return new JsonResponse($jsonSlot, Response::HTTP_OK, [], true);

    }

    #[Route('/api/slot', name: 'slot.get_all', methods: ['GET'])]
    public function getAllSlots(SerializerInterface $serializer, SlotRepository $repository): JsonResponse
    {
        $slots = $repository->findAllActive();

        $context = SerializationContext::create()->setGroups(["getSlot"]);
        $jsonSlots = $serializer->serialize($slots, 'json', $context);
        return new JsonResponse($jsonSlots, Response::HTTP_OK, [], true);
    }

    #[Route('/api/slot', name: 'slot.create', methods: ['POST'])]
    public function createSlot(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        /** @var Slot $requestModel */
        $slot = $serializer->deserialize(
            $request->getContent(),
            Slot::class, 
            'json'
        );
        
        $slot->setStatus(true);
    
        $errors = $validator->validate($slot);
        
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $messages], Response::HTTP_BAD_REQUEST);
        }
        
        $entityManager->persist($slot);
        $entityManager->flush();
        
        $context = SerializationContext::create()->setGroups(["getSlot"]);
        $jsonSlot = $serializer->serialize($slot, 'json', $context);
        return new JsonResponse($jsonSlot, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/slot/{id}', name: 'slot.update', methods: ['PATCH'])]
    public function updateSlot(int $id, Request $request, SlotRepository $repository, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $slot = $repository->findActive($id);
        if (!$slot) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        /** @var Slot $requestSlot */
        $requestSlot = $serializer->deserialize(
            $request->getContent(),
            Slot::class,
            'json'
        );
        
        $slot->setStartDate($requestSlot->getStartDate() ?? $slot->getStartDate())
        ->setEndDate($requestSlot->getEndDate() ?? $slot->getEndDate())
        ->setStatus(true);

        $errors = $validator->validate($slot);
        
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $messages], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->flush();

        $context = SerializationContext::create()->setGroups(["getSlot"]);
        $jsonSlot = $serializer->serialize($slot, 'json', $context);
        return new JsonResponse($jsonSlot, Response::HTTP_OK, [], true);
    }

    #[Route('/api/slot/{id}', name: 'slot.delete', methods: ['DELETE'])]
    public function deleteSlot(int $id, SlotRepository $repository, EntityManagerInterface $entityManager): JsonResponse
    {
        $slot = $repository->findActive($id);

        if (!$slot) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $slot->setStatus(false);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
