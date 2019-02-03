<?php

namespace spec\Nanbando\Console\Command;

use Nanbando\Console\Command\RestoreCommand;
use Nanbando\Console\OutputFormatter;
use Nanbando\Restore\RestoreArchiveInterface;
use Nanbando\Restore\RestoreReader;
use Nanbando\Restore\RestoreRunner;
use Nanbando\Storage\Storage;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RestoreCommandSpec extends ObjectBehavior
{
    public function let(
        Storage $storage,
        RestoreRunner $restoreRunner,
        RestoreReader $restoreReader,
        OutputFormatter $output,
        InputInterface $input
    ) {
        $this->beConstructedWith($storage, $restoreRunner, $restoreReader, $output);

        $input->bind(Argument::cetera())->willReturn(null);
        $input->isInteractive()->willReturn(null);
        $input->hasArgument(Argument::cetera())->willReturn(null);
        $input->validate()->willReturn(null);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RestoreCommand::class);
    }

    public function it_extends_symfony_command()
    {
        $this->shouldBeAnInstanceOf(Command::class);
    }

    public function it_should_run_restore(
        InputInterface $input,
        OutputInterface $output,
        RestoreRunner $restoreRunner,
        RestoreReader $restoreReader,
        RestoreArchiveInterface $restoreArchive
    ) {
        $input->getArgument('file')->willReturn('20180419-170300');

        $restoreReader->open('20180419-170300')->willReturn($restoreArchive);

        $restoreRunner->run($restoreArchive)->shouldBeCalled();

        $this->run($input, $output);
    }
}
