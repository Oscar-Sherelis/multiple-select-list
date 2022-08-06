<?php

namespace App\Dto;

class UpdatePostDto
{
    private string $title;
    private string $title_loc;

    static function of(string $title, string $title_loc): UpdatePostDto
    {
        $dto = new UpdatePostDto();
        $dto->setTitle($title)->setTitleLoc($title_loc);
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
     * @param string $title_loc
     */
    public
    function setTitleLoc(string $title_loc): self
    {
        $this->title_loc = $title_loc;
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
        return $this->title_loc;
    }


}