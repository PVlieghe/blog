<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/favorite')]
class FavoriteController extends AbstractController
{


    #[Route('/new/{userId}/{recipeId}', name: 'app_favorite_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager,  $userId, $recipeId, UserRepository $userRepo, RecipeRepository $recipeRepo)
    {
        $user = $userRepo ->findOneById($userId);
        $recipe = $recipeRepo->findOneById($recipeId);
        $like = new Favorite();
        $like -> setRecipe($recipe);
        $like -> setUser($user);
        $entityManager->persist($like);
        $entityManager->flush();

        return $this -> redirectToRoute('app_recipe_show',[
            'id' => $recipe->getId()
        ]);
    }



    #[Route('/{recipeId}/{id}', name: 'app_favorite_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Favorite $favorite, EntityManagerInterface $entityManager, $recipeId)
    {

        $entityManager->remove($favorite);
        $entityManager->flush();
        
        return $this -> redirectToRoute('app_recipe_show',[
            'id' => $recipeId
        ]);
    }
}
