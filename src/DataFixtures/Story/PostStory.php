<?php

declare(strict_types=1);

namespace App\DataFixtures\Story;

use App\DataFixtures\Factory\PostFactory;
use Zenstruck\Foundry\Story;

final class PostStory extends Story
{
    public function build(): void
    {
        $this->loadData();
    }

    private function loadData(): void
    {
        PostFactory::createMany(10);
    }
}
