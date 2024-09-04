<?php

namespace App\AdminBundle\Controller;

use App\CoreBundle\Service\NewsCategoriesService;
use App\CoreBundle\Service\NewsCommentsService;
use App\CoreBundle\Service\NewsService;
use App\CoreBundle\Service\PictureUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NewsController extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly NewsCategoriesService $newsCategoriesService,
        private readonly NewsService $newsService,
        private readonly PictureUploadService $pictureUploadService,
        private readonly NewsCommentsService $newsCommentsService
    ) {}

    public function index(Request $request): Response
    {
        $newsCategories = $this->newsCategoriesService->getCategories();

        $page = $request->query->get('page', 1);
        $newsList = $this->newsService->getNewsByPage($page);

        return $this->render('bundles/AdminBundle/news.list.html.twig', [
            'newsCategories' => $newsCategories,
            'newsList' => $newsList,
        ]);
    }

    public function addNews(Request $request): JsonResponse {
        $data = $request->request->all();
        $file = $request->files->get('picture');

        $validationErrors = $this->handleNewsValidationErrors($data);
        if ($validationErrors !== null) {
            return $validationErrors;
        }

        if ($file !== null) {
            $newFilename = $this->pictureUploadService->uploadFile($file);
            if ($newFilename === null) {
                return new JsonResponse(['status' => 'error', 'message' => 'Failed to upload picture'], 500);
            }
            $data['picturePath'] = $newFilename;
        }

        $news = $this->newsService->addNews($data);

        return new JsonResponse([
            'status' => 'success',
            'message' => 'News added successfully',
            'news' => $news
        ], 200);
    }

    public function deleteNews(int $id): JsonResponse
    {
        $isDeleted = $this->newsService->deleteNewsById($id);

        if ($isDeleted) {
            return new JsonResponse(['status' => 'success', 'message' => 'News deleted successfully']);
        }

        return new JsonResponse(['status' => 'error', 'errors' => ['News with given id could not be found']], 400);
    }

    public function news(int $id): Response {
        $news = $this->newsService->getNewsById($id);
        $newsCategories = $this->newsCategoriesService->getCategories();

        return $this->render('bundles/AdminBundle/news.html.twig', [
            'newsCategories' => $newsCategories,
            'news' => $news
        ]);
    }

    public function updateNews(Request $request, int $id): JsonResponse {
        $data = $request->request->all();
        $file = $request->files->get('picture');

        $validationErrors = $this->handleNewsValidationErrors($data);
        if ($validationErrors !== null) {
            return $validationErrors;
        }

        if ($file !== null) {
            $newFilename = $this->pictureUploadService->uploadFile($file);
            if ($newFilename === null) {
                return new JsonResponse(['status' => 'error', 'message' => 'Failed to upload picture'], 500);
            }

            $data['picturePath'] = $newFilename;
        }

        $news = $this->newsService->updateNewsById($id, $data);

        return new JsonResponse([
            'status' => 'success',
            'message' => 'News updated successfully',
            'news' => $news
        ], 200);
    }

    private function validateNews(array $data): ConstraintViolationListInterface {
        $constraints = new Assert\Collection([
            'title' => [
                new Assert\NotBlank(['message' => 'News title is required!']),
                new Assert\Length(['max' => 255, 'maxMessage' => 'News title cannot exceed 255 characters!']),
            ],
            'shortDescription' => [
                new Assert\NotBlank(['message' => 'Short description is required!']),
            ],
            'content' => [
                new Assert\NotBlank(['message' => 'Content is required!']),
            ],
            'insertDate' => [
                new Assert\NotBlank(['message' => 'Insert date is required!']),
                new Assert\DateTime(['message' => 'Insert date must be a valid datetime!']),
            ],
            'categories' => [
                new Assert\Optional([
                    new Assert\Type(['type' => 'array', 'message' => 'Categories must be an array!']),
                ])
            ]
        ]);

        return $this->validator->validate($data, $constraints);
    }

    private function handleNewsValidationErrors(array $data): ?JsonResponse {
        $errors = $this->validateNews($data);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['status' => 'error', 'errors' => $errorMessages], 400);
        }

        return null;
    }

    public function deleteNewsComment(int $id): JsonResponse
    {
        $isDeleted = $this->newsCommentsService->deleteNewsCommentById($id);

        if ($isDeleted) {
            return new JsonResponse(['status' => 'success', 'message' => 'News comment deleted successfully']);
        }

        return new JsonResponse(['status' => 'error', 'errors' => ['News comment with given id could not be found']], 400);
    }
}