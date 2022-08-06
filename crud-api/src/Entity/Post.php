<?php

namespace App\Entity;

use App\Repository\PostRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[Id]
    // Since DBAL 3.0, this does not work.
    //#[GeneratedValue(strategy: "UUID")//#[Column(type: "string", unique: true)]
    #[Column(type: "integer", unique: true)]
    #[GeneratedValue(strategy: "AUTO")]
    private $id = null;

    #[Column(type: "string", length: 255)]
    private string $title;

    #[Column(type: "string", length: 255)]
    private string $title_loc;


    public function getId()
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
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
     * @return string
     */
    public function getTitleLoc(): string
    {
        return $this->title_loc;
    }

    /**
     * @param string $title_loc
     */
    public function setTitleLoc(string $title_loc): self
    {
        $this->title_loc = $title_loc;
        return $this;
    }

    public function __toString(): string
    {
        return "Post: [ id =" . $this->getId()
            . ", title=" . $this->getTitle()
            . ", title_loc=" . $this->gettitle_loc()
            . "]";
    }

}
