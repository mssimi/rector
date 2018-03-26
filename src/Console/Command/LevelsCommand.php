<?php declare(strict_types=1);

namespace Rector\Console\Command;

use Rector\Console\ConsoleStyle;
use Rector\Naming\CommandNaming;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

final class LevelsCommand extends Command
{
    /**
     * @var ConsoleStyle
     */
    private $consoleStyle;

    public function __construct(ConsoleStyle $consoleStyle)
    {
        $this->consoleStyle = $consoleStyle;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription('List available levels.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $levels = $this->getAvailbleLevels();

        $this->consoleStyle->title(sprintf('%d available levels:', count($levels)));
        $this->consoleStyle->listing($levels);

        return 0;
    }

    /**
     * @return string[]
     */
    private function getAvailbleLevels(): array
    {
        $finder = Finder::create()->files()
            ->in(__DIR__ . '/../../config/level');

        $levels = [];
        foreach ($finder->getIterator() as $fileInfo) {
            $levels[] = $fileInfo->getBasename('.' . $fileInfo->getExtension());
        }

        sort($levels);

        return array_unique($levels);
    }
}
