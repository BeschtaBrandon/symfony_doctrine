<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 */
class Question
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $text;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $askedAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $votes = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $askedBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getAskedAt(): ?\DateTimeInterface
    {
        return $this->askedAt;
    }

    public function setAskedAt(?\DateTimeInterface $askedAt): self
    {
        $this->askedAt = $askedAt;

        return $this;
    }

    public function getVotes(): ?int
    {
        return $this->votes;
    }

    public function getVotesString(): string
    {
        $prefix = $this->getVotes() >= 0 ? '+' : '-';
        return sprintf('%s %d', $prefix, abs($this->getVotes()));
    }

    public function setVotes(int $votes): self
    {
        $this->votes = $votes;

        return $this;
    }

    public function getAskedBy(): ?string
    {
        return $this->askedBy;
    }

    public function getAskedByString(): string
    {
        if ($this->askedBy !== null) {
            return $this->askedBy;
        }
        return 'Anonymous';
    }

    public function setAskedBy(?string $askedBy): self
    {
        $this->askedBy = $askedBy;

        return $this;
    }
}
