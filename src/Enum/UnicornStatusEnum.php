<?php

declare(strict_types=1);

namespace App\Enum;

enum UnicornStatusEnum: int
{
    case PUBLISHED = 1;
    case DRAFT = 2;
    case PURCHASED = 3;
}