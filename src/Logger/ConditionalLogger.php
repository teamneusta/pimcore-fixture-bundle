<?php
declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Logger;

use Psr\Log\LoggerInterface;

final class ConditionalLogger implements LoggerInterface
{
    public function __construct(
        private readonly LoggerInterface $inner,
        private readonly LoggingState $state,
    ) {
    }

    public function emergency(\Stringable|string $message, array $context = []): void
    {
        if ($this->state->isEnabled()) {
            $this->inner->emergency($message, $context);
        }
    }

    public function alert(\Stringable|string $message, array $context = []): void
    {
        if ($this->state->isEnabled()) {
            $this->inner->alert($message, $context);
        }
    }

    public function critical(\Stringable|string $message, array $context = []): void
    {
        if ($this->state->isEnabled()) {
            $this->inner->critical($message, $context);
        }
    }

    public function error(\Stringable|string $message, array $context = []): void
    {
        if ($this->state->isEnabled()) {
            $this->inner->error($message, $context);
        }
    }

    public function warning(\Stringable|string $message, array $context = []): void
    {
        if ($this->state->isEnabled()) {
            $this->inner->warning($message, $context);
        }
    }

    public function notice(\Stringable|string $message, array $context = []): void
    {
        if ($this->state->isEnabled()) {
            $this->inner->notice($message, $context);
        }
    }

    public function info(\Stringable|string $message, array $context = []): void
    {
        if ($this->state->isEnabled()) {
            $this->inner->info($message, $context);
        }
    }

    public function debug(\Stringable|string $message, array $context = []): void
    {
        if ($this->state->isEnabled()) {
            $this->inner->debug($message, $context);
        }
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        if ($this->state->isEnabled()) {
            $this->inner->log($level, $message, $context);
        }
    }
}
