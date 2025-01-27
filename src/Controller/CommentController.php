<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\UserRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/Comment')]
class CommentController extends AbstractController
{


    #[Route('/new/{userId}/{recipeId}', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $entityManager,  $userId, $recipeId, UserRepository $userRepo, RecipeRepository $recipeRepo)
    {
        $user = $userRepo ->findOneById($userId);
        $recipe = $recipeRepo->findOneById($recipeId);
        $like = new Comment();
        $like -> setRecipe($recipe);
        $like -> setUser($user);
        $entityManager->persist($like);
        $entityManager->flush();

        return $this -> redirectToRoute('app_recipe_show',[
            'id' => $recipe->getId()
        ]);
    }



    #[Route('/{recipeId}/{id}', name: 'app_comment_delete', methods: ['GET','POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager, $recipeId)
    {

        $entityManager->remove($comment);
        $entityManager->flush();

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin_dashboard', [], Response::HTTP_SEE_OTHER);
        }else{
            return $this->redirectToRoute('app_recipe_show', [
                 'id' => $recipeId
            ], Response::HTTP_SEE_OTHER);
        }
        
;
    }
}
