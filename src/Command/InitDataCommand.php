<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:init-data',
    description: 'Initialize sample data in CSV files',
)]
class InitDataCommand extends Command
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        parent::__construct();
        $this->projectDir = $projectDir;
    }

    protected function configure(): void
    {
        $this->setHelp('This command creates sample data files for cottages and bookings');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        try {
            $this->initializeCottagesFile();
            $this->initializeBookingsFile();
            
            $io->success('Sample data successfully initialized!');
            $io->text([
                'Files created:',
                '- '.$this->projectDir.'/data/dev/cottages.csv',
                '- '.$this->projectDir.'/data/dev/bookings.csv'
            ]);
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Error: '.$e->getMessage());
            return Command::FAILURE;
        }
    }

    private function initializeCottagesFile(): void
    {
        $filePath = $this->projectDir.'/data/dev/cottages.csv';
        $data = [
            ['id', 'amenities', 'beds', 'distanceFromSea'],
            [1, 'санузел', 2, 100],
            [2, 'душевая кабина', 4, 50],
            [3, 'никаких', 6, 200]
        ];
        
        $this->writeCsvFile($filePath, $data);
    }

    private function initializeBookingsFile(): void
    {
        $filePath = $this->projectDir.'/data/dev/bookings.csv';
        $data = [
            ['id', 'phone', 'cottageId', 'comment', 'createdAt']
        ];
        
        $this->writeCsvFile($filePath, $data);
    }

    private function writeCsvFile(string $path, array $data): void
    {
        $dir = dirname($path);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        $file = fopen($path, 'w');
        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
    }
}