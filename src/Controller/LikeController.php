<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\User;
use App\Entity\Recipe;
use App\Form\Like1Type;
use App\Repository\LikeRepository;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/like')]
class LikeController extends AbstractController
{


    #[Route('/new/{userId}/{recipeId}', name: 'app_like_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager,  $userId, $recipeId, UserRepository $userRepo, RecipeRepository $recipeRepo)
    {
        $user = $userRepo ->findOneById($userId);
        $recipe = $recipeRepo->findOneById($recipeId);
        $like = new Like();
        $like -> setRecipe($recipe);
        $like -> setUser($user);
        $entityManager->persist($like);
        $entityManager->flush();

        return $this -> redirectToRoute('app_recipe_show',[
            'id' => $recipe->getId()
        ]);
    }



    #[Route('/{recipeId}/{id}', name: 'app_like_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Like $like, EntityManagerInterface $entityManager, $recipeId)
    {

        $entityManager->remove($like);
        $entityManager->flush();
        
        return $this -> redirectToRoute('app_recipe_show',[
            'id' => $recipeId
        ]);
    }
}
