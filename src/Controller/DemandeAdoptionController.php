<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\DemandeAdoption;
use App\Repository\DemandeAdoptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ELEVEUR')]
final class DemandeAdoptionController extends AbstractController
{
    #[Route('/demandes', name: 'app_demandes', methods: ['GET'])]
    public function index(DemandeAdoptionRepository $repo): Response
    {
        $user = $this->getUser();

        // Toutes les demandes sur les animaux de l'éleveur connecté
        $demandes = $repo->createQueryBuilder('d')
            ->join('d.animal', 'a')->addSelect('a')
            ->join('d.utilisateur', 'u')->addSelect('u')
            ->andWhere('a.eleveur = :eleveur')
            ->setParameter('eleveur', $user)
            ->orderBy('d.date', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('demandes/index.html.twig', [
            'demandes' => $demandes,
        ]);
    }

    #[Route('/demandes/{id}/accepter', name: 'app_demandes_accept', methods: ['POST'])]
    public function accept(
        DemandeAdoption $demande,
        Request $request,
        EntityManagerInterface $em,
        DemandeAdoptionRepository $repo
    ): Response {
        // Sécurité propriétaire
        if ($demande->getAnimal()->getEleveur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('accept'.$demande->getId(), (string)$request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisée (CSRF).');
            return $this->redirectToRoute('app_demandes');
        }

        // Accepter la demande courante
        $demande->setStatut('acceptee');

        // Refuser automatiquement toutes les autres demandes en attente pour le même animal
        $autres = $repo->createQueryBuilder('x')
            ->andWhere('x.animal = :animal')
            ->andWhere('x != :courante')
            ->setParameter('animal', $demande->getAnimal())
            ->setParameter('courante', $demande)
            ->getQuery()
            ->getResult();

        foreach ($autres as $autre) {
            if (in_array($autre->getStatut(), ['en_attente', 'en attente', 'En attente'], true)) {
                $autre->setStatut('refusee');
            }
        }

        $em->flush();
        $this->addFlash('success', 'Demande acceptée ✅');

        return $this->redirectToRoute('app_demandes');
    }

    #[Route('/demandes/{id}/refuser', name: 'app_demandes_refuse', methods: ['POST'])]
    public function refuse(DemandeAdoption $demande, Request $request, EntityManagerInterface $em): Response
    {
        if ($demande->getAnimal()->getEleveur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('refuse'.$demande->getId(), (string)$request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisée (CSRF).');
            return $this->redirectToRoute('app_demandes');
        }

        $demande->setStatut('refusee');
        $em->flush();

        $this->addFlash('info', 'Demande refusée ❌');
        return $this->redirectToRoute('app_demandes');
    }
}
