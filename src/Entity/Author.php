<?php

namespace App\Entity;

class Author
{
    public function __construct(
        public string $id,
        public string $handle
    )
    {
    }
}
