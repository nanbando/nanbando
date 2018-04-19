<?php

namespace Nanbando\Backup;

use Nanbando\Clock\ClockInterface;
use Nanbando\Console\OutputFormatter;
use Nanbando\File\FileHasher;
use Nanbando\Script\ScriptInterface;
use Nanbando\Script\ScriptRegistry;
use Nanbando\TempFileManager\TempFileManagerInterface;

class BackupRunner
{
    /**
     * @var ClockInterface
     */
    private $clock;

    /**
     * @var ScriptRegistry
     */
    private $scriptManager;

    /**
     * @var BackupWriter
     */
    private $backupWriter;

    /**
     * @var TempFileManagerInterface
     */
    private $tempFileManager;

    /**
     * @var FileHasher
     */
    private $fileHasher;

    /**
     * @var OutputFormatter
     */
    private $output;

    public function __construct(
        ClockInterface $clock,
        ScriptRegistry $scriptRegistry,
        BackupWriter $backupWriter,
        TempFileManagerInterface $tempFileManager,
        FileHasher $fileHasher,
        OutputFormatter $output
    ) {
        $this->clock = $clock;
        $this->scriptManager = $scriptRegistry;
        $this->backupWriter = $backupWriter;
        $this->tempFileManager = $tempFileManager;
        $this->output = $output;
        $this->fileHasher = $fileHasher;
    }

    public function run(string $label = null, string $message = null): BackupArchiveInterface
    {
        $started = $this->clock->getDateTime();

        $this->output->headline('Backup started at %s', $started);
        $this->output->list(
            [
                'label' => $label ?: '',
                'message' => $message ?: '',
            ]
        );

        $backupArchive = new BackupArchive($this->fileHasher);
        $backupArchive->set('label', $label ?: '');
        $backupArchive->set('message', $message ?: '');
        $backupArchive->set('started', $started);
        foreach ($this->scriptManager->get() as $name => $script) {
            $this->runScript(new BackupArchiveDecorator($name, $backupArchive), $name, $script);
        }

        $finished = $this->clock->getDateTime();
        $backupArchive->set('finished', $finished);

        $archiveFile = $this->writeArchive($backupArchive);
        $this->cleanupTempFiles();

        $this->output->info('Backup finished at %s in file %s', $started, $archiveFile);

        return $backupArchive;
    }

    protected function runScript(BackupArchiveInterface $backupArchive, string $name, ScriptInterface $script): void
    {
        $sectionOutput = $this->output->section();
        $sectionOutput->headline('Executing script %s', $name);

        $backupArchive->set('started', $this->clock->getDateTime());
        $script->backup($backupArchive, $sectionOutput);
        $backupArchive->set('finished', $this->clock->getDateTime());

        $sectionOutput->clear();
        $sectionOutput->checkmark('Executing script %s', $name);
    }

    protected function writeArchive(BackupArchive $backupArchive): string
    {
        $sectionOutput = $this->output->section();
        $sectionOutput->headline('Generating backup archive');
        $archiveFile = $this->backupWriter->write($backupArchive, $sectionOutput);

        $sectionOutput->clear();
        $sectionOutput->checkmark('Generating backup archive');

        return $archiveFile;
    }

    protected function cleanupTempFiles(): void
    {
        $sectionOutput = $this->output->section();
        $sectionOutput->headline('Cleanup temporary files');
        $this->tempFileManager->cleanup($sectionOutput);

        $sectionOutput->clear();
        $sectionOutput->checkmark('Cleanup temporary files');
    }
}
