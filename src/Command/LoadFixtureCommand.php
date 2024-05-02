<?php declare(strict_types=1);

namespace Neusta\Pimcore\FixtureBundle\Command;

use Neusta\Pimcore\FixtureBundle\Event\BeforeExecuteFixture;
use Neusta\Pimcore\FixtureBundle\FixtureLoader\SelectiveFixtureLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommand(
    name: 'neusta:pimcore-fixture:load',
    description: 'Loads a defined fixture class.',
)]
final class LoadFixtureCommand extends Command
{
    public function __construct(
        private readonly SelectiveFixtureLoader $fixtureLoader,
        private readonly EventDispatcherInterface $eventDispatcher,
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

            To define the fixture to load, it's fully qualified class name has to be provided.
            e.g. <info>%command.name% 'MyProduct\SomeBundle\TheAwesomeFixture'</info>

            <info>Important:</info> This command <comment>must not</comment> be executed twice. On a second execution,
            this command would fail, because the keys of the Pimcore data objects are already present.
            To re-run the command, please reset the database first.
            EOF
        );

        $this->addArgument('fixture', InputArgument::REQUIRED, 'The Fixture to load');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fixture = $input->getArgument('fixture');

        $output->writeln('Loading fixture');
        $output->writeln('');

        $this->eventDispatcher->addListener(BeforeExecuteFixture::class, function (BeforeExecuteFixture $event) use ($output) {
            $output->writeln(sprintf(
                ' - Loading <info>%s</info>',
                $event->fixture::class
            ));
        });

        $this->fixtureLoader->setFixturesToLoad([$fixture])->loadFixtures();

        $output->writeln('Loading fixtures completed.');

        return Command::SUCCESS;
    }
}
