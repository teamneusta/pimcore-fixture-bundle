<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Command;

use Neusta\Pimcore\FixtureBundle\Factory\FixtureFactory;
use Neusta\Pimcore\FixtureBundle\Factory\FixtureInstantiator\FixtureInstantiatorForAll;
use Neusta\Pimcore\FixtureBundle\Factory\FixtureInstantiator\FixtureInstantiatorForParametrizedConstructors;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Profiler\Profiler;

#[AsCommand(
    name: 'neusta:pimcore-fixtures:load',
    description: 'Loads a defined fixture class.',
)]
class LoadFixturesCommand extends Command
{
    private OutputInterface $output;

    public function __construct(
        private ContainerInterface $container,
        private string $fixtureClass,
        private ?Profiler $profiler = null,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp(
            <<<'EOF'
            The <info>%command.name%</info> command takes a single fixture and loads it.
            That fixture itself may of course depend on further fixtures, thus allowing to build up
            an entire fixture hierarchy to load.
            The result is sample data in your Pimcore instance.

            Use <info>-v, --verbose</info> to output the time the fixtures took to create and to show an ordered list
            of executed fixtures.

              <info>php %command.name% -v</info>

            <info>Important:</info> This command is currently very limited. The fixture that is
            loaded is static at the moment. Its dependency hierarchy containing further fixtures must be extended
            when new fixtures are created and need to be loaded as well.
            <info>Important:</info> This command <comment>must not</comment> be executed twice. On a second execution,
            this command would fail, because the keys of the Pimcore data objects are already present.
            To re-run the command, please reset the database first.
            EOF
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;

        $output->writeln('Loading fixture');
        $output->writeln('');

        $instantiators = [
            new FixtureInstantiatorForParametrizedConstructors($this->container),
            new FixtureInstantiatorForAll(),
        ];

        $trackExecutionTimes = OutputInterface::VERBOSITY_VERBOSE === $this->output->getVerbosity();
        $fixtureFactory = new FixtureFactory([], $instantiators, $this->profiler, $trackExecutionTimes);
        $fixtureFactory->createFixtures([$this->fixtureClass]);

        if (OutputInterface::VERBOSITY_VERBOSE === $this->output->getVerbosity()) {
            $executionTimes = $fixtureFactory->getExecutionTimes();
            $convertedExecutionTimes = array_map(
                // some magic formatting '0.02' => '    0.020'
                static fn ($value, $key) => [$key, str_pad(sprintf('%.3f', $value), 9, ' ', \STR_PAD_LEFT)],
                $executionTimes,
                array_keys($executionTimes),
            );

            $output->writeln('<info>Print the execution times of the fixtures:</info>');

            $table = new Table($output->section());
            $table->setHeaders(['Fixture', 'Time in s']);
            $table->setRows($convertedExecutionTimes);
            $table->render();

            $output->writeln('When the duration is <info>0.000</info>,'
                . ' that will likely mean the fixture was executed by dependency', );
            $output->writeln('');

            $executionTree = $fixtureFactory->getExecutionTree();
            $output->writeln('<info>Print the execution tree of the fixtures:</info>');

            foreach ($executionTree as $entry) {
                $fixtureClass = $entry['fixtureClass'];
                $level = $entry['level'];
                $prefix = str_pad(' ', $level * 2);
                $output->writeln($prefix . $fixtureClass);
            }
            $output->writeln('');
        }

        $output->writeln('Loading fixture completed.');

        return Command::SUCCESS;
    }
}
