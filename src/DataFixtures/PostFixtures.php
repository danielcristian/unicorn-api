<?php

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
