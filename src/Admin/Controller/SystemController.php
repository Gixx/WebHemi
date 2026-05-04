<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/system', name: 'admin_system_')]
final class SystemController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('dashboard.view')]
    public function index(): Response
    {
        return $this->render('admin/system/index.html.twig');
    }
}
