# Backend — Vehicle fleet parking management

DDD & CQRS implementation of the [Fulll backend subject](https://github.com/fulll/hiring/blob/master/Backend/ddd-and-cqrs-intermediate-senior.md) (intermediate/senior variant).

## Requirements

PHP >= 8.5 with `pdo_pgsql`, Composer, Docker (PostgreSQL).

## Install

```shell
composer install
docker compose up -d   # starts PostgreSQL, schema auto-applied on first boot
```

## Usage

```shell
./fleet create <userId>                                        # prints the new fleetId
./fleet register-vehicle <fleetId> <vehiclePlateNumber>
./fleet localize-vehicle <fleetId> <vehiclePlateNumber> lat lng [alt]
```

No local PHP? Same commands through Docker:

```shell
docker compose run --rm cli create user1
```

## Tests & code quality

```shell
composer test               # behat, all scenarios, in-memory repositories
composer test:integration   # behat @critical scenarios against PostgreSQL
composer qa                 # cs:check + stan + test (also: docker build --target qa .)
composer db:init            # (re)apply the SQL schema
```

Note: `test:integration` truncates the local database between scenarios.

## Architecture

```
src/
├── Domain/   # aggregates Fleet & Vehicle, value objects, repository interfaces,
│             # business exceptions — one folder per aggregate
├── App/      # CQRS: 3 commands, 2 queries, one handler each
└── Infra/    # adapters: Cli (symfony/console), InMemory, Postgres (raw PDO)
```

Dependencies only flow inwards: `Infra → App → Domain`. The CLI and the Behat
suites drive the exact same handlers; repositories are swapped per context
(in-memory by default, PostgreSQL for the CLI and the `integration` profile).

## Step 3 — code quality tools, which and why

- **PHPStan (level max)** — catches type errors and impossible states before any test runs.
- **PHP-CS-Fixer (PER-CS)** — one non-negotiable style, zero review time spent on formatting.
- **Behat** — the business specs *are* the test suite; profiles rerun critical scenarios on real infrastructure.

One entry point for all three: `composer qa` — identical locally, in Docker and in CI.

Worth adding:

- **Deptrac** — turns the `Infra → App → Domain` dependency rule into a CI-enforced constraint.
- **Infection** — mutation testing: verifies the suite actually fails when the domain logic is broken.
- **Rector** — automated refactoring, keeps the code on current PHP idioms at each version bump.
- **composer audit** — fails the build on known vulnerabilities in locked dependencies.

## Step 3 — CI/CD in a few words

CI ([workflow](../.github/workflows/ci.yml), runs on every push to `main` and every PR): checkout → PHP 8.5 setup →
cached `composer install` → `composer qa` → schema + `@critical` scenarios against a PostgreSQL
service container. CD would follow: build the runtime Docker image, tag it with the commit SHA,
push to a registry, deploy staging then production with schema migrations applied before rollout.
Migrations stay backward-compatible (expand/contract), so rollback = redeploy the previous tag,
no database change involved.
