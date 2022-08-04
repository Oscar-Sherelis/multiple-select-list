<?php

namespace App\Dto;

class CreatePostDto
{
    private string $title;
    private string $titleLoc;

    static function of(string $title, string $titleLoc): CreatePostDto
    {
        $dto = new CreatePostDto();
        $dto->setTitle($title)->setTitleLoc($titleLoc);
        return $dto;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $titleLoc
     */
    public
    function setTitleLoc(string $titleLoc): self
    {
        $this->titleLoc = $titleLoc;
        return $this;
    }

    /**
     * @return string
     */
    public
    function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public
    function getTitleLoc(): string
    {
        return $this->titleLoc;
    }


}