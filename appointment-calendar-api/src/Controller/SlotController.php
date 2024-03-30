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
    public function createSlot(Request $request, 
        EntityManagerInterface $entityManager,
        SlotRepository $slotRepository,
        SerializerInterface $serializer, 
        ValidatorInterface $validator): JsonResponse
    {
        /** @var Slot $newSlot  */
        $newSlot  = $serializer->deserialize(
            $request->getContent(),
            Slot::class, 
            'json'
        );
        
        $newSlot ->setStatus(true);
    
        $errors = $validator->validate($newSlot );
        
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $messages], Response::HTTP_BAD_REQUEST);
        }

        $mergedSlotIds = [];

        $overlappingSlots = $slotRepository->findOverlappingSlots($newSlot->getStartDate(), $newSlot->getEndDate());

        foreach ($overlappingSlots as $overlap) {
            if ($overlap->getStartDate() < $newSlot->getStartDate()) {
                $newSlot->setStartDate($overlap->getStartDate());
            }
            if ($overlap->getEndDate() > $newSlot->getEndDate()) {
                $newSlot->setEndDate($overlap->getEndDate());
            }
            array_push($mergedSlotIds, $overlap->getId());
            $entityManager->remove($overlap);
        }

        $entityManager->persist($newSlot);
        $entityManager->flush();
        
        $context = SerializationContext::create()->setGroups(["getSlot"]);
        $jsonSlot = $serializer->serialize($newSlot, 'json', $context);
        $responseMessage = [
            'slot' => json_decode($jsonSlot),
            'message' => 'Merged with slots: ' . implode(', ', $mergedSlotIds)
        ];
        return new JsonResponse($responseMessage, Response::HTTP_CREATED, [], false);
    }

    #[Route('/api/slot/{id}', name: 'slot.update', methods: ['PATCH'])]
    public function updateSlot(int $id, 
        Request $request, 
        SlotRepository $slotRepository, 
        EntityManagerInterface $entityManager, 
        SerializerInterface $serializer, 
        ValidatorInterface $validator): JsonResponse
    {
        $existingSlot = $slotRepository->findActive($id);
        if (!$existingSlot) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        /** @var Slot $requestSlot */
        $requestSlot = $serializer->deserialize(
            $request->getContent(),
            Slot::class,
            'json'
        );
        
        $existingSlot->setStartDate($requestSlot->getStartDate() ?? $existingSlot->getStartDate())
        ->setEndDate($requestSlot->getEndDate() ?? $existingSlot->getEndDate())
        ->setStatus(true);

        $errors = $validator->validate($existingSlot);
        
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $messages], Response::HTTP_BAD_REQUEST);
        }

        $mergedSlotIds = [];

        $overlappingSlots = $slotRepository->findOverlappingSlots($existingSlot->getStartDate(), $existingSlot->getEndDate());

        foreach ($overlappingSlots as $overlap) {
            if ($overlap->getStartDate() < $existingSlot->getStartDate()) {
                $existingSlot->setStartDate($overlap->getStartDate());
            }
            if ($overlap->getEndDate() > $existingSlot->getEndDate()) {
                $existingSlot->setEndDate($overlap->getEndDate());
            }
            array_push($mergedSlotIds, $overlap->getId());
            $entityManager->remove($overlap);
        }

        $entityManager->persist($existingSlot);
        $entityManager->flush();

        $context = SerializationContext::create()->setGroups(["getSlot"]);
        $jsonSlot = $serializer->serialize($existingSlot, 'json', $context);
        $responseMessage = [
            'slot' => json_decode($jsonSlot),
            'message' => 'Merged with slots: ' . implode(', ', $mergedSlotIds)
        ];
        return new JsonResponse($responseMessage, Response::HTTP_OK, [], false);
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
