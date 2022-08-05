<?php

namespace App\Dto;

class CreateCommentDto
{
    private string $content;

    static function of( string $content): CommentWithPostSummaryDto
    {
        $dto = new CommentWithPostSummaryDto();
        $dto->setTitleLoc($content);
        return $dto;
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
    function getTitleLoc(): string
    {
        return $this->content;
    }


}