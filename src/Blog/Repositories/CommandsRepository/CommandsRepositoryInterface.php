<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\CommandsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Comment;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;

interface CommandsRepositoryInterface
{

    public function save(Comment $comment): void;

    public function get(UUID $uuid): Comment;

}
