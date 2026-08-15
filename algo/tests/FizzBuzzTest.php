<?php

declare(strict_types=1);

namespace Fulll\Algo\Tests;

use Fulll\Algo\FizzBuzz;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FizzBuzzTest extends TestCase
{
    #[DataProvider('provideTransformCases')]
    public function testTransform(int $number, string $expected): void
    {
        self::assertSame($expected, (new FizzBuzz())->transform($number));
    }

    /**
     * @return iterable<string, array{int, string}>
     */
    public static function provideTransformCases(): iterable
    {
        yield 'not a multiple' => [1, '1'];
        yield 'multiple of 3' => [3, 'Fizz'];
        yield 'multiple of 5' => [5, 'Buzz'];
        yield 'multiple of 3 and 5' => [15, 'FizzBuzz'];
        yield 'other multiple of 3' => [9, 'Fizz'];
        yield 'other multiple of 5' => [20, 'Buzz'];
        yield 'other multiple of 3 and 5' => [45, 'FizzBuzz'];
    }

    public function testSequenceYieldsFromOneToLimit(): void
    {
        $sequence = (new FizzBuzz())->sequence(5);

        self::assertSame(['1', '2', 'Fizz', '4', 'Buzz'], iterator_to_array($sequence, false));
    }

    public function testSequenceRejectsLimitBelowOne(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new FizzBuzz())->sequence(0);
    }
}
