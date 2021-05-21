<?php

namespace App\Command;

use App\Entity\Author;

class AuthorCreateAndUpdateCommand
{
    
    public function execute(Author $entity): int
    {
        return 0;
    }
}