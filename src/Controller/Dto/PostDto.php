<?php

declare(strict_types=1);

namespace App\Controller\Dto;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class PostDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 10, max: 255, groups: ['post', 'post:new'])]
        #[OA\Property(example: "Add your message")]
        public readonly ?string $message,

        #[Assert\NotBlank(groups: ['post:new'])]
        #[OA\Property(example: "1")]
        public readonly ?int $unicornId,
    ) {
    }
}
