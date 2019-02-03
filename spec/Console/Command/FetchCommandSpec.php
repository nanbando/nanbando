<?php

namespace spec\Nanbando\Console\Command;

use Nanbando\Console\Command\FetchCommand;
use Nanbando\Console\OutputFormatter;
use Nanbando\Storage\Storage;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchCommandSpec extends ObjectBehavior
{
    public function let(
        Storage $storage,
        OutputFormatter $outputFormatter,
        InputInterface $input
    ) {
        $this->beConstructedWith($storage, $outputFormatter);

        $input->bind(Argument::cetera())->willReturn(null);
        $input->isInteractive()->willReturn(null);
        $input->hasArgument(Argument::cetera())->willReturn(null);
        $input->validate()->willReturn(null);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(FetchCommand::class);
    }

    public function it_extends_symfony_command()
    {
        $this->shouldBeAnInstanceOf(Command::class);
    }

    public function it_should_upload_not_existing_files(
        InputInterface $input,
        OutputInterface $output,
        Storage $storage,
        OutputFormatter $outputFormatter
    ) {
        $input->getArgument('file')->willReturn('20190101-173700');

        $storage->fetch('20190101-173700', $outputFormatter)->shouldBeCalled();

        $this->run($input, $output);
    }
}
