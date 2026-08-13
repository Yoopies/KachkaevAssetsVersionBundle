<?php

namespace Kachkaev\AssetsVersionBundle\Command;

use Kachkaev\AssetsVersionBundle\AssetsVersionManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'assets-version:increment',
    description: 'Increments assets version parameter',
)]
class IncrementCommand extends Command
{
    public function __construct(
        private readonly AssetsVersionManager $assetsVersionManager,
        private readonly string $parameterName,
        private readonly string $filePath,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                    'delta',
                    InputArgument::OPTIONAL,
                    'Number to increment the assets version by',
                    1
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Incrementing parameter <info>'.$this->parameterName.'</info> in <info>'.basename($this->filePath).'</info> by <info>'.var_export($input->getArgument('delta'), true).'</info>...');

        $this->assetsVersionManager->incrementVersion($input->getArgument('delta'));

        $output->writeln('Done. New value for <info>'.$this->parameterName.'</info> is <info>'.$this->assetsVersionManager->getVersion().'</info>. Clearing of <info>prod</info> cache is required.');

        return self::SUCCESS;
    }
}
