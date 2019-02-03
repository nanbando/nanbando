<?php

namespace Nanbando\Storage;

use Nanbando\Console\OutputFormatter;

class Storage
{
    /**
     * @var LocalStorage
     */
    private $localStorage;

    /**
     * @var RemoteStorage[]
     */
    private $storages;

    public function __construct(LocalStorage $localStorage, array $storages)
    {
        $this->localStorage = $localStorage;
        $this->storages = $storages;
    }

    public function get(string $name): ArchiveInfo
    {
        return $this->localStorage->get($name);
    }

    public function fetch(string $name, OutputFormatter $output): ArchiveInfo
    {
        $archive = $this->get($name);
        if ($archive->isFetched()) {
            return $archive;
        }

        foreach ($this->storages as $storage) {
            if ($storage->exists($archive)) {
                $storage->fetch($archive);

                break;
            }
        }

        $output->section()->checkmark('Fetched backup %s', $archive->getName());

        return $this->get($name);
    }

    public function fetchDatabase(ArchiveInfo $file): ArchiveInfo
    {
        if ($file->isFetched()) {
            return $file;
        }

        foreach ($this->storages as $storage) {
            if ($storage->exists($file)) {
                $storage->fetchDatabase($file);

                break;
            }
        }

        return $this->get($file->getName());
    }

    public function fetchArchive(ArchiveInfo $file): ArchiveInfo
    {
        if ($file->isFetched()) {
            return $file;
        }

        foreach ($this->storages as $storage) {
            if ($storage->exists($file)) {
                $storage->fetchArchive($file);

                break;
            }
        }

        return $this->get($file->getName());
    }

    public function push(string $name, OutputFormatter $output): void
    {
        $file = $this->get($name);

        foreach ($this->storages as $name => $storage) {
            $section = $output->section();

            if (!$storage->exists($file)) {
                $storage->push($file);
            }

            $section->checkmark('Pushed backup %s', $file->getName());
        }
    }

    public function pushAll(OutputFormatter $output): void
    {
        $files = $this->localStorage->listFiles();

        foreach ($this->storages as $name => $storage) {
            $section = $output->section();
            $section->headline('Push to %s', $name);

            foreach ($files as $file) {
                if (!$storage->exists($file)) {
                    $storage->push($file);
                }

                $section->checkmark('Pushed backup %s', $file->getName());
            }
        }
    }

    /**
     * @return ArchiveInfo[]
     */
    public function listFiles(): array
    {
        $result = [];
        foreach ($this->localStorage->listFiles() as $file) {
            $result[$file->getName()] = $file;
        }

        foreach ($this->storages as $storage) {
            foreach ($storage->listFiles() as $file) {
                $result[$file->getName()] = $file;
            }
        }

        return array_values($result);
    }
}
