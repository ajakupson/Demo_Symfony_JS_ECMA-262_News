<?php

namespace App\CoreBundle\Service;

use App\CoreBundle\Entity\NewsComment;
use App\CoreBundle\Repository\NewsCommentsRepository;
use App\CoreBundle\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;

class NewsCommentsService
{
    public function __construct(
        private NewsRepository $newsRepository,
        private NewsCommentsRepository $newsCommentsRepository,
        private EntityManagerInterface $entityManager,
    ) {}

    public function addComment(int $newsId, array $data): ?NewsComment {
        $news = $this->newsRepository->find($newsId);

        if ($news === null) {
            return null;
        }

        $comment = new NewsComment();
        $comment->setContent($data['content']);
        $comment->setUsername($data['username']);
        $comment->setCreatedAt(new \DateTime());
        $comment->setNews($news);

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return $comment;
    }

    public function deleteNewsCommentById(int $id): bool {
        $newsComment = $this->newsCommentsRepository->find($id);

        if ($newsComment !== null) {
            $this->entityManager->remove($newsComment);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}