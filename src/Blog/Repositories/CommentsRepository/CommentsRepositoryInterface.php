<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\CommentsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Comment;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;

interface CommentsRepositoryInterface
{

    public function save(Comment $comment): void;

    public function get(UUID $uuid): Comment;

}
