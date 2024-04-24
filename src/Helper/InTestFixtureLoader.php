<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Helper;

use Pimcore\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class InTestFixtureLoader
{
    private Application $application;

    public function __construct(KernelInterface $kernel)
    {
        $this->application = new Application($kernel);
        $this->application->setAutoExit(false);
    }

    /**
     * @param array<string> $groups
     */
    public function load(array $groups): void
    {
        $arguments = [
            'command' => 'doctrine:fixtures:load',
            '--append' => true,
        ];

        if (!empty($groups)) {
            $arguments['--group'] = $groups;
        }

        $output = new BufferedOutput();
        $exit = $this->application->run(new ArrayInput($arguments), $output);

        if (0 !== $exit) {
            throw new \RuntimeException(sprintf('Error loading fixtures: %s', $output->fetch()));
        }
    }
}