<?php

namespace App\DataFixtures\Factory;

use App\Entity\Unicorn;
use App\Enum\UnicornStatusEnum;
use App\Repository\UnicornRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Unicorn>
 *
 * @method        Unicorn|Proxy                     create(array|callable $attributes = [])
 * @method static Unicorn|Proxy                     createOne(array $attributes = [])
 * @method static Unicorn|Proxy                     find(object|array|mixed $criteria)
 * @method static Unicorn|Proxy                     findOrCreate(array $attributes)
 * @method static Unicorn|Proxy                     first(string $sortedField = 'id')
 * @method static Unicorn|Proxy                     last(string $sortedField = 'id')
 * @method static Unicorn|Proxy                     random(array $attributes = [])
 * @method static Unicorn|Proxy                     randomOrCreate(array $attributes = [])
 * @method static UnicornRepository|RepositoryProxy repository()
 * @method static Unicorn[]|Proxy[]                 all()
 * @method static Unicorn[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Unicorn[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Unicorn[]|Proxy[]                 findBy(array $attributes)
 * @method static Unicorn[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Unicorn[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class UnicornFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'description' => self::faker()->text(255),
            'name' => self::faker()->name(),
            'price' => self::faker()->randomFloat(),
            // 'status' => self::faker()->randomElement(UnicornStatusEnum::cases()),
            'status' => UnicornStatusEnum::PUBLISHED,
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Unicorn $unicorn): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Unicorn::class;
    }
}
