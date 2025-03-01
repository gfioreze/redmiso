<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    //#[IsGranted(User::ROLE_ADMIN)]
    public function index(): Response
    {
        //$this->denyAccessUnlessGranted('ROLE_USER');
        return $this->render('main/main.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}