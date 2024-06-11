<?php

namespace App\Tests\Repository;

use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class RecipeRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private RecipeRepository $recipeRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->recipeRepository = $this->entityManager->getRepository(Recipe::class);
    }

    public function testFindByUser(): void
    {
        // Create a user
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail('testuser1@example.com');
        $user->setPassword('hashedpassword'); // Assurez-vous que le mot de passe est haché

        // Persist the user
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Create a recipe associated with the user
        $recipe = new Recipe();
        $recipe->setName('Test Recipe');
        $recipe->setPic('test_image.jpg');
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUser($user);

        // Persist the recipe
        $this->entityManager->persist($recipe);
        $this->entityManager->flush();

        // Test the findByUser function
        $recipes = $this->recipeRepository->findByUser($user);

        // Assertions
        $this->assertCount(1, $recipes);
        $this->assertEquals('Test Recipe', $recipes[0]->getName());
        $this->assertEquals($user->getId(), $recipes[0]->getUser()->getId());

        
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();

    }
}
