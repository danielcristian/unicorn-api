<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Dto\PostDto;
use App\Controller\Dto\UserDto;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UnicornRepository;
use App\Repository\UserRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

#[Route(path: 'posts')]
class PostController extends AbstractController
{
    #[OA\Tag('posts')]
    #[OA\Response(
        response: 200,
        description: 'Returns all unicorns posts',
        content: new Model(type: Post::class, groups: ['post'])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'No posts found',
        content: new JsonContent(example: [
            'statusCode' => Response::HTTP_NOT_FOUND,
            'errors' => ['message' => 'No posts found']
        ])
    )]
    #[Route('', name: 'api_posts', methods: [Request::METHOD_GET])]
    public function listPosts(
        PostRepository $postRepository
    ): Response {
        $posts = $postRepository->createQueryBuilder('p')->getQuery()->getArrayResult();

        if (!$posts) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'No posts found.');
        }

        return $this->json($posts, context: [AbstractObjectNormalizer::GROUPS => ['post']]);
    }

    #[OA\Tag('posts')]
    #[OA\RequestBody(
        description: 'Post new message to unicorn',
        content: [
            new OA\MediaType(
                mediaType: "application/json",
                schema: new OA\Schema(
                    allOf: [
                        new OA\Schema(new Model(type: PostDto::class, groups: ['post:new'])),
                        new OA\Schema(new Model(type: UserDto::class)),
                    ],
                ),
            ),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'Return successfully post created',
        content: new Model(type: Post::class, groups: ['post'])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Unicorn not found',
        content: new JsonContent(example: [
            'statusCode' => Response::HTTP_NOT_FOUND,
            'errors' => ['message' => 'Unicorn not found.']
        ])
    )]
    #[Route('', name: 'api_unicorn_post', methods: [Request::METHOD_POST])]
    public function addPost(
        #[MapRequestPayload(validationGroups: ['post:new'])] PostDto $postDto,
        #[MapRequestPayload] UserDto $userDto,
        UserRepository $userRepository,
        PostRepository $postRepository,
        ?UnicornRepository $unicornRepository,
    ): Response {
        $unicorn = $unicornRepository->find($postDto->unicornId);
        if (!$unicorn) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Unicorn not found.');
        }

        // Check if user exists otherwise create new one
        $user = $userRepository->findOneBy(['email' => $userDto->email]);

        if (!$user) {
            $user = new User();
            $user->setEmail($userDto->email);
            $user->setName($userDto->displayName ?? $userDto->email);
            $userRepository->save($user);
        }

        $post = new Post();
        $post->setMessage($postDto->message);
        $post->setUser($user);
        $post->setUnicorn($unicorn);
        $postRepository->save($post);

        return $this->json($post, context: [AbstractObjectNormalizer::GROUPS => ['post']]);
    }

    #[OA\Tag('posts')]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'Unicorn id',
        schema: new OA\Schema(type: 'integer'),
        example: 1
    )]
    #[OA\Parameter(
        name: 'user_id',
        in: 'path',
        description: 'User id',
        schema: new OA\Schema(type: 'integer'),
        example: 1
    )]
    #[OA\RequestBody(
        description: 'Edit post',
        content: [
            new OA\MediaType(mediaType: "application/json", schema: new OA\Schema(new Model(type: PostDto::class))),
        ],
    )]
    #[OA\Response(
        response: 200,
        description: 'Post updated succesfully',
        content: new Model(type: Post::class, groups: ['post'])
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Entity not found',
        content: new JsonContent(example: [
            'statusCode' => Response::HTTP_NOT_FOUND,
            'errors' => ['message' => 'Post not found.']
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_UNAUTHORIZED,
        description: 'Post is not created by provided user',
        content: new JsonContent(example: [
            'statusCode' => Response::HTTP_UNAUTHORIZED,
            'errors' => ['message' => 'You are not authorized to edit this comment']
        ])
    )]
    #[Route('/{id}/user/{user_id}', name: 'api_edit_message', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
    public function editPost(
        #[MapRequestPayload] PostDto $postDto,
        PostRepository $postRepository,
        ?Post $post,
        #[MapEntity(expr: 'repository.find(user_id)')]
        ?User $user,
    ): Response {
        if (!$post) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Post not found.');
        } elseif (!$user) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'User not found.');
        } elseif ($post->getUser() !== $user) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'You are not authorized to edit this comment.');
        }

        $post->setMessage($postDto->message);
        $postRepository->save($post);

        return $this->json($post, context: [AbstractObjectNormalizer::GROUPS => ['post']]);
    }

    #[OA\Tag('posts')]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'Unicorn id',
        schema: new OA\Schema(type: 'integer'),
        example: 1
    )]
    #[OA\Parameter(
        name: 'user_id',
        in: 'path',
        description: 'User id',
        schema: new OA\Schema(type: 'integer'),
        example: 1
    )]
    #[OA\Response(
        response: 200,
        description: 'Post deleted succesfully',
    )]
    #[OA\Response(
        response: Response::HTTP_NOT_FOUND,
        description: 'Entity not found',
        content: new JsonContent(example: [
            'statusCode' => Response::HTTP_NOT_FOUND,
            'errors' => ['message' => 'Post not found.']
        ])
    )]
    #[OA\Response(
        response: Response::HTTP_UNAUTHORIZED,
        description: 'Post is not created by provided user',
        content: new JsonContent(example: [
            'statusCode' => Response::HTTP_UNAUTHORIZED,
            'errors' => ['message' => 'You are not authorized to edit this comment']
        ])
    )]
    #[Route('/{id}/user/{user_id}', name: 'api_delete_message', methods: [Request::METHOD_DELETE])]
    public function deletePost(
        PostRepository $postRepository,
        ?Post $post,
        #[MapEntity(expr: 'repository.find(user_id)')]
        ?User $user,
    ): Response {
        if (!$post) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Post not found.');
        } elseif (!$user) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'User not found.');
        } elseif ($post->getUser() !== $user) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'You are not authorized to delete this comment.');
        }

        $postRepository->remove($post);

        return $this->json('Post deleted succesfully.');
    }
}
