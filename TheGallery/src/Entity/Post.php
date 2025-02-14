<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToOne(inversedBy: 'post', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Image $id_img = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post', orphanRemoval: true)]
    private Collection $id_comment;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $id_user = null;

    public function __construct()
    {
        $this->id_comment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdImg(): ?Image
    {
        return $this->id_img;
    }

    public function setIdImg(Image $id_img): static
    {
        $this->id_img = $id_img;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getIdComment(): Collection
    {
        return $this->id_comment;
    }

    public function addIdComment(Comment $idComment): static
    {
        if (!$this->id_comment->contains($idComment)) {
            $this->id_comment->add($idComment);
            $idComment->setPost($this);
        }

        return $this;
    }

    public function removeIdComment(Comment $idComment): static
    {
        if ($this->id_comment->removeElement($idComment)) {
            // set the owning side to null (unless already changed)
            if ($idComment->getPost() === $this) {
                $idComment->setPost(null);
            }
        }

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->id_user;
    }

    public function setIdUser(?User $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }
}
