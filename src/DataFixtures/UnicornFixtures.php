<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataFixtures\Story\UnicornStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UnicornFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UnicornStory::load();
    }
}
