<?php

declare(strict_types=1);

namespace App\Controller\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class UserDto
{
    public function __construct(
        #[Assert\Length(max: 255)]
        #[Assert\NotBlank]
        #[Assert\Email]
        #[OA\Property(example: "your_email@example.com")]
        public readonly ?string $email,

        #[Assert\Length(max: 255)]
        #[OA\Property(example: "Jon Doe")]
        public readonly ?string $displayName,
    ) {
    }
}
