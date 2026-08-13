<?php

namespace Kachkaev\AssetsVersionBundle\Command;

use Kachkaev\AssetsVersionBundle\AssetsVersionManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'assets-version:set',
    description: 'Sets assets version parameter to a given value',
)]
class SetCommand extends Command
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
                    'value',
                    InputArgument::REQUIRED,
                    'New value for assets version'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Setting parameter <info>'.$this->parameterName.'</info> in <info>'.basename($this->filePath).'</info> to <info>'.var_export($input->getArgument('value'), true).'</info>...');

        $this->assetsVersionManager->setVersion($input->getArgument('value'));

        $output->writeln('Done. Clearing of <info>prod</info> cache is required.');

        return self::SUCCESS;
    }
}
