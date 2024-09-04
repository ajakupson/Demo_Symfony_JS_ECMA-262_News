<?php

namespace App\AdminBundle\Controller;

use App\CoreBundle\Service\NewsCategoriesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NewsCategoriesController extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly NewsCategoriesService $newsCategoriesService
    ) {}

    public function index(): Response {
        $categories = $this->newsCategoriesService->getCategories();

        return $this->render('bundles/AdminBundle/news.categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    public function addCategory(Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $validationErrors = $this->handleValidationErrors($data);
        if ($validationErrors !== null) {
            return $validationErrors;
        }

        $newsCategory = $this->newsCategoriesService->addCategory($data);

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Category added successfully',
            'category' => $newsCategory->toArray()
        ], 200);
    }

    public function updateCategory(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $validationErrors = $this->handleValidationErrors($data);
        if ($validationErrors !== null) {
            return $validationErrors;
        }

        $newsCategory = $this->newsCategoriesService->updateCategoryById($id, $data);
        if ($newsCategory === null) {
            return new JsonResponse(['status' => 'error', 'message' => 'Category not found'], 404);
        }

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Category updated successfully',
            'category' => $newsCategory
        ], 200);
    }

    public function deleteCategory(int $id): JsonResponse
    {
        $isDeleted = $this->newsCategoriesService->deleteCategoryById($id);

        if ($isDeleted) {
            return new JsonResponse(['status' => 'success', 'message' => 'Category deleted successfully']);
        }

        return new JsonResponse(['status' => 'error', 'errors' => ['Category with given id could not be found']], 400);
    }

    private function validateNewsCategory(array $data): ConstraintViolationListInterface {
        $constraints = new Assert\Collection([
            'title' => [
                new Assert\NotBlank(['message' => 'Category title is required!']),
                new Assert\Length(['max' => 255, 'maxMessage' => 'Category name cannot exceed 255 characters!']),
            ]
        ]);

        return $this->validator->validate($data, $constraints);
    }

    private function handleValidationErrors(array $data): ?JsonResponse {
        $errors = $this->validateNewsCategory($data);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['status' => 'error', 'errors' => $errorMessages], 400);
        }

        return null;
    }
}