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
// use Symfony\Component\Uid\Uuid;

#[Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[Id]
    // Since DBAL 3.0, this does not work.
    //#[GeneratedValue(strategy: "UUID")//#[Column(type: "string", unique: true)]
    #[Column(unique: true)]
    // #[GeneratedValue(strategy: "CUSTOM")]
    // #[CustomIdGenerator(class: UuidGenerator::class)]
    private $id = null;

    #[Column(type: "string", length: 255)]
    private string $title;

    #[Column(type: "string", length: 255)]
    private string $title_loc;

    // #[Column(type: "string", enumType: Status::class)]
    // private Status $status;

    // #[Column(name: "created_at", type: "datetime", nullable: true)]
    // private DateTimeInterface|null $createdAt = null;

    // #[Column(name: "published_at", type: "datetime", nullable: true)]
    // private DateTimeInterface|null $publishedAt = null;

    // #[OneToMany(mappedBy: "post", targetEntity: Comment::class, cascade: ['persist', 'merge', "remove"], fetch: 'LAZY', orphanRemoval: true)]
    // private Collection $comments;

    // #[ManyToMany(targetEntity: Tag::class, mappedBy: "posts", cascade: ['persist', 'merge'], fetch: 'EAGER')]
    // private Collection $tags;

    public function __construct()
    {
        // $this->status = Status::Draft;
        // $this->createdAt = new DateTime();
        // $this->comments = new ArrayCollection();
        // $this->tags = new ArrayCollection();
    }


    public function getId()
    {
        return $this->id;
    }

    public function setId( $id): self
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

    // /**
    //  * @return Status
    //  */
    // public function getStatus(): Status
    // {
    //     return $this->status;
    // }

    // /**
    //  * @param Status $status
    //  * @return Post
    //  */
    // public function setStatus(Status $status): self
    // {
    //     $this->status = $status;
    //     return $this;
    // }


    // /**
    //  * @return DateTimeInterface
    //  */
    // public function getCreatedAt(): DateTimeInterface
    // {
    //     return $this->createdAt;
    // }

    // /**
    //  * @param DateTimeInterface|null $createdAt
    //  * @return Post
    //  */
    // public function setCreatedAt(?DateTimeInterface $createdAt): self
    // {
    //     $this->createdAt = $createdAt;
    //     return $this;
    // }

    // /**
    //  * @return DateTimeInterface|null
    //  */
    // public function getPublishedAt(): ?DateTimeInterface
    // {
    //     return $this->publishedAt;
    // }

    // /**
    //  * @param DateTimeInterface|null $publishedAt
    //  * @return Post
    //  */
    // public function setPublishedAt(?DateTimeInterface $publishedAt): self
    // {
    //     $this->publishedAt = $publishedAt;
    //     return $this;
    // }


    // /**
    //  * @return Collection
    //  */
    // public function getTags(): Collection
    // {
    //     return $this->tags;
    // }

    // public function addTag(Tag $tag): self
    // {
    //     if (!$this->tags->contains($tag)) {
    //         $this->tags[] = $tag;
    //         $tag->addPost($this);
    //     }

    //     return $this;
    // }

    // public function removeTag(Tag $tag): self
    // {
    //     if ($this->tags->removeElement($tag)) {
    //         $tag->removePost($this);
    //     }

    //     return $this;
    // }

    // public function getComments(): Collection
    // {
    //     return $this->comments;
    // }


    // public function addComment(Comment $comment): self
    // {
    //     if (!$this->comments->contains($comment)) {
    //         $this->comments[] = $comment;
    //         $comment->setPost($this);
    //     }

    //     return $this;
    // }

    // public function removeComment(Comment $comment): self
    // {
    //     if ($this->comments->removeElement($comment)) {
    //         $comment->setPost(null);
    //     }

    //     return $this;
    // }

    public function __toString(): string
    {
        return "Post: [ id =" . $this->getId()
            . ", title=" . $this->getTitle()
            . ", title_loc=" . $this->gettitle_loc()
            . "]";
    }

}
