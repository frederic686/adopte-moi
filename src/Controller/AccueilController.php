<?php

namespace App\Controller;

use App\Repository\AnimalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(AnimalRepository $animalRepository): Response
    {
        // Récupère seulement les 6 derniers animaux triés par date de publication (DESC)
        $animaux = $animalRepository->findBy([], ['datePublication' => 'DESC'], 6);

        return $this->render('accueil/index.html.twig', [
            'animaux' => $animaux,
        ]);
    }
}
