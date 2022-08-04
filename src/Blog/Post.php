<?php

namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Person\Person;

class Post
{
    private Person $author;
    private string $text;

    public function __construct(
        Person $author,
        string $text
    )
    {
        $this->text = $text;
        $this->author = $author;
    }

    public function __toString()
    {
        return $this->author . ' пишет: ' . $this->text;
    }
}
