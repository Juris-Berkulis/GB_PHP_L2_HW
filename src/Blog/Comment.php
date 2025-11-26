<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog;

class Comment
{

    private UUID $uuid;
    private User $user;
    private Post $post;
    private string $text;

    public function __construct(UUID $uuid, User $user, Post $post, string $text)
    {
        $this->uuid = $uuid;
        $this->user = $user;
        $this->post = $post;
        $this->text = $text;
    }

    public function __toString()
    {
        return "Комментарий от '$this->user' к статье '{$this->post->getTitle()}': $this->text";
    }

    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPost(): Post
    {
        return $this->post;
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
