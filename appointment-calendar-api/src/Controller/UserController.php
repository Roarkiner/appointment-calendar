<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Mime\Email;

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

    #[Route('/api/user/{id}', name: 'user.get', methods:['GET'])]
    public function getUserById(int $id,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        CacheInterface $cache,
        Request $request): JsonResponse
    {
        $cacheKey = "user.get/{$id}";

        $user = $cache->get($cacheKey, function (ItemInterface $item) use ($userRepository, $id) {
            $item->expiresAfter(3600);

            return $userRepository->findActiveWithAppointments($id);
        });

        $currentUser = $this->getUser();

        if (in_array("ROLE_ADMIN", $currentUser->getRoles()) || $user->getEmail() == $currentUser->getUserIdentifier())
        {
            $context = SerializationContext::create()->setGroups(["getUser"])->setSerializeNull(true);

            $jsonCurrentUser = $serializer->serialize($user, 'json', $context);

            //Client-side cache
            $etag = md5($jsonCurrentUser);

            if ($request->headers->get('If-None-Match') === $etag) {
                return new JsonResponse(null, Response::HTTP_NOT_MODIFIED);
            }

            return new JsonResponse($jsonCurrentUser, Response::HTTP_OK, ['ETag' => $etag], true);
        } else {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }
    }

    #[Route('/api/register', name: 'user.register', methods: ['POST'])]
    public function registerUser(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher, 
        SerializerInterface $serializer, 
        JWTTokenManagerInterface $tokenManager, 
        ValidatorInterface $validator, 
        MailerInterface $mailer): JsonResponse
    {
        /**
         * @var User $user
         */
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
        
        $email = (new Email())
        ->from('emailerdemo8@gmail.com')
        ->to($user->getEmail())
        ->subject('You account was created')
        ->html("<p>Hello, {$user->getFirstname()}</p>
        <p>Your account was succefully created.</p>");
    
        $mailer->send($email);
        
        $token = $tokenManager->create($user);
        
        return new JsonResponse($token, Response::HTTP_CREATED, [], true);
    }
    
    #[Route('/api/user/{id}', name: 'user.delete', methods: ['DELETE'])]
    public function deactivateUser(int $id, 
    UserRepository $userRepository, 
    EntityManagerInterface $entityManager,
    CacheInterface $cache): JsonResponse
    {
        $user = $userRepository->findActive($id);

        if (!$user)
        {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $userAppointments = $user->getAppointments();

        foreach ($userAppointments as $appointment) {
            $appointment->setStatus(false);
        }

        $currentUser = $this->getUser();

        if (in_array("ROLE_ADMIN", $currentUser->getRoles()) || $user->getEmail() == $currentUser->getUserIdentifier())
        {
            $user->setStatus(false);
            $entityManager->flush();

            $cache->delete("user.get/{$id}");

            if (count($userAppointments) > 0) {
                $cache->delete("appointment.get_all");
    
                foreach($userAppointments as $appointment) {
                    $cache->delete("appointment.get/{$appointment->getId()}");
                }
            }
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } else {
            return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
        }
    }
}
