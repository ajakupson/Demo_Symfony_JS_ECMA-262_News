<?php

namespace App\CoreBundle\Service;

use App\CoreBundle\Entity\News;
use App\CoreBundle\Repository\NewsCategoryRepository;
use App\CoreBundle\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

readonly class NewsService
{
    public function __construct(
        private NewsCategoryRepository $newsCategoryRepository,
        private NewsRepository $newsRepository,
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator,
        private PictureUploadService $pictureUploadService,
        private Environment $twig,
        private MailerInterface $mailer
    ) {}

    public function getAllNews(): array {
        return $this->newsRepository->findAll();
    }

    public function getNewsByPage(int $page, int $limit = 10, ?int $category = null): \Knp\Component\Pager\Pagination\PaginationInterface
    {
        $queryBuilder = $this->newsRepository
            ->createQueryBuilder('n')
            ->orderBy('n.insertDate', 'DESC');

        if ($category !== null) {
            $queryBuilder->join('n.categories', 'c')
                ->andWhere('c.id = :category')
                ->setParameter('category', $category);
        }

        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            $limit
        );
    }

    public function addNews(array $data): News {
        $news = new News();
        $news = $this->createNewsFromData($news, $data);

        $this->entityManager->persist($news);
        $this->entityManager->flush();

        return $news;
    }

    public function deleteNewsById(int $id): bool {
        $news = $this->newsRepository->find($id);

        if ($news !== null) {
            if ($news->getPicture()) {
                $this->pictureUploadService->deleteFile($news->getPicture());
            }

            $this->entityManager->remove($news);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    public function getNewsById(int $id): ?News {
        return $this->newsRepository->find($id);
    }

    public function updateNewsById(int $id, array $data): ?News {
        $news = $this->newsRepository->find($id);
        if ($news !== null) {
            $news = $this->createNewsFromData($news, $data);
            $this->entityManager->persist($news);
            $this->entityManager->flush();
        }

        return null;
    }

    private function createNewsFromData(News $news, array $data): News {
        $news->setTitle($data['title']);
        $news->setShortDescription($data['shortDescription']);
        $news->setContent($data['content']);
        $news->setInsertDate(new \DateTime($data['insertDate']));

        if (isset($data['picturePath'])) {
            $news->setPicture($data['picturePath']);
        }

        foreach ($news->getCategories() as $category) {
            $news->removeCategory($category);
        }

        if (!empty($data['categories'])) {
            foreach ($data['categories'] as $categoryId) {
                $category = $this->newsCategoryRepository->find($categoryId);
                if ($category) {
                    $news->addCategory($category);
                }
            }
        }

        return $news;
    }

    public function getTopNewsByComments(int $limit = 10): array {
        $queryBuilder = $this->newsRepository
            ->createQueryBuilder('n')
            ->leftJoin('n.comments', 'c')
            ->groupBy('n.id')
            ->orderBy('COUNT(c.id)', 'DESC')
            ->setMaxResults($limit);

        return $queryBuilder->getQuery()->getResult();
    }

    public function sendWeeklyNewsletter(): void {
        $topNews = $this->getTopNewsByComments();

        $content = $this->twig->render('bundles/AdminBundle//email.weekly.news.html.twig', [
            'topNews' => $topNews
        ]);

        $email = (new Email())
            ->from('noreply@localhost.com')
            ->to('ajakupson.job@gmail.com')
            ->subject('Weekly Top News')
            ->html($content);

        $this->mailer->send($email);
    }
}