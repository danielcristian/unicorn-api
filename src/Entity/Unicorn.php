<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\UnicornStatusEnum;
use App\Repository\UnicornRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UnicornRepository::class)]
class Unicorn
{
    #[Groups(['post', 'unicorn'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['unicorn'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Groups(['unicorn'])]
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[Groups(['unicorn'])]
    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToOne]
    private ?User $buyer = null;

    #[Groups(['unicorn'])]
    #[ORM\Column(length: 20, enumType: UnicornStatusEnum::class)]
    private ?UnicornStatusEnum $status;

    #[ORM\OneToMany(mappedBy: 'unicorn', targetEntity: Post::class)]
    private Collection $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getBuyer(): ?User
    {
        return $this->buyer;
    }

    public function setBuyer(?User $buyer): static
    {
        $this->buyer = $buyer;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setUnicorn($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        // set the owning side to null (unless already changed)
        if ($this->posts->removeElement($post) && $post->getUnicorn() === $this) {
            $post->setUnicorn(null);
        }

        return $this;
    }

    public function getStatus(): ?UnicornStatusEnum
    {
        return $this->status;
    }

    public function setStatus(UnicornStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

}
