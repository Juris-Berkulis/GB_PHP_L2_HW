<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog;

class Comment
{

    private int $id;
    private User $user;
    private Post $post;
    private string $text;

    public function __construct(int $id, User $user, Post $post, string $text)
    {
        $this->id = $id;
        $this->user = $user;
        $this->post = $post;
        $this->text = $text;
    }

    public function __toString()
    {
        return "Комментарий от $this->user к статье '{$this->post->getTitle()}': $this->text";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): void
    {
        $this->post = $post;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

}