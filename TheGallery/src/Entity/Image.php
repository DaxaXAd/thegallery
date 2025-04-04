<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: false)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Tag $id_tag = null;

    #[ORM\OneToOne(mappedBy: 'img', cascade: ['persist', 'remove'])]
    private ?Post $post = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(name: "user", nullable: false)]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $title;
        $this->title = $title;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getIdTag(): ?Tag
    {
        return $this->id_tag;
    }

    public function setIdTag(?Tag $id_tag): static
    {
        $this->id_tag = $id_tag;

        return $this;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(Post $post): static
    {
        // set the owning side of the relation if necessary
        if ($post->getimg() !== $this) {
            $post->setimg($this);
        }

        $this->post = $post;

        return $this;
    }

    public function getuser(): ?User
    {
        return $this->user;
    }

    public function setuser(?User $user): static
    {
        $this->$user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }
}
