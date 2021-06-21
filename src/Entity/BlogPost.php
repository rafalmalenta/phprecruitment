<?php declare(strict_types=1);

namespace App\Entity;

use App\Repository\BlogPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BlogPostRepository::class)
 */
class BlogPost
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"main","post_info","comment_info"})
     */
    private int $id;

    /**
     * @Groups({"main","post_info"})
     * @ORM\Column(type="text")
     */
    private string $fullContent;

    /**
     * @Groups({"main","post_info"})
     * @ORM\Column(type="text")
     */
    private string $shortContent;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="post", orphanRemoval=true)
     * @Groups({"main","post_info"})
    */
    private Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullContent(): ?string
    {
        return $this->fullContent;
    }

    public function setFullContent(string $fullContent): self
    {
        $this->fullContent = $fullContent;

        return $this;
    }

    public function getShortContent(): ?string
    {
        return $this->shortContent;
    }

    public function setShortContent(string $shortContent): self
    {
        $this->shortContent = $shortContent;

        return $this;
    }

    /**
     * @return int
     * @Groups("main")
     */
    public function getCommentsCount(): int
    {
        return count($this->comments);
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
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }
}
