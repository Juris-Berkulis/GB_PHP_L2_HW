<?php

namespace JurisBerkulis\GbPhpL2Hw\Blog\Repositories\PostsRepository;

use JurisBerkulis\GbPhpL2Hw\Blog\Post;
use JurisBerkulis\GbPhpL2Hw\Blog\UUID;

interface PostsRepositoryInterface
{

    public function save(Post $post): void;

    public function get(UUID $uuid): Post;

    public function delete(UUID $uuid): void;

}
