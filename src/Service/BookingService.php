<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Booking;
use App\Repository\BookingRepository;
use App\Repository\CottageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final class BookingService
{
    /** @psalm-suppress PossiblyUnusedMethod */
    public function __construct(
        private BookingRepository $bookingRepository,
        private CottageRepository $cottageRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {
    }

    public function createBooking(string $phone, int $cottageId, ?string $comment = null): bool
    {
        $cottage = $this->cottageRepository->find($cottageId);

        if (!$cottage) {
            $this->logger->error('Cottage not found', ['cottageId' => $cottageId]);

            return false;
        }

        $booking = new Booking($cottage, $phone);
        $booking->setComment($comment);

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return true;
    }

    public function updateBooking(string $id, string $newComment): bool
    {
        $booking = $this->bookingRepository->find($id);

        if (!$booking) {
            $this->logger->warning('Booking not found', ['id' => $id]);

            return false;
        }

        $booking->setComment($newComment);
        $this->entityManager->flush();

        return true;
    }

    public function getBooking(string $id): ?array
    {
        $booking = $this->bookingRepository->find($id);

        if (!$booking) {
            return null;
        }

        return [
            'id' => $booking->getId(),
            'phone' => $booking->getPhone(),
            'cottageId' => $booking->getCottage()->getId(),
            'comment' => $booking->getComment(),
            'createdAt' => $booking->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
