<?php

declare(strict_types=1);

namespace App\DataFixtures\Story;

use App\DataFixtures\Factory\UnicornFactory;
use Zenstruck\Foundry\Story;

final class UnicornStory extends Story
{
    public function build(): void
    {
        $this->loadData();
    }

    private function loadData(): void
    {
        UnicornFactory::createMany(10);
    }
}
