<?php

namespace App\CoreBundle\DTO;

class NewsCategoryDTO
{
    private string $title;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}