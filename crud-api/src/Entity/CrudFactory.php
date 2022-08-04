<?php

namespace App\Entity;

class CrudFactory
{
    public static function create(string $title, string $titleLoc): Crud
    {
        $post = new Crud();
        $post->setTitle($title);
        $post->setTitleLoc($titleLoc);
        return $post;
    }
}