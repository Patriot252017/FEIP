<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\BookingService;
use App\Service\HomeDataService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookingController
{
    public function __construct(
        private readonly HomeDataService $homeDataService,
        private readonly BookingService $bookingService,
    ) {
    }

    #[Route('/api/cottages', name: 'api_cottages', methods: ['GET'])]
    public function getCottages(): JsonResponse
    {
        return new JsonResponse($this->homeDataService->getAvailableCottages());
    }

    #[Route('/api/bookings', name: 'api_bookings_create', methods: ['POST'])]
    public function createBooking(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (!isset($data['phone'], $data['cottageId'])) {
            return new JsonResponse(
                ['status' => 'error', 'message' => 'Missing required fields'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $success = $this->bookingService->createBooking(
            (string)$data['phone'],
            (int)$data['cottageId'],
            $data['comment'] ?? null
        );

        return new JsonResponse(
            ['status' => $success ? 'success' : 'error'],
            $success ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST
        );
    }

    #[Route('/api/bookings/{id}', name: 'api_bookings_update', methods: ['PUT'])]
    public function updateBooking(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $success = $this->bookingService->updateBooking($id, $data['comment'] ?? '');

        return new JsonResponse(
            ['status' => $success ? 'success' : 'error'],
            $success ? Response::HTTP_OK : Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/api/bookings/{id}', name: 'api_booking_get', methods: ['GET'])]
    public function getBooking(string $id): JsonResponse
    {
        $booking = $this->bookingService->getBooking($id);

        if ($booking === null) {
            return new JsonResponse(
                ['status' => 'error', 'message' => 'Booking not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse($booking);
    }
}
