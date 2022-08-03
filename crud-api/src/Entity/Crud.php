<?php

namespace App\Entity;

use App\Repository\CrudRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CrudRepository::class)]
class Crud
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Column(type: "string", length: 255)]
    private ?string $title = "";

    #[Column(type: "string", length: 255)]
    private ?string $titleLoc = "";

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getTitleLoc(): ?string
    {
        return $this->titleLoc;
    }

    // Setters

    public function setTitle(sting $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setTitleLoc(string $titleLoc): self 
    {
        $this->titleLoc = $titleLoc;
        return $this;
    }

    // CRUD

    public function create(string $title, string $titleLoc)
    {
        $this->getTitle($title);
        $this->getTitleLoc($titleLoc);

        return $this;
    }
}
