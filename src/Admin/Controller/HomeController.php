<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'admin_home', methods: ['GET'], condition: "request.attributes.get('surface') == 'admin'")]
    public function __invoke(): RedirectResponse
    {
        return $this->redirectToRoute('admin_dashboard');
    }
}
