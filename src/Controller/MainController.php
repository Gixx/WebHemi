<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function homepage(): Response
    {
        return $this->render('main/homepage.html.twig', [
            'title' => 'Welcome to the Homepage',
        ]);
    }
}
