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

    #[ORM\Column(length: 255, nullable: true)]
    
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToOne(inversedBy: 'post', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Image $img = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post', orphanRemoval: true)]
    private Collection $id_comment;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $title = null;

    /**
     * @var Collection<int, Like>
     */
    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'post', orphanRemoval: true)]
    private Collection $likes;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'posts')]
    private Collection $tags;

    

    public function __construct()
    {
        $this->id_comment = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter pour title
    
    

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getimg(): ?Image
    {
        return $this->img;
    }

    public function setimg(Image $img): static
    {
        $this->img = $img;

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

    public function getuser(): ?User
    {
        return $this->user;
    }

    public function setuser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setPost($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getPost() === $this) {
                $like->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    
}
