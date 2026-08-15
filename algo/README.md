# Algo — FizzBuzz

Displays numbers from 1 to N. Multiples of 3 become `Fizz`, multiples of 5 become `Buzz`, multiples of both become `FizzBuzz`.

## Requirements

PHP >= 8.5, Composer.

## Install

```shell
composer install
```

## Usage

```shell
bin/fizzbuzz 15
```

## Tests & code quality

```shell
composer test      # phpunit
composer stan      # phpstan, level max
composer cs:check  # php-cs-fixer, PER-CS ruleset
composer qa        # all three
```

## Run with Docker

No local PHP needed:

```shell
docker build -t fizzbuzz .
docker run --rm fizzbuzz 15
```

QA suite (build fails if style, static analysis or tests fail):

```shell
docker build --target qa .
```

## Design notes

- `transform()` (rule for one number), `sequence()` (iteration) and `bin/fizzbuzz` (I/O) are separated: each part is testable in isolation.
- `sequence()` returns a generator: output is streamed, memory stays O(1) whatever N.
- Rules are intentionally hardcoded in a `match`: a configurable rule engine would be over-engineering for this scope.
