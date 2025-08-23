<?php

namespace App\Controller;

use App\Repository\AnimalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RechercheController extends AbstractController
{
    #[Route('/recherche', name: 'app_recherche')]
    public function index(Request $request, AnimalRepository $repo): Response
    {
        $q    = $request->query->get('q');       // mot-clé
        $type = $request->query->get('type');    // chien|chat|oiseau|lapin

        $qb = $repo->createQueryBuilder('a')
            ->join('a.eleveur', 'e')
            ->leftJoin('a.type', 't'); // ⬅️ JOINT le type

        if ($q) {
            $qb->andWhere('a.nom LIKE :q OR a.race LIKE :q OR e.ville LIKE :q OR t.categorie LIKE :q')
               ->setParameter('q', '%'.$q.'%');
        }

        if ($type) {
            $qb->andWhere('LOWER(t.categorie) = :type')
               ->setParameter('type', strtolower($type));
        }

        $animaux = $qb->orderBy('a.datePublication', 'DESC')
                      ->getQuery()->getResult();

        return $this->render('recherche/index.html.twig', [
            'animaux' => $animaux,
            'q' => $q,
            'type' => $type,
        ]);
    }
}
