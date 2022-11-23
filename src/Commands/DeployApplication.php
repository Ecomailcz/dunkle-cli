<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeployApplication extends Command
{

    public static $defaultName = 'dunkle:deploy-app';

    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Deploy the application');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $zipName = $this->projectDir . '/hubert.zip';
        $zip = new \ZipArchive();

        if ($zip->open($zipName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
            // Create recursive directory iterator
            /** @var \SplFileInfo[] $files */
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($this->projectDir),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file)
            {
                // Skip directories (they would be added automatically)
                if (!$file->isDir())
                {
                    // Get real and relative path for current file
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($this->projectDir) + 1);

                    // Add current file to archive
                    $zip->addFile($filePath, $relativePath);
                }
            }

            // Zip archive  will be created only after closing object
            $zip->close();
        }

        // todo send this zip

        // todo delete folder

        return 0;
    }

}