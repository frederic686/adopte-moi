<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfilAnimalController extends AbstractController
{
    #[Route('/profil/animal', name: 'app_profil_animal')]
    public function index(): Response
    {
        return $this->render('profil_animal/index.html.twig', [
            'controller_name' => 'ProfilAnimalController',
        ]);
    }
}
