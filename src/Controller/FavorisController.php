<?php
declare(strict_types=1);

namespace App\Controller;

use App\Repository\AnimalRepository;
use App\Repository\FriandiseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/favoris')]
final class FavorisController extends AbstractController
{
    #[Route('', name: 'app_favoris', methods: ['GET'])]
    public function index(FriandiseRepository $friandiseRepo, AnimalRepository $animalRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $ids = $friandiseRepo->findDistinctAnimalIdsForUser($this->getUser());

        $animaux = [];
        if (!empty($ids)) {
            $animaux = $animalRepo->createQueryBuilder('a')
                ->andWhere('a.id IN (:ids)')
                ->setParameter('ids', $ids)
                ->orderBy('a.datePublication', 'DESC')
                ->getQuery()
                ->getResult();
        }

        return $this->render('favoris/index.html.twig', [
            'animaux' => $animaux,
        ]);
    }
}
