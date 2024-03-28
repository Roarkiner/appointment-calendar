<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/api/test', name: 'app_test', methods: ["GET"])]
    public function index(): Response
    {
        return new JsonResponse("IT WORKS!", Response::HTTP_OK);
    }
}
