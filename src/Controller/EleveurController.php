<?php

namespace App\Controller;

use App\Repository\AnimalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EleveurController extends AbstractController
{
    #[Route('/eleveur', name: 'app_eleveur')]
    public function index(AnimalRepository $animalRepository): Response
    {
        $user = $this->getUser();

        // Vérifie si l’utilisateur est bien connecté et éleveur
        if (!$user || !in_array('ROLE_ELEVEUR', $user->getRoles())) {
            throw $this->createAccessDeniedException("Accès réservé aux éleveurs.");
        }

        // On récupère les animaux publiés par cet éleveur
        $animaux = $animalRepository->findBy(['eleveur' => $user]);

        return $this->render('eleveur/index.html.twig', [
            'user' => $user,
            'animaux' => $animaux,
        ]);
    }
}
