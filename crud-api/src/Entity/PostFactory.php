<?php

namespace App\Entity;

class PostFactory
{
    public static function create(string $title, string $content): Post
    {
        $post = new Post();
        $post->setTitle($title);
        $post->setTitleLoc($content);
        return $post;
    }
}