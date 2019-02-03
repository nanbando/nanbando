<?php

namespace Nanbando\Console\Command;

use Nanbando\Backup\BackupArchiveFactory;
use Nanbando\Backup\BackupRunner;
use Nanbando\Console\OutputFormatter;
use Nanbando\Storage\Storage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BackupCommand extends Command
{
    /**
     * @var BackupRunner
     */
    private $backupRunner;

    /**
     * @var BackupArchiveFactory
     */
    private $factory;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var OutputFormatter
     */
    private $outputFormatter;

    public function __construct(
        BackupRunner $backupRunner,
        BackupArchiveFactory $factory,
        Storage $storage,
        OutputFormatter $outputFormatter
    ) {
        parent::__construct();

        $this->backupRunner = $backupRunner;
        $this->factory = $factory;
        $this->storage = $storage;
        $this->outputFormatter = $outputFormatter;
    }

    protected function configure()
    {
        $this->addArgument('label', InputArgument::OPTIONAL);
        $this->addOption('message', 'm', InputOption::VALUE_REQUIRED);
        $this->addOption('push', 'p', InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $backupArchive = $this->factory->create();
        $backupArchive->set('label', $input->getArgument('label'));
        $backupArchive->set('message', $input->getOption('message'));

        $this->backupRunner->run($backupArchive);

        if ($input->getOption('push')) {
            $this->outputFormatter->headline('Push backup %s', $backupArchive->get('name'));

            $this->storage->push($backupArchive->get('name'), $this->outputFormatter);

            $this->outputFormatter->info('Push finished');
        }
    }
}
