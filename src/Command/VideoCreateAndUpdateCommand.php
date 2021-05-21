<?php

namespace App\Command;

use App\Entity\Video;

class VideoCreateAndUpdateCommand
{
    
    public function execute(Video $entity, int $authorId): int
    {
        return 0;
    }
}