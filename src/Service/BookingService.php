<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

class BookingService
{
    private string $bookingsFile;
    private LoggerInterface $logger;
    private const CSV_HEADERS = ['id', 'phone', 'cottageId', 'comment', 'createdAt'];

    public function __construct(string $bookingsFile, LoggerInterface $logger)
    {
        $this->bookingsFile = $bookingsFile;
        $this->logger = $logger;
        $this->initializeFile();
    }

    private function initializeFile(): void
    {
        if (!file_exists(dirname($this->bookingsFile))) {
            mkdir(dirname($this->bookingsFile), 0777, true);
        }

        if (!file_exists($this->bookingsFile) || filesize($this->bookingsFile) === 0) {
            $this->writeCsvHeaders();
        }
    }

    private function writeCsvHeaders(): void
    {
        $file = fopen($this->bookingsFile, 'w');
        if ($file) {
            fputcsv($file, self::CSV_HEADERS);
            fclose($file);
        }
    }

    public function updateBooking(string $id, string $newComment): bool
    {
        try {
            
            $bookings = $this->readAllBookings();
            $updated = false;

            
            foreach ($bookings as &$booking) {
                if ($booking['id'] === $id) {
                    $booking['comment'] = $newComment;
                    $updated = true;
                    break;
                }
            }

            if ($updated) {
                
                $this->writeAllBookings($bookings);
                $this->logger->info("Booking updated", ['id' => $id]);
                return true;
            }

            $this->logger->warning("Booking not found", ['id' => $id]);
            return false;

        } catch (\Exception $e) {
            $this->logger->error("Update failed", [
                'error' => $e->getMessage(),
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    private function readAllBookings(): array
    {
        if (!file_exists($this->bookingsFile)) {
            return [];
        }

        $bookings = [];
        $file = fopen($this->bookingsFile, 'r');
        
        
        fgetcsv($file);
        
        while (($data = fgetcsv($file)) !== false) {
            if (count($data) === count(self::CSV_HEADERS)) {
                $bookings[] = array_combine(self::CSV_HEADERS, $data);
            }
        }
        
        fclose($file);
        return $bookings;
    }

    private function writeAllBookings(array $bookings): void
    {
        $file = fopen($this->bookingsFile, 'w');
        if (!$file) {
            throw new \RuntimeException("Failed to open file for writing");
        }

        try {
            
            fputcsv($file, self::CSV_HEADERS);
            
            
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking['id'],
                    $booking['phone'],
                    $booking['cottageId'],
                    $booking['comment'],
                    $booking['createdAt']
                ]);
            }
        } finally {
            fclose($file);
        }
    }

        public function createBooking(string $phone, int $cottageId, ?string $comment = null): bool
    {
        try {
            if (!$this->validatePhone($phone)) {
                throw new \InvalidArgumentException('Invalid phone format');
            }

            $booking = [
                'id' => Uuid::v4()->toRfc4122(),
                'phone' => $phone,
                'cottageId' => $cottageId,
                'comment' => $comment ?? '',
                'createdAt' => date('Y-m-d H:i:s')
            ];

            $this->appendToCsv($booking);
            return true;
        } catch (\Exception $e) {
            $this->logger->error('Create booking failed: ' . $e->getMessage());
            return false;
        }
    }

    private function appendToCsv(array $booking): void
    {
        $file = fopen($this->bookingsFile, 'a');
        if ($file) {
            fputcsv($file, $booking);
            fclose($file);
        }
    }

    private function validatePhone(string $phone): bool
    {
        return preg_match('/^\+?\d{10,15}$/', $phone);
    }

    public function getBooking(string $id): ?array
    {
        $bookings = $this->readAllBookings();
    
        foreach ($bookings as $booking) {
            if ($booking['id'] === $id) {
                return $booking;
            }
        }
    
        return null;
    }
}