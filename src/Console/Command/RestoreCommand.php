<?php

namespace Nanbando\Console\Command;

use Nanbando\Console\OutputFormatter;
use Nanbando\Restore\RestoreReader;
use Nanbando\Restore\RestoreRunner;
use Nanbando\Storage\Storage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class RestoreCommand extends Command
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var RestoreRunner
     */
    private $restoreRunner;

    /**
     * @var RestoreReader
     */
    private $restoreReader;

    /**
     * @var OutputFormatter
     */
    private $output;

    public function __construct(
        Storage $storage,
        RestoreRunner $restoreRunner,
        RestoreReader $restoreReader,
        OutputFormatter $output
    ) {
        parent::__construct();

        $this->storage = $storage;
        $this->restoreRunner = $restoreRunner;
        $this->restoreReader = $restoreReader;
        $this->output = $output;
    }

    protected function configure()
    {
        $this->addArgument('file', InputArgument::REQUIRED);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if ($input->getArgument('file')) {
            return;
        }

        $files = $this->storage->listFiles();
        $options = [];
        foreach ($files as $file) {
            $options[] = $file->getName();
        }

        $question = new ChoiceQuestion('Backup:', $options);
        $helper = new QuestionHelper();
        $file = $helper->ask($input, $output, $question);
        $input->setArgument('file', $file);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var string $file */
        $file = $input->getArgument('file');

        $this->output->headline('Restoring %s', $file);

        $restoreArchive = $this->restoreReader->open($file);

        $this->restoreRunner->run($restoreArchive);
    }
}
