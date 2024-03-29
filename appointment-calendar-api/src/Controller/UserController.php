<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\SerializationContext;

class UserController extends AbstractController
{
    #[Route('/api/user/current', name: 'user.get_current', methods:['GET'])]
    public function getCurrentUserInfos(SerializerInterface $serializer): JsonResponse
    {
        $currentUser = $this->getUser();

        $context = SerializationContext::create()->setGroups(["getUserInfos"])->setSerializeNull(true);

        $jsonCurrentUser = $serializer->serialize($currentUser, 'json', $context);

        return new JsonResponse($jsonCurrentUser, Response::HTTP_OK, [], true);
    }

    #[Route('/api/register', name: 'user.register', methods: ['POST'])]
    public function registerUser(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher, 
        SerializerInterface $serializer, 
        JWTTokenManagerInterface $tokenManager, 
        ValidatorInterface $validator): JsonResponse
    {
        $user = $serializer->deserialize(
            $request->getContent(),
            User::class,
            'json'
        );
        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);
        $user->setStatus(true);
        $user->setRoles(['ROLE_USER']);

        $errors = $validator->validate($user);
        if ($errors->count() > 0)
        {
            return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST, [], true);    
        }
        
        $entityManager->persist($user);
        $entityManager->flush();

        $token = $tokenManager->create($user);

        return new JsonResponse($token, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/user/{id}', name: 'user.delete', methods: ['DELETE'])]
    public function deactivateUser(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$user)
        {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $currentUser = $this->getUser();

        if (in_array("ROLE_ADMIN", $currentUser->getRoles()) || $user->getEmail() == $currentUser->getUserIdentifier())
        {
            $user->setStatus(false);
            $entityManager->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } else {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }
    }
}
