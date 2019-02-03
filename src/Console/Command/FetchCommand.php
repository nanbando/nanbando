<?php

namespace Nanbando\Console\Command;

use Nanbando\Console\OutputFormatter;
use Nanbando\Storage\Storage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class FetchCommand extends Command
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var OutputFormatter
     */
    private $output;

    public function __construct(Storage $storage, OutputFormatter $output)
    {
        parent::__construct();

        $this->storage = $storage;
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
            if (!$file->isFetched()) {
                $options[] = $file->getName();
            }
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

        $this->output->headline('Fetch backup %s started', $file);

        $this->storage->fetch($file, $this->output);

        $this->output->info('Fetch finished');
    }
}
