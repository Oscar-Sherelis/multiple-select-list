<?php

namespace App\Entity;

class PostFactory
{
    public static function create(string $title, string $title_loc): Post
    {
        $post = new Post();
        $post->setTitle($title);
        $post->setTitleLoc($title_loc);
        return $post;
    }
}