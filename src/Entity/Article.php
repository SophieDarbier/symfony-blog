<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $author = null;

    #[ORM\OneToMany(targetEntity: Commentary::class, mappedBy: 'article', orphanRemoval: true)]
    private Collection $ArticleCommentary;

    public function __construct()
    {
        $this->ArticleCommentary = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, Commentary>
     */
    public function getArticleCommentary(): Collection
    {
        return $this->ArticleCommentary;
    }

    public function addArticleCommentary(Commentary $articleCommentary): static
    {
        if (!$this->ArticleCommentary->contains($articleCommentary)) {
            $this->ArticleCommentary->add($articleCommentary);
            $articleCommentary->setArticle($this);
        }

        return $this;
    }

    public function removeArticleCommentary(Commentary $articleCommentary): static
    {
        if ($this->ArticleCommentary->removeElement($articleCommentary)) {
            // set the owning side to null (unless already changed)
            if ($articleCommentary->getArticle() === $this) {
                $articleCommentary->setArticle(null);
            }
        }

        return $this;
    }
}
