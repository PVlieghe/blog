<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    private $userRepository;
    private $entityManager;


    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/dashboard', name: 'app_admin_dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }


    #[Route('/up/{id}', name: 'app_admin_up')]
    public function up(User $user): Response
    {
        // Ajouter le rôle ROLE_ADMIN à l'utilisateur
        $roles = $user->getRoles();
        if (!in_array('ROLE_ADMIN', $roles)) {
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles($roles);

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/down/{id}', name: 'app_admin_down')]
    public function down(User $user): Response
    {
        // Retirer le rôle ROLE_ADMIN à l'utilisateur
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles)) {
            $roles = array_diff($roles, ['ROLE_ADMIN']);
            $user->setRoles($roles);

            // Sauvegarder les modifications dans la base de données
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        // Rediriger vers le tableau de bord admin
        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/show/{id}', name: 'app_admin_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        
        return $this->render('admin/show.html.twig', [
            'user' => $user,
        ]);
    }


}
