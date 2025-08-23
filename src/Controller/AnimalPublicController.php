<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Friandise;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;

#[Route('/animal')]
final class AnimalPublicController extends AbstractController
{
    #[Route('', name: 'animal_public_index', methods: ['GET'])]
    public function index(AnimalRepository $repo): Response
    {
        $animaux = $repo->findBy([], ['datePublication' => 'DESC']);

        return $this->render('animal_public/index.html.twig', [
            'animaux' => $animaux,
        ]);
    }

    #[Route('/{id}', name: 'animal_public_show', methods: ['GET'])]
    public function show(Animal $animal): Response
    {
        return $this->render('animal_public/show.html.twig', [
            'animal' => $animal,
        ]);
    }

    #[Route('/{id}/friandise', name: 'app_envoyer_friandise', methods: ['POST'])]
    public function envoyerFriandise(Animal $animal, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // CSRF
        if (!$this->isCsrfTokenValid('friandise'.$animal->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisée (CSRF).');
            return $this->redirectToRoute('animal_public_show', ['id' => $animal->getId()]);
        }

        $user = $this->getUser();

        // Empêche d'envoyer une friandise à son propre animal (on garde la sécurité)
        if (method_exists($animal, 'getEleveur') && $animal->getEleveur() === $user) {
            $this->addFlash('error', 'Vous ne pouvez pas envoyer de friandise à votre propre animal.');
            return $this->redirectToRoute('animal_public_show', ['id' => $animal->getId()]);
        }

        // Anti-doublon : une friandise par (utilisateur, animal)
        $existe = $em->getRepository(Friandise::class)->findOneBy([
            'animal'   => $animal,
            'envoyeur' => $user,
        ]);
        if ($existe) {
            $this->addFlash('info', 'Cet animal est déjà dans vos favoris 🍬');
            return $this->redirectToRoute('animal_public_show', ['id' => $animal->getId()]);
        }

        // Type simple (plus de mapping par espèce)
        $type = '🍬 Friandise';

        $friandise = new Friandise();
        $friandise->setAnimal($animal);
        $friandise->setEnvoyeur($user);
        $friandise->setType($type);

        $em->persist($friandise);
        $em->flush();

        $this->addFlash('success', 'Vous avez ajouté '.$animal->getNom().' à vos favoris 🎁');
        return $this->redirectToRoute('animal_public_show', ['id' => $animal->getId()]);
    }
}
