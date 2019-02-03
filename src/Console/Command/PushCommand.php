<?php

namespace Nanbando\Console\Command;

use Nanbando\Console\OutputFormatter;
use Nanbando\Storage\Storage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PushCommand extends Command
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output->headline('Push started');

        $this->storage->pushAll($this->output);

        $this->output->info('Push finished');
    }
}
