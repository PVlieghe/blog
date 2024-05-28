<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Step;
use App\Entity\Recipe;
use App\Form\LikeType;
use App\Entity\Comment;
use App\Form\RecipeType;
use App\Form\SearchType;
use App\Form\CommentType;
use App\Form\FavoriteType;
use App\Repository\LikeRepository;
use App\Repository\RecipeRepository;
use App\Repository\CommentRepository;
use App\Repository\FavoriteRepository;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/recipe')]
#[IsGranted('ROLE_USER')]
class RecipeController extends AbstractController
{
    #[Route('/', name: 'app_recipe_index', methods: ['GET', 'POST'])]
    public function index(RecipeRepository $recipeRepository, IngredientRepository $ingRepo, Request $request): Response
    {
        $searchForm = $this->createForm(SearchType::class);
        $searchForm->handleRequest($request);
        
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $foods = $searchForm->getData()['criterias'];
            
            if (empty($foods)) {
                // Si aucun critère n'est sélectionné, retournez toutes les recettes
                $recipes = $recipeRepository->findAll();
            } else {
                // Si des critères sont sélectionnés, filtrer les recettes par ces critères
                $recipes = $recipeRepository->findAll();
                $recipesWithAllIngredients = [];
        
                // Extraire les identifiants des foods sélectionnés
                $foodIds = array_map(function($food) {
                    return $food->getId();
                }, $foods);
        
                foreach ($recipes as $recipe) {
                    $ingredients = $recipe->getIngredients()->toArray(); // Convertir la collection en tableau
                    $ingredientFoods = array_map(function($ingredient) {
                        return $ingredient->getFood()->getId();
                    }, $ingredients);
        
                    // Vérifiez si tous les aliments sélectionnés sont dans les ingrédients de la recette
                    if (count(array_intersect($foodIds, $ingredientFoods)) === count($foodIds)) {
                        $recipesWithAllIngredients[] = $recipe;
                    }
                }
        
                $recipes = $recipesWithAllIngredients;
            }
        } else {
            // Si le formulaire n'est pas soumis ou invalide, retourner toutes les recettes
            $recipes = $recipeRepository->findAll();
        }
        $averageRatings = [];
        if($recipes != []){ 
            foreach ($recipes as $recipe) {
                $comments = $recipe->getComments();
                $totalRating = 0;
                $numberOfRatings = count($comments);
                if ($numberOfRatings > 0) {
                    foreach ($comments as $comment) {
                        $totalRating += $comment->getRate();
                    }
                    $averageRating = $totalRating / $numberOfRatings;
                    // Arrondir le résultat à deux chiffres après la virgule si nécessaire
                        $averageRating = round($averageRating, 2);

                } else {
                    $averageRating = null;
                }
                $averageRatings[$recipe->getId()] = $averageRating;
            }
    }
        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
            'averageRatings' => $averageRatings,
            'form' => $searchForm
        ]);
    }

    #[Route('/new', name: 'app_recipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser();
            if ($user) {
                $recipe->setUser($user);
                $stepsData = $form ->getData()->getSteps();
                $ingredientsData = $form ->getData()->getIngredients();
                foreach($stepsData as $steps) {
                    $steps->setRecipe($recipe);
                }
                foreach($ingredientsData as $ingredients){
                    $ingredients ->setRecipe($recipe);
                }
                
                $file = $form['pic']->getData();
                if ($file){
                    $fileName = uniqid().'.'.$file->guessExtension();
                    $file->move(
                        $this->getParameter('upload_directory'),
                        $fileName
                    );
                    $recipe ->setPic($fileName);
                }
                $entityManager->persist($recipe);
                $entityManager->flush();
            }
            return $this->redirectToRoute('app_recipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recipe/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recipe_show', methods: ['GET', 'POST'])]
    public function show(EntityManagerInterface $entityManager, Recipe $recipe, Request $request): Response
    {
        $user = $this -> getUser();
        $like = null;
        foreach($recipe->getLikes() as $l){
            if($l->getUser() == $user){
                $like = $l;
            }
        }

        $favorite = null;
        foreach($recipe->getFavorites() as $favo){
            if($favo->getUser() == $user){
                $favorite = $favo;
            }
        }
        $userComment = false;
        foreach($recipe->getComments() as $comment){
            if($comment->getUser() == $user){
                $userComment = true;
            }
        }

        $ingredients = $recipe ->getIngredients();   

        $formLike = $this -> createForm(LikeType::class);
        $formLike -> handleRequest($request);
        

        $formFavo = $this -> createForm(FavoriteType::class);
        $formFavo ->handleRequest($request);

        $formComment = $this -> createForm(CommentType::class);
        $formComment -> handleRequest($request);

        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setRecipe($recipe); // Associer l'article au commentaire
            $comment->setUser($user); // Associer l'article au commentaire
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this -> redirectToRoute('app_recipe_show',[
                'id' => $recipe->getId()
            ]);
        }
        
        if ($formLike -> isSubmitted() && $formLike -> isValid()){
            
            if($like){
                return $this -> redirectToRoute('app_like_delete',[
                    'id' => $like ->getId(),
                    'recipeId' => $recipe->getId()
                ]);
            }
            else {
                return $this -> redirectToRoute('app_like_new',[
                    'userId'  => $user->getId(),
                    'recipeId' => $recipe->getId()
                ]);
            } 
        }

        if ($formFavo -> isSubmitted() && $formFavo -> isValid()){
           
            if($favorite){
                return $this -> redirectToRoute('app_favorite_delete',[
                    'id' => $favorite ->getId(),
                    'recipeId' => $recipe->getId()
                ]);
            }
            else {
                return $this -> redirectToRoute('app_favorite_new',[
                    'userId'  => $user->getId(),
                    'recipeId' => $recipe->getId()
                ]);
            } 
        }

        $steps = $recipe->getSteps()->toArray();

        // Triez les étapes par le champ "number"
        usort($steps, function($a, $b) {
            return $a->getNumber() - $b->getNumber();
        });

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'steps' => $steps,
            'formLike' => $formLike,
            'like' => $like,
            'formFavo' => $formFavo,
            'favorite' => $favorite,
            'formComment' => $formComment,
            'comment' => $userComment,
            'comments' => $recipe -> getComments(),
            'ingredients' => $recipe ->getIngredients()
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
         // Vérifier si l'utilisateur connecté n'est pas l'administrateur
         if (!$this->isGranted('ROLE_ADMIN')) {
            // Vérifier si l'utilisateur connecté est le propriétaire de la recette
            if ($this->getUser() !== $recipe->getUser()) {
                // Rediriger l'utilisateur vers une page d'erreur ou une autre page appropriée
                return $this->redirectToRoute('app_recipe_index', [], Response::HTTP_SEE_OTHER);
            }
        }
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_recipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recipe_delete')]
    public function delete(Request $request, Recipe $recipe, EntityManagerInterface $entityManager): Response
    {

            $entityManager->remove($recipe);
            $entityManager->flush();

        return $this->redirectToRoute('app_recipe_index', [], Response::HTTP_SEE_OTHER);
    }
}
