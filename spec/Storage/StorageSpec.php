<?php

namespace spec\Nanbando\Storage;

use Nanbando\Console\OutputFormatter;
use Nanbando\Console\SectionOutputFormatter;
use Nanbando\Storage\ArchiveInfo;
use Nanbando\Storage\LocalStorage;
use Nanbando\Storage\RemoteStorage;
use Nanbando\Storage\Storage;
use PhpSpec\ObjectBehavior;

class StorageSpec extends ObjectBehavior
{
    public function let(
        LocalStorage $localStorage,
        RemoteStorage $remoteStorage1,
        RemoteStorage $remoteStorage2
    ) {
        $this->beConstructedWith($localStorage, ['test1' => $remoteStorage1, 'test2' => $remoteStorage2]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Storage::class);
    }

    public function it_should_get_archive(
        LocalStorage $localStorage,
        ArchiveInfo $info
    ) {
        $localStorage->get('20180415-205000')->willReturn($info);

        $this->get('20180415-205000');
    }

    public function it_should_fetch(
        LocalStorage $localStorage,
        ArchiveInfo $info,
        RemoteStorage $remoteStorage1,
        RemoteStorage $remoteStorage2,
        OutputFormatter $output,
        SectionOutputFormatter $section
    ) {
        $info->isFetched()->willReturn(false);
        $info->getName()->willReturn('20180415-205000');
        $localStorage->get('20180415-205000')->willReturn($info);

        $remoteStorage1->exists($info)->willReturn(true);
        $remoteStorage2->exists($info)->willReturn(false);
        $remoteStorage1->fetch($info)->shouldBeCalled();
        $remoteStorage2->fetch($info)->shouldNotBeCalled();

        $output->section()->willReturn($section);

        $this->fetch('20180415-205000', $output);
    }

    public function it_should_not_fetch(
        LocalStorage $localStorage,
        ArchiveInfo $info,
        RemoteStorage $remoteStorage1,
        RemoteStorage $remoteStorage2,
        OutputFormatter $output,
        SectionOutputFormatter $section
    ) {
        $info->isFetched()->willReturn(true);
        $info->getName()->willReturn('20180415-205000');
        $localStorage->get('20180415-205000')->willReturn($info);

        $remoteStorage1->exists($info)->shouldNotBeCalled();
        $remoteStorage2->exists($info)->shouldNotBeCalled();
        $remoteStorage1->fetch($info)->shouldNotBeCalled();
        $remoteStorage2->fetch($info)->shouldNotBeCalled();

        $output->section()->willReturn($section);

        $this->fetch('20180415-205000', $output);
    }

    public function it_should_fetch_from_second_storage(
        LocalStorage $localStorage,
        ArchiveInfo $info,
        RemoteStorage $remoteStorage1,
        RemoteStorage $remoteStorage2,
        OutputFormatter $output,
        SectionOutputFormatter $section
    ) {
        $info->isFetched()->willReturn(false);
        $info->getName()->willReturn('20180415-205000');
        $localStorage->get('20180415-205000')->willReturn($info);

        $remoteStorage1->exists($info)->willReturn(false);
        $remoteStorage2->exists($info)->willReturn(true);
        $remoteStorage1->fetch($info)->shouldNotBeCalled();
        $remoteStorage2->fetch($info)->shouldBeCalled();

        $output->section()->willReturn($section);

        $this->fetch('20180415-205000', $output)->shouldBe($info);
    }

    public function it_should_fetch_database(
        LocalStorage $localStorage,
        ArchiveInfo $info,
        RemoteStorage $remoteStorage1,
        RemoteStorage $remoteStorage2,
        OutputFormatter $output,
        SectionOutputFormatter $section
    ) {
        $info->isFetched()->willReturn(false);
        $info->getName()->willReturn('20180415-205000');
        $localStorage->get('20180415-205000')->willReturn($info);

        $remoteStorage1->exists($info)->willReturn(true);
        $remoteStorage2->exists($info)->willReturn(false);
        $remoteStorage1->fetchDatabase($info)->shouldBeCalled();
        $remoteStorage2->fetchDatabase($info)->shouldNotBeCalled();

        $output->section()->willReturn($section);

        $this->fetchDatabase($info, $output);
    }

    public function it_should_not_fetch_database(
        LocalStorage $localStorage,
        ArchiveInfo $info,
        RemoteStorage $remoteStorage1,
        RemoteStorage $remoteStorage2,
        OutputFormatter $output,
        SectionOutputFormatter $section
    ) {
        $info->isFetched()->willReturn(true);
        $info->getName()->willReturn('20180415-205000');
        $localStorage->get('20180415-205000')->willReturn($info);

        $remoteStorage1->exists($info)->shouldNotBeCalled();
        $remoteStorage2->exists($info)->shouldNotBeCalled();
        $remoteStorage1->fetchDatabase($info)->shouldNotBeCalled();
        $remoteStorage2->fetchDatabase($info)->shouldNotBeCalled();

        $output->section()->willReturn($section);

        $this->fetchDatabase($info, $output);
    }

    public function it_should_fetch_database_from_second_storage(
        LocalStorage $localStorage,
        ArchiveInfo $info,
        RemoteStorage $remoteStorage1,
        RemoteStorage $remoteStorage2,
        OutputFormatter $output,
        SectionOutputFormatter $section
    ) {
        $info->isFetched()->willReturn(false);
        $info->getName()->willReturn('20180415-205000');
        $localStorage->get('20180415-205000')->willReturn($info);

        $remoteStorage1->exists($info)->willReturn(false);
        $remoteStorage2->exists($info)->willReturn(true);
        $remoteStorage1->fetchDatabase($info)->shouldNotBeCalled();
        $remoteStorage2->fetchDatabase($info)->shouldBeCalled();

        $output->section()->willReturn($section);

        $this->fetchDatabase($info, $output)->shouldBe($info);
    }

    public function it_should_fetch_archive(
        LocalStorage $localStorage,
        ArchiveInfo $info,
        RemoteStorage $remoteStorage1,
        RemoteStorage $remoteStorage2,
        OutputFormatter $output,
        SectionOutputFormatter $section
    ) {
        $info->isFetched()->willReturn(false);
        $info->getName()->willReturn('20180415-205000');
        $localStorage->get('20180415-205000')->willReturn($info);

        $remoteStorage1->exists($info)->willReturn(true);
        $remoteStorage2->exists($info)->willReturn(false);
        $remoteStorage1->fetchArchive($info)->shouldBeCalled();
        $remoteStorage2->fetchArchive($info)->shouldNotBeCalled();

        $output->section()->willReturn($section);

        $this->fetchArchive($info, $output);
    }

    public function it_should_not_fetch_archive(
        LocalStorage $localStorage,
        ArchiveInfo $info,
        RemoteStorage $remoteStorage1,
        RemoteStorage $remoteStorage2,
        OutputFormatter $output,
        SectionOutputFormatter $section
    ) {
        $info->isFetched()->willReturn(true);
        $info->getName()->willReturn('20180415-205000');
        $localStorage->get('20180415-205000')->willReturn($info);

        $remoteStorage1->exists($info)->shouldNotBeCalled();
        $remoteStorage2->exists($info)->shouldNotBeCalled();
        $remoteStorage1->fetchArchive($info)->shouldNotBeCalled();
        $remoteStorage2->fetchArchive($info)->shouldNotBeCalled();

        $output->section()->willReturn($section);

        $this->fetchArchive($info, $output);
    }

    public function it_should_fetch_darchive_from_second_storage(
        LocalStorage $localStorage,
        ArchiveInfo $info,
        RemoteStorage $remoteStorage1,
        RemoteStorage $remoteStorage2,
        OutputFormatter $output,
        SectionOutputFormatter $section
    ) {
        $info->isFetched()->willReturn(false);
        $info->getName()->willReturn('20180415-205000');
        $localStorage->get('20180415-205000')->willReturn($info);

        $remoteStorage1->exists($info)->willReturn(false);
        $remoteStorage2->exists($info)->willReturn(true);
        $remoteStorage1->fetchArchive($info)->shouldNotBeCalled();
        $remoteStorage2->fetchArchive($info)->shouldBeCalled();

        $output->section()->willReturn($section);

        $this->fetchArchive($info, $output)->shouldBe($info);
    }

    public function it_should_push_to_remote_storages(
        LocalStorage $localStorage,
        ArchiveInfo $info,
        RemoteStorage $remoteStorage1,
        RemoteStorage $remoteStorage2,
        OutputFormatter $output,
        SectionOutputFormatter $section
    ) {
        $info->getName()->willReturn('20180415-205000');

        $localStorage->get('20180415-205000')->willReturn($info);

        $remoteStorage1->exists($info)->willReturn(true);
        $remoteStorage1->push($info)->shouldNotBeCalled();
        $remoteStorage2->exists($info)->willReturn(false);
        $remoteStorage2->push($info)->shouldBeCalled();

        $output->section()->willReturn($section);

        $this->push('20180415-205000', $output);
    }

    public function it_should_push_all_to_remote_storages(
        LocalStorage $localStorage,
        ArchiveInfo $info1,
        ArchiveInfo $info2,
        RemoteStorage $remoteStorage1,
        RemoteStorage $remoteStorage2,
        OutputFormatter $output,
        SectionOutputFormatter $section
    ) {
        $info1->getName()->willReturn('file-1');
        $info2->getName()->willReturn('file-2');

        $localStorage->listFiles()->willReturn([$info1, $info2]);

        $remoteStorage1->exists($info1)->willReturn(true);
        $remoteStorage1->push($info1)->shouldNotBeCalled();
        $remoteStorage2->exists($info1)->willReturn(false);
        $remoteStorage2->push($info1)->shouldBeCalled();

        $remoteStorage1->exists($info2)->willReturn(false);
        $remoteStorage1->push($info2)->shouldBeCalled();
        $remoteStorage2->exists($info2)->willReturn(false);
        $remoteStorage2->push($info2)->shouldBeCalled();

        $output->section()->willReturn($section);

        $this->pushAll($output);
    }

    public function it_should_list_files(
        LocalStorage $localStorage,
        ArchiveInfo $info1,
        ArchiveInfo $info2,
        ArchiveInfo $info3,
        ArchiveInfo $info4,
        RemoteStorage $remoteStorage1,
        RemoteStorage $remoteStorage2
    ) {
        $info1->getName()->willReturn('file-1');
        $info2->getName()->willReturn('file-2');
        $info3->getName()->willReturn('file-3');
        $info4->getName()->willReturn('file-4');

        $localStorage->listFiles()->willReturn([$info1, $info2]);
        $remoteStorage1->listFiles()->willReturn([$info2, $info3]);
        $remoteStorage2->listFiles()->willReturn([$info1, $info2, $info4]);

        $this->listFiles()->shouldBe([$info1, $info2, $info3, $info4]);
    }
}
