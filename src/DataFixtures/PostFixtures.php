<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\DataFixtures\Story\PostStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        PostStory::load();
    }
}
