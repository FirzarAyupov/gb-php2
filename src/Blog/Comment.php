<?php

namespace GeekBrains\LevelTwo\Blog;

class Comment
{
    private int $id;
    private ?int $user_id;
    private ?int $post_id;
    private string $text;

    public function __construct(
        int $user_id = null,
        int $post_id = null,
        string $text
    )
    {
        $this->text = $text;
        $this->$user_id = $user_id;
        $this->$post_id = $post_id;
    }

    public function __toString()
    {
        return $this->text;
    }
}
