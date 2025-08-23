<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\DemandeAdoption;
use App\Form\DemandeAdoptionType;
use App\Repository\DemandeAdoptionRepository; // ⬅️ IMPORT MANQUANT
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class DemandeAdoptionUserController extends AbstractController
{
    #[Route('/animal/{id}/demande-adoption', name: 'app_demande_adoption_new', methods: ['GET','POST'])]
    public function new(Animal $animal, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Empêcher l’éleveur de demander son propre animal
        if (method_exists($animal, 'getEleveur') && $animal->getEleveur() === $user) {
            $this->addFlash('error', 'Vous ne pouvez pas adopter votre propre animal.');
            return $this->redirectToRoute('animal_public_show', ['id' => $animal->getId()]);
        }

        // Empêcher les doublons
        $existe = $em->getRepository(DemandeAdoption::class)->findOneBy([
            'animal' => $animal,
            'utilisateur' => $user,
        ]);
        if ($existe) {
            $this->addFlash('warning', 'Vous avez déjà fait une demande pour cet animal.');
            return $this->redirectToRoute('animal_public_show', ['id' => $animal->getId()]);
        }

        $demande = new DemandeAdoption();
        $demande->setAnimal($animal);
        $demande->setUtilisateur($user);

        $form = $this->createForm(DemandeAdoptionType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $demande->setDate(new \DateTimeImmutable());
            $demande->setStatut('en_attente');

            $em->persist($demande);
            $em->flush();

            $this->addFlash('success', 'Votre demande a été envoyée ✅');
            return $this->redirectToRoute('animal_public_show', ['id' => $animal->getId()]);
        }

        return $this->render('demande_adoption_user/index.html.twig', [
            'animal' => $animal,
            'form'   => $form->createView(),
        ]);
    }

    #[Route('/mes-demandes', name: 'app_mes_demandes', methods: ['GET'])]
    public function index(DemandeAdoptionRepository $repo): Response
    {
        $user = $this->getUser();

        // Les demandes faites par l’utilisateur courant
        $demandes = $repo->createQueryBuilder('d')
            ->join('d.animal', 'a')->addSelect('a')
            ->join('a.eleveur', 'e')->addSelect('e')
            ->andWhere('d.utilisateur = :u')
            ->setParameter('u', $user)
            ->orderBy('d.date', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('demande_adoption_user/mes_demandes.html.twig', [
            'demandes' => $demandes,
        ]);
    }

    #[Route('/mes-demandes/{id}/annuler', name: 'app_mes_demandes_cancel', methods: ['POST'])]
    public function cancel(DemandeAdoption $demande, Request $request, EntityManagerInterface $em): Response
    {
        // sécurité : seule la personne qui a FAIT la demande peut l’annuler
        if ($demande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('cancel'.$demande->getId(), (string)$request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisée (CSRF).');
            return $this->redirectToRoute('app_mes_demandes');
        }

        // On n’annule que si elle est encore en attente
        if (in_array($demande->getStatut(), ['en_attente', 'en attente', 'En attente'], true)) {
            $demande->setStatut('annulee');
            $em->flush();
            $this->addFlash('info', 'Demande annulée.');
        }

        return $this->redirectToRoute('app_mes_demandes');
    }
}
