<?php

declare(strict_types=1);

namespace App\Site\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'site_home', methods: ['GET'], condition: "request.attributes.get('surface') == 'site'")]
    public function __invoke(Request $request): JsonResponse
    {
        return $this->json([
            'area' => 'site',
            'site_id' => (int) $request->attributes->get('site_id'),
            'host' => (string) $request->attributes->get('resolved_host'),
        ]);
    }
}
