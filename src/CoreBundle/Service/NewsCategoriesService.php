<?php

namespace App\CoreBundle\Service;

use App\CoreBundle\Entity\NewsCategory;
use App\CoreBundle\Repository\NewsCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class NewsCategoriesService
{
    public function __construct(
        private NewsCategoryRepository $newsCategoryRepository,
        private EntityManagerInterface $entityManager,
    ) {}

    public function getCategories(): array {
        return $this->newsCategoryRepository->findAll();
    }

    public function addCategory(array $data): NewsCategory {
        $newsCategory = new NewsCategory();
        $newsCategory->setTitle($data['title']);

        $this->entityManager->persist($newsCategory);
        $this->entityManager->flush();

        return $newsCategory;
    }

    public function deleteCategoryById(int $id): bool {
        $newsCategory = $this->newsCategoryRepository->find($id);

        if ($newsCategory !== null) {
            $this->entityManager->remove($newsCategory);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }

    public function updateCategoryById(int $id, array $data): ?NewsCategory {
        $newsCategory = $this->newsCategoryRepository->find($id);

        if ($newsCategory !== null) {
            $newsCategory->setTitle($data['title']);
            $this->entityManager->persist($newsCategory);
            $this->entityManager->flush();

            return $newsCategory;
        }

        return null;
    }
}