<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Dto\UserDto;
use App\Entity\Unicorn;
use App\Entity\User;
use App\Enum\UnicornStatusEnum;
use App\Repository\PostRepository;
use App\Repository\UnicornRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

#[Route(path: 'unicorns')]
class UnicornController extends AbstractController
{
    #[OA\Tag('unicorns')]
    #[OA\Response(
        response: 200,
        description: 'Returns all unicorns',
        content: new Model(type: Unicorn::class)
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'No unicorns found in the farm',
        content: new JsonContent(example: [
            'statusCode' => Response::HTTP_NOT_FOUND,
            'errors' => ['message' => 'No unicorns found in the farm']
        ])
    )]
    #[Route('', name: 'api_unicorns', methods: [Request::METHOD_GET])]
    public function listUnicorns(UnicornRepository $unicornRepository): Response
    {
        $unicorns = $unicornRepository->createQueryBuilder('u')->getQuery()->getArrayResult();

        if (!$unicorns) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'No unicorns found in the farm.');
        }

        return $this->json($unicorns, context: [AbstractObjectNormalizer::GROUPS => ['unicorn']]);
    }

    #[OA\Tag('unicorns')]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'Unicorn id',
        schema: new OA\Schema(type: 'integer'),
        example: 1
    )]
    #[OA\RequestBody(
        description: 'Purchase unicorn',
        content: [
            new OA\MediaType(mediaType: "application/json", schema: new OA\Schema(new Model(type: UserDto::class))),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'Return successfully post created',
        content: new Model(type: Unicorn::class, groups: ['unicorn'])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Unicorn id doest not exists',
        content: new JsonContent(example: [
            'statusCode' => Response::HTTP_NOT_FOUND,
            'errors' => ['message' => 'Unicorn not found']
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_BAD_REQUEST,
        description: 'Unicorn is already purchased',
        content: new JsonContent(example: [
            'statusCode' => Response::HTTP_BAD_REQUEST,
            'errors' => ['message' => 'Unicorn is already purchased']
        ])
    )]
    #[Route('/{id}/purchase', name: 'api_unicorn_purchase', methods: [Request::METHOD_POST])]
    public function purchaseUnicorn(
        #[MapRequestPayload] UserDto $userDto,
        UnicornRepository $unicornRepository,
        PostRepository $postRepository,
        UserRepository $userRepository,
        ?Unicorn $unicorn,
        MailerInterface $mailer
    ): Response {
        if (!$unicorn) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Unicorn not found.');
        }

        if ($unicorn->getStatus() === UnicornStatusEnum::PURCHASED) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Unicorn is already purchased.');
        }

        // Check if user exists otherwise create new one
        $user = $userRepository->findOneBy(['email' => $userDto->email]);
        if (!$user) {
            $user = new User();
            $user->setEmail($userDto->email);
            $user->setName($userDto->displayName ?? $userDto->email);
            $userRepository->save($user);
        }

        if ($unicorn->getPosts()) {
            $mailContent = '<p>See list of comments for purchased unicorn:</p><br/><ul>';
            foreach ($unicorn->getPosts() as $post) {
                $mailContent .= '<li>' . $post->getUser() . ': ' . $post->getMessage();
                $postRepository->remove($post);
            }
            $mailContent .= '</ul>';
        } else {
            $mailContent = '<p>Unicorn din not received any messages.</p>';
        }

        $unicorn->setStatus(UnicornStatusEnum::PURCHASED);
        $unicorn->setBuyer($user);
        $unicornRepository->save($unicorn);

        $email = (new Email())
            ->to($user->getEmail())
            ->subject("Thank you for purchasing our unicorn: {$unicorn->getName()}")
            ->html($mailContent);
        $mailer->send($email);

        return $this->json($unicorn, context: [AbstractObjectNormalizer::GROUPS => ['unicorn']]);
    }
}
