<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 
use App\Service\HomeDataService;
use App\Service\BookingService;

class BookingController
{
    public function __construct(
        private HomeDataService $homeDataService,
        private BookingService $bookingService
    ) {}

    #[Route('/api/cottages', name: 'api_cottages', methods: ['GET'])]
    public function getCottages(): JsonResponse
    {
        $cottages = $this->homeDataService->getAvailableCottages();
        return new JsonResponse($cottages);
    }

    #[Route('/api/bookings', name: 'api_bookings_create', methods: ['POST'])]
    public function createBooking(Request $request, BookingService $bookingService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        if (!isset($data['phone']) || !isset($data['cottageId'])) {
            return new JsonResponse(
                ['status' => 'error', 'message' => 'Missing required fields'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($data['cottageId'] <= 0) {
            return new JsonResponse(
                ['status' => 'error', 'message' => 'Invalid cottage ID'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $success = $bookingService->createBooking(
            $data['phone'],
            (int)$data['cottageId'],
            $data['comment'] ?? null
        );

        return new JsonResponse(
            ['status' => $success ? 'success' : 'error'],
            $success ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST
        );
    }

    #[Route('/api/bookings/{id}', name: 'api_bookings_update', methods: ['PUT'])]
    public function updateBooking(string $id, Request $request, BookingService $bookingService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $success = $bookingService->updateBooking($id, $data['comment'] ?? '');

        return new JsonResponse(
            ['status' => $success ? 'success' : 'error'],
            $success ? Response::HTTP_OK : Response::HTTP_NOT_FOUND
        );
    }

    #[Route('/api/bookings/{id}', name: 'api_booking_get', methods: ['GET'])]
    public function getBooking(string $id, BookingService $bookingService): JsonResponse
    {
        $booking = $bookingService->getBooking($id);
    
        if (!$booking) {
            return new JsonResponse(
                data: ['status' => 'error', 'message' => 'Booking not found'],
                status: Response::HTTP_NOT_FOUND
            );
        }

        return new JsonResponse($booking);
    }
}