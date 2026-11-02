<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Serves the starter landing page and the health endpoint.
 */
final class StarterController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('starter/index.html.twig', [
            'symfony_version' => Kernel::VERSION,
        ]);
    }

    /**
     * Answers without touching the database or other services, so it only
     * reports whether PHP-FPM and the application itself are up.
     */
    #[Route('/healthz', name: 'healthz', methods: ['GET', 'HEAD'])]
    public function healthz(): JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }
}
