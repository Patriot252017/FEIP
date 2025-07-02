<?php

declare(strict_types=1);

namespace App\Command;

use Exception;
use Override;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:init-data',
    description: 'Initialize sample data in CSV files',
)]
final class InitDataCommand extends Command
{
    public function __construct(
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    #[Override]
    protected function configure(): void
    {
        $this->setHelp('This command creates sample data files for cottages and bookings');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->initializeCottagesFile();
            $this->initializeBookingsFile();

            $io->success('Sample data successfully initialized!');
            $io->text([
                'Files created:',
                '- ' . $this->projectDir . '/data/dev/cottages.csv',
                '- ' . $this->projectDir . '/data/dev/bookings.csv',
            ]);

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error('Error: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }

    private function initializeCottagesFile(): void
    {
        $this->writeCsvFile(
            $this->projectDir . '/data/dev/cottages.csv',
            [
                ['id', 'amenities', 'beds', 'distanceFromSea'],
                [1, 'санузел', 2, 100],
                [2, 'душевая кабина', 4, 50],
                [3, 'никаких', 6, 200],
            ]
        );
    }

    private function initializeBookingsFile(): void
    {
        $this->writeCsvFile(
            $this->projectDir . '/data/dev/bookings.csv',
            [
                ['id', 'phone', 'cottageId', 'comment', 'createdAt'],
            ]
        );
    }

    private function writeCsvFile(string $path, array $data): void
    {
        $dir = dirname($path);

        if (!file_exists($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        $file = fopen($path, 'wb');

        if (false === $file) {
            throw new RuntimeException('Failed to open file for writing');
        }

        try {
            foreach ($data as $row) {
                if (false === fputcsv($file, $row)) {
                    throw new RuntimeException('Failed to write data to file');
                }
            }
        } finally {
            fclose($file);
        }
    }
}
