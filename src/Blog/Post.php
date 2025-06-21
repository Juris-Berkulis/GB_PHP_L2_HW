<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog;

use JurisBerkulis\GbPhpL2Hw\Person\Person;

class Post
{
    private int $id;
    private Person $author;
    private string $text;

    public function __construct(
        int $id,
        Person $author,
        string $text
    )
    {
        $this->id = $id;
        $this->author = $author;
        $this->text = $text;
    }

    public function __toString()
    {
        return $this->author . ' пишет: ' . $this->text  . PHP_EOL;
    }
}