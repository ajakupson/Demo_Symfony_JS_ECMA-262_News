<?php

namespace App\CoreBundle\Repository;

use App\CoreBundle\Entity\NewsCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NewsCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsCategory::class);
    }

    public function findByTitle(string $title): ?NewsCategory
    {
        return $this->findOneBy(['title' => $title]);
    }
}
