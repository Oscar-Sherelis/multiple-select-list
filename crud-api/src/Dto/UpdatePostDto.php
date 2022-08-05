<?php

namespace App\Dto;

class UpdatePostDto
{
    private string $title;
    private string $content;

    static function of(string $title, string $content): UpdatePostDto
    {
        $dto = new UpdatePostDto();
        $dto->setTitle($title)->setTitleLoc($content);
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
     * @param string $content
     */
    public
    function setTitleLoc(string $content): self
    {
        $this->content = $content;
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
        return $this->content;
    }


}