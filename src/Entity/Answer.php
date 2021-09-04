<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnswerRepository::class)
 */
class Answer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="integer")
     */
    private $vote;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $posted_at;

    /**
     * @ORM\ManyToOne(targetEntity=Question::class, inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $question;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="answer")
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=UserVoteAnswer::class, mappedBy="answer", cascade={"persist", "remove"})
     */
    private $userVoteAnswer;

    /**
     * @ORM\OneToMany(targetEntity=UserVoteAnswer::class, mappedBy="answer")
     */
    private $userVoteAnswers;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->userVoteAnswers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getVote(): ?int
    {
        return $this->vote;
    }

    public function setVote(int $vote): self
    {
        $this->vote = $vote;

        return $this;
    }

    public function getPostedAt(): ?\DateTimeImmutable
    {
        return $this->posted_at;
    }

    public function setPostedAt(\DateTimeImmutable $posted_at): self
    {
        $this->posted_at = $posted_at;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAnswer($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAnswer() === $this) {
                $comment->setAnswer(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUserVoteAnswer(): ?UserVoteAnswer
    {
        return $this->userVoteAnswer;
    }

    public function setUserVoteAnswer(UserVoteAnswer $userVoteAnswer): self
    {
        // set the owning side of the relation if necessary
        if ($userVoteAnswer->getAnswer() !== $this) {
            $userVoteAnswer->setAnswer($this);
        }

        $this->userVoteAnswer = $userVoteAnswer;

        return $this;
    }

    /**
     * @return Collection|UserVoteAnswer[]
     */
    public function getUserVoteAnswers(): Collection
    {
        return $this->userVoteAnswers;
    }

    public function addUserVoteAnswer(UserVoteAnswer $userVoteAnswer): self
    {
        if (!$this->userVoteAnswers->contains($userVoteAnswer)) {
            $this->userVoteAnswers[] = $userVoteAnswer;
            $userVoteAnswer->setAnswer($this);
        }

        return $this;
    }

    public function removeUserVoteAnswer(UserVoteAnswer $userVoteAnswer): self
    {
        if ($this->userVoteAnswers->removeElement($userVoteAnswer)) {
            // set the owning side to null (unless already changed)
            if ($userVoteAnswer->getAnswer() === $this) {
                $userVoteAnswer->setAnswer(null);
            }
        }

        return $this;
    }
}
