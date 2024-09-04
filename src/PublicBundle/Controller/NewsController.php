<?php

namespace App\PublicBundle\Controller;

use App\CoreBundle\Service\NewsCategoriesService;
use App\CoreBundle\Service\NewsCommentsService;
use App\CoreBundle\Service\NewsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NewsController extends AbstractController
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly NewsCategoriesService $newsCategoriesService,
        private readonly NewsService $newsService,
        private readonly NewsCommentsService $newsCommentsService
    ) {}

    public function index(Request $request): Response
    {
        $newsCategories = $this->newsCategoriesService->getCategories();

        $page = $request->query->get('page', 1);
        $category = $request->query->get('category', null);

        $newsList = $this->newsService->getNewsByPage($page, 10, $category);

        return $this->render('bundles/PublicBundle/news.list.html.twig', [
            'newsCategories' => $newsCategories,
            'newsList' => $newsList,
        ]);
    }

    public function news(int $id): Response {
        $news = $this->newsService->getNewsById($id);
        return $this->render('bundles/PublicBundle/news.html.twig', [
            'news' => $news,
        ]);
    }

    public function newsAddComment(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $constraints = new Assert\Collection([
            'username' => new Assert\Optional([
                new Assert\NotBlank(['message' => 'Username is required!']),
                new Assert\Length(['max' => 255, 'maxMessage' => 'Username cannot exceed 255 characters!']),
            ]),
            'content' => [
                new Assert\NotBlank(['message' => 'Comment content is required!']),
                new Assert\Length(['max' => 1000, 'maxMessage' => 'Comment content cannot exceed 1000 characters!']),
            ],
        ]);

        $errors = $this->validator->validate($data, $constraints);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['status' => 'error', 'errors' => $errorMessages], 400);
        }

        $comment = $this->newsCommentsService->addComment($id, $data);
        if ($comment === null) {
            return new JsonResponse(['status' => 'error', 'message' => 'News not found'], 404);
        }

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Comment added successfully',
            'comment' => [
                'content' => $comment->getContent(),
                'username' => $comment->getUsername(),
                'createdAt' => $comment->getCreatedAt()->format('Y-m-d H:i')
            ]
        ], 200);
    }
}