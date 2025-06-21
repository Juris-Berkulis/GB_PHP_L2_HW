<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog;

class Post
{
    private int $id;
    private User $author;
    private string $title;
    private string $text;

    public function __construct(
        int $id,
        User $author,
        string $title,
        string $text
    )
    {
        $this->id = $id;
        $this->author = $author;
        $this->title = $title;
        $this->text = $text;
    }

    public function __toString()
    {
        return "Статья '$this->title' автора $this->author: $this->text";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
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