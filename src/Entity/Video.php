<?php

namespace App\Entity;

class Video
{
    public function __construct(
        public string $id,
        public string $caption,
        public Author $author,
        public string $url
    )
    {
    }
}