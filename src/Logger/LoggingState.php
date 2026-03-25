<?php
declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Logger;

final class LoggingState
{
    private bool $enabled = true;

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
