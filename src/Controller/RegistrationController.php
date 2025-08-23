<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;

final class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $user = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 1) Mot de passe
            $plainPassword = (string) $form->get('plainPassword')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

           // 2) Rôle éleveur si coché
        if ($form->has('isEleveur') && $form->get('isEleveur')->getData()) {
            $user->setRoles(['ROLE_ELEVEUR']);
        } else {
            $user->setRoles(['ROLE_USER']);
        }

            // 3) Upload de la photo (si fournie)
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                $uploadsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/avatars';
                $fs = new Filesystem();
                if (!$fs->exists($uploadsDir)) {
                    $fs->mkdir($uploadsDir, 0775);
                }

                $originalName = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeName = $slugger->slug($originalName)->lower()->toString();
                $ext = $photoFile->guessExtension() ?: 'bin';
                $newFilename = sprintf('%s-%s.%s', $safeName, uniqid('', true), $ext);

                $photoFile->move($uploadsDir, $newFilename);

                // On stocke le chemin "web" (accessible depuis le navigateur)
                $user->setPhoto('/uploads/avatars/' . $newFilename);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Compte créé. Connecte-toi maintenant.');
            return $this->redirectToRoute('app_login');
        }
        if ($form->isSubmitted() && !$form->isValid()) {
        dd((string) $form->getErrors(true, false));
        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
