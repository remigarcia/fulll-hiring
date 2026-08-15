<?php

declare(strict_types=1);

namespace Fulll\Algo;

final class FizzBuzz
{
    public function transform(int $number): string
    {
        return match (true) {
            $number % 15 === 0 => 'FizzBuzz',
            $number % 3 === 0 => 'Fizz',
            $number % 5 === 0 => 'Buzz',
            default => (string) $number,
        };
    }

    /**
     * Lazy sequence from 1 to $limit: O(1) memory whatever the limit.
     *
     * @return \Generator<int, string>
     */
    public function sequence(int $limit): \Generator
    {
        if ($limit < 1) {
            throw new \InvalidArgumentException(sprintf('Limit must be >= 1, got %d.', $limit));
        }

        return $this->generate($limit);
    }

    /**
     * A body containing yield only runs at first iteration; keeping
     * validation out of it makes sequence() throw at call time.
     *
     * @return \Generator<int, string>
     */
    private function generate(int $limit): \Generator
    {
        for ($number = 1; $number <= $limit; ++$number) {
            yield $this->transform($number);
        }
    }
}
