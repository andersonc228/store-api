<?php

declare(strict_types=1);

namespace App\Tests\Architecture;

use PHPat\Selector\Selector;
use PHPat\Selector\SelectorInterface;
use PHPat\Test\Attributes\TestRule;
use PHPat\Test\Builder\Rule;
use PHPat\Test\PHPat;

final class ArchitectureRules
{
    /** Bounded contexts containing Domain and Application layers. */
    private const array CONTEXTS = [
        'Product',
        'User',
    ];

    /** UI-only entry points that consume domain contexts. */
    private const array UI_CONTEXTS = [
        'Api',
    ];

    /** @return iterable<string, Rule> */
    #[TestRule]
    public function domainIsPure(): iterable
    {
        foreach (self::CONTEXTS as $context) {
            yield $context => PHPat::rule()
                ->classes(Selector::inNamespace(sprintf('App\\%s\\Domain', $context)))
                ->canOnly()
                ->dependOn()
                ->classes(
                    Selector::inNamespace(sprintf('App\\%s\\Domain', $context)),
                    Selector::inNamespace('App\\Shared\\Common'),
                    Selector::inNamespace('App\\Shared\\Domain'),
                    Selector::inNamespace('Doctrine\\Common\\Collections'),
                    Selector::classname('DateTimeImmutable'),
                    Selector::classname('DateTimeInterface'),
                    Selector::classname('DomainException'),
                    Selector::classname('InvalidArgumentException'),
                    Selector::classname('Exception'),
                    Selector::classname('Throwable'),
                    Selector::classname('BackedEnum'),
                    Selector::classname('UnitEnum'),
                    $this->anyContextDomainModels(),
                    $this->anyContextDomainExceptions(),
                )
                ->because(
                    sprintf(
                        '%s\\Domain must be pure: it can only depend on its own Domain, Shared, and Doctrine Collections.',
                        $context
                    )
                );
        }
    }

    /** @return iterable<string, Rule> */
    #[TestRule]
    public function infrastructureIsPrivate(): iterable
    {
        foreach (self::CONTEXTS as $context) {
            yield $context => PHPat::rule()
                ->classes($this->otherControlledContexts($context))
                ->shouldNot()
                ->dependOn()
                ->classes(Selector::inNamespace(sprintf('App\\%s\\Infrastructure', $context)))
                ->because(
                    sprintf(
                        '%s\\Infrastructure is a private adapter layer: no other controlled context may reach into it.',
                        $context
                    )
                );
        }
    }

    /** @return iterable<string, Rule> */
    #[TestRule]
    public function applicationIsBounded(): iterable
    {
        foreach (self::CONTEXTS as $context) {
            yield $context => PHPat::rule()
                ->classes(Selector::inNamespace(sprintf('App\\%s\\Application', $context)))
                ->canOnly()
                ->dependOn()
                ->classes(
                    Selector::inNamespace(sprintf('App\\%s\\Domain', $context)),
                    Selector::inNamespace(sprintf('App\\%s\\Application', $context)),
                    Selector::inNamespace('App\\Shared\\Common'),
                    Selector::inNamespace('App\\Shared\\Domain'),
                    Selector::inNamespace('App\\Shared\\Application'),
                    $this->anyContextDomainModels(),
                    $this->anyContextDomainExceptions(),
                    Selector::classname('/^App\\\\[^\\\\]+\\\\Domain\\\\Events\\\\/', true),
                    // Command/Query DTOs from other contexts (excluding Handlers)
                    Selector::AllOf(
                        Selector::classname('/^App\\\\[^\\\\]+\\\\Application\\\\(Command|Query)\\\\/', true),
                        Selector::Not(Selector::classname('/Handler$/', true))
                    ),
                    Selector::inNamespace('Doctrine\\Common\\Collections'),
                    Selector::inNamespace('Psr\\Log'),
                    // Allow DateTimeInterface for testing purposes
                    Selector::classname('DateTimeImmutable'),
                    Selector::classname('DateTimeInterface'),
                    Selector::classname('SensitiveParameter'),
                    Selector::classname('DomainException'),
                    Selector::classname('InvalidArgumentException'),
                    Selector::classname('Exception'),
                    Selector::classname('Throwable'),
                )
                ->because(
                    sprintf(
                        '%s\\Application may only depend on its own Domain/Application, Shared, cross-context DTOs, Doctrine Collections, and PSR Log.',
                        $context
                    )
                );
        }
    }

    /** @return iterable<string, Rule> */
    #[TestRule]
    public function applicationHandlersAreInternal(): iterable
    {
        foreach (self::CONTEXTS as $context) {
            yield $context => PHPat::rule()
                ->classes($this->otherControlledContexts($context))
                ->shouldNot()
                ->dependOn()
                ->classes(
                    Selector::AllOf(
                        Selector::inNamespace(sprintf('App\\%s\\Application', $context)),
                        Selector::classname('/Handler$/', true)
                    )
                )
                ->because(
                    sprintf(
                        '%s\\Application handlers are internal: cross-context calls must dispatch Command/Query DTOs through the bus, never invoking the handler directly.',
                        $context
                    )
                );
        }
    }

    /** @return iterable<string, Rule> */
    #[TestRule]
    public function servicesAreInternal(): iterable
    {
        foreach (self::CONTEXTS as $context) {
            yield $context => PHPat::rule()
                ->classes($this->otherControlledContexts($context))
                ->shouldNot()
                ->dependOn()
                ->classes(
                    Selector::inNamespace(sprintf('App\\%s\\Domain\\Service', $context))
                )
                ->because(
                    sprintf(
                        '%s\\Domain services are internal contracts: cross-context access is not allowed directly.',
                        $context
                    )
                );
        }
    }

    #[TestRule]
    public function applicationDoesNotTouchInfrastructureOrUi(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::classname('/^App\\\\[^\\\\]+\\\\Application\\\\/', true))
            ->shouldNot()
            ->dependOn()
            ->classes(
                Selector::classname('/^App\\\\[^\\\\]+\\\\Infrastructure\\\\/', true),
                Selector::classname('/^App\\\\[^\\\\]+\\\\Ui\\\\/', true)
            )
            ->because(
                'Application orchestrates Domain via ports; adapters (Infrastructure) and entry points (Ui) depend on Application, never the reverse.'
            );
    }

    #[TestRule]
    public function uiDoesNotTouchDomainOrInfrastructure(): iterable
    {
        foreach (self::CONTEXTS as $context) {
            yield $context => PHPat::rule()
                ->classes(Selector::inNamespace(sprintf('App\\%s\\Ui', $context)))
                ->shouldNot()
                ->dependOn()
                ->classes(
                    Selector::classname('/^App\\\\[^\\\\]+\\\\Domain\\\\/', true),
                    Selector::classname('/^App\\\\[^\\\\]+\\\\Infrastructure\\\\/', true)
                )
                ->because(
                    sprintf(
                        '%s\\Ui talks to its own Application; it must not reach Domain or Infrastructure directly.',
                        $context
                    )
                );
        }
    }

    #[TestRule]
    public function sharedDoesNotDependOnAnyContext(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace('App\\Shared'))
            ->shouldNot()
            ->dependOn()
            ->classes(
                Selector::AllOf(
                    Selector::inNamespace('App'),
                    Selector::Not(Selector::inNamespace('App\\Shared')),
                    Selector::Not(Selector::inNamespace('App\\Tests'))
                )
            )
            ->because('Shared is the universal vocabulary: it must not depend on any specific bounded context.');
    }

    /** Selects all classes inside controlled bounded contexts except the current one. */
    private function otherControlledContexts(string $context): SelectorInterface
    {
        $others = array_values(array_filter(
            [...self::CONTEXTS, ...self::UI_CONTEXTS],
            static fn (string $c): bool => $c !== $context
        ));

        $selectors = array_map(
            static fn (string $c) => Selector::inNamespace(sprintf('App\\%s', $c)),
            $others
        );

        return Selector::AnyOf(...$selectors);
    }

    private function anyContextDomainModels(): SelectorInterface
    {
        return Selector::classname('/^App\\\\[^\\\\]+\\\\Domain\\\\Model\\\\/', true);
    }

    private function anyContextDomainExceptions(): SelectorInterface
    {
        return Selector::classname('/^App\\\\[^\\\\]+\\\\Domain\\\\Exception\\\\/', true);
    }
}
