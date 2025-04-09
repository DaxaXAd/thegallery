<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo; // to use sluggable from package gedmo/doctrine-extensions

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Constants for user roles
    public const ROLE_ADMIN = "ROLE_ADMIN";
    public const ROLE_USER = "ROLE_USER";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: "Email est requis.")]
    #[Assert\Email(message: "Format email invalide.")]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: "mot de passe est requis.")]
    private ?string $password = null;

    #[ORM\Column(length: 40, unique: true)]
    #[Assert\NotBlank(message: "nom utilisateur est requis.")]
    #[Assert\Length(min: 3, minMessage: "Le nom d'utilisateur doit contenir au moins {{ limit }} caractères.")]
    private ?string $username = null;

    #[Gedmo\Slug(fields: ['username'])]
    #[ORM\Column(length: 40, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(length: 255)]
    private ?string $profile_pic = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $location = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, Post> Collection of posts created by the user
     */
    #[ORM\OneToMany(targetEntity: Post::class, mappedBy: 'user', orphanRemoval: true, cascade: ['remove'])]
    private Collection $posts;

    /**
     * @var Collection<int, Comment> Collection of comments made by the user
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user', orphanRemoval: true, cascade: ['remove'])]
    private Collection $comments;

    /**
     * @var Collection<int, Image> Collection of images uploaded by the user
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'user', orphanRemoval: true, cascade: ['remove'])]
    private Collection $images;

    /**
     * @var Collection<int, Like> Collection of likes made by the user
     */
    #[ORM\OneToMany(targetEntity: Like::class, mappedBy: 'user', orphanRemoval: true, cascade: ['remove'])]
    private Collection $likes;

    /**
     * @var Collection<int, Contact> Collection of contacts associated with the user
     */
    #[ORM\OneToMany(targetEntity: Contact::class, mappedBy: 'user', orphanRemoval: true, cascade: ['remove'])] 
    private Collection $contacts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->roles = ['ROLE_USER']; // Default role
        $this->updated_at = new \DateTimeImmutable(); // Set the current date and time
        $this->images = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

    /**
     * Get the user ID
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the user's email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the user's email
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Get the user's roles
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles ?? []; // Ensure it's an array
        $roles[] = 'ROLE_USER'; // Always include ROLE_USER
        return array_unique($roles);
    }

    /**
     * Set the user's roles
     *
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the user's hashed password
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set the user's hashed password
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Clear sensitive data
     *
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Get the username
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Set the username
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Check if the username is valid
     */
    public function isUsernameValid(): bool
    {
        return $this->username !== null && strlen($this->username) >= 3;
    }

    /**
     * Get the slug
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set the slug
     */
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Get the user's bio
     */
    public function getBio(): ?string
    {
        return $this->bio;
    }

    /**
     * Set the user's bio
     */
    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get the user's profile picture
     */
    public function getProfilePic(): ?string
    {
        return $this->profile_pic;
    }

    /**
     * Set the user's profile picture
     */
    public function setProfilePic(string $profile_pic): static
    {
        $this->profile_pic = $profile_pic;

        return $this;
    }

    /**
     * Get the user's location
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * Set the user's location
     */
    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get the last update date
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * Set the last update date
     */
    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, Post> Get all posts created by the user
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    /**
     * Add a post to the user's collection
     */
    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setuser($this);
        }

        return $this;
    }

    /**
     * Remove a post from the user's collection
     */
    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getuser() === $this) {
                $post->setuser(null);
            }
        }

        return $this;
    }

    
    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setuser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getuser() === $this) {
                $comment->setuser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) { 
            $this->images->add($image); 
            $image->setuser($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getuser() === $this) {
                $image->setuser(null);
            }
        }

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
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): static
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setUser($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): static
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getUser() === $this) {
                $contact->setUser(null);
            }
        }

        return $this;
    }

}
