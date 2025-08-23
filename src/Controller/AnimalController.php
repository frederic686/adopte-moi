<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Form\AnimalType;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/eleveur/animal')]
#[IsGranted('ROLE_ELEVEUR')]
final class AnimalController extends AbstractController
{
    #[Route(name: 'app_animal_index', methods: ['GET'])]
    public function index(AnimalRepository $animalRepository): Response
    {
        $me = $this->getUser();
        return $this->render('animal/index.html.twig', [
            // ✅ ne lister que mes animaux
            'animals' => $animalRepository->findBy(
                ['eleveur' => $me],
                ['datePublication' => 'DESC']
            ),
        ]);
    }

    #[Route('/new', name: 'app_animal_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $animal = new Animal();

        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ✅ Photo obligatoire (gérée via le FormType)
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                $uploadsDir = $this->getParameter('kernel.project_dir').'/public/uploads/animals';
                $fs = new Filesystem();
                if (!$fs->exists($uploadsDir)) {
                    $fs->mkdir($uploadsDir, 0775);
                }

                $original = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safe = $slugger->slug($original)->lower()->toString();
                $ext = $photoFile->guessExtension() ?: 'jpg';
                $filename = sprintf('%s-%s.%s', $safe, uniqid('', true), $ext);

                $photoFile->move($uploadsDir, $filename);
                $animal->setPhoto('/uploads/animals/'.$filename);
            }

            // ✅ Eleveur & date assignés automatiquement
            $animal->setEleveur($this->getUser());
            $animal->setDatePublication(new \DateTimeImmutable());

            $em->persist($animal);
            $em->flush();

            $this->addFlash('success', 'Nouvel animal publié ✅');
            return $this->redirectToRoute('app_animal_index');
        }

        return $this->render('animal/new.html.twig', [
            'animal' => $animal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_animal_show', methods: ['GET'])]
    public function show(Animal $animal): Response
    {
        // 🔒 un éleveur ne voit pas le détail des animaux des autres dans son espace
        if ($animal->getEleveur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('animal/show.html.twig', [
            'animal' => $animal,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_animal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Animal $animal, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        // 🔒 sécurité propriétaire
        if ($animal->getEleveur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ✅ Photo mise à jour si fournie
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                $uploadsDir = $this->getParameter('kernel.project_dir').'/public/uploads/animals';
                $fs = new Filesystem();
                if (!$fs->exists($uploadsDir)) {
                    $fs->mkdir($uploadsDir, 0775);
                }

                $original = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safe = $slugger->slug($original)->lower()->toString();
                $ext = $photoFile->guessExtension() ?: 'jpg';
                $filename = sprintf('%s-%s.%s', $safe, uniqid('', true), $ext);

                $photoFile->move($uploadsDir, $filename);
                $animal->setPhoto('/uploads/animals/'.$filename);
            }

            $em->flush();
            $this->addFlash('success', 'Annonce modifiée ✅');

            return $this->redirectToRoute('app_animal_index');
        }

        return $this->render('animal/edit.html.twig', [
            'animal' => $animal,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_animal_delete', methods: ['POST'])]
    public function delete(Request $request, Animal $animal, EntityManagerInterface $em): Response
    {
        // 🔒 sécurité propriétaire
        if ($animal->getEleveur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$animal->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($animal);
            $em->flush();
            $this->addFlash('info', 'Annonce supprimée ❌');
        }

        return $this->redirectToRoute('app_animal_index');
    }
}
