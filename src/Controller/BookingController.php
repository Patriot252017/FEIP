<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\HomeDataService;
use App\Service\BookingService;
use App\Service\ExternalApiInterface;
class BookingController
{
    public function __construct(
        private HomeDataService $homeDataService,
        private BookingService $bookingService
    ) {}

    #[Route('/api/cottages/external', name: 'api_cottages_external', methods: ['GET'])]
    public function getExternalCottages(ExternalApiInterface $externalApi): JsonResponse
    {
        try {
            $cottages = $externalApi->getCottages();
            return new JsonResponse($cottages);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['status' => 'error', 'message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/api/bookings/external', name: 'api_bookings_external', methods: ['POST'])]
    public function createExternalBooking(Request $request, ExternalApiInterface $externalApi): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        try {
            $success = $externalApi->createBooking([
                'phone' => $data['phone'],
                'cottageId' => $data['cottageId'],
                'comment' => $data['comment'] ?? null
            ]);
            
            return new JsonResponse(
                ['status' => $success ? 'success' : 'error'],
                $success ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                ['status' => 'error', 'message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/api/cottages', name: 'api_cottages', methods: ['GET'])]
    public function getCottages(): JsonResponse
    {
        try {
            $cottages = $this->homeDataService->getAvailableCottages();
            return new JsonResponse($cottages);
        } catch (\RuntimeException $e) {
            return new JsonResponse(
                ['status' => 'error', 'message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/api/bookings', name: 'api_bookings_create', methods: ['POST'])]
    public function createBooking(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $errors = $this->validateBookingData($data);
            if (!empty($errors)) {
                return new JsonResponse(
                    ['status' => 'error', 'errors' => $errors],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $cottageId = (int)$data['cottageId'];
            
            if (!$this->homeDataService->cottageExists($cottageId)) {
                return new JsonResponse(
                    ['status' => 'error', 'message' => 'Cottage not found'],
                    Response::HTTP_NOT_FOUND
                );
            }

            $success = $this->bookingService->createBooking(
                $data['phone'],
                $cottageId,
                $data['comment'] ?? null
            );

            if (!$success) {
                throw new \RuntimeException('Failed to create booking');
            }

            return new JsonResponse(
                ['status' => 'success'],
                Response::HTTP_CREATED
            );
            
        } catch (\Exception $e) {
            return new JsonResponse(
                ['status' => 'error', 'message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/api/bookings/{id}', name: 'api_bookings_update', methods: ['PUT'])]
    public function updateBooking(string $id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['comment'])) {
                return new JsonResponse(
                    ['status' => 'error', 'message' => 'Comment is required'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $success = $this->bookingService->updateBooking($id, $data['comment']);
            
            return new JsonResponse(
                ['status' => $success ? 'success' : 'error'],
                $success ? Response::HTTP_OK : Response::HTTP_NOT_FOUND
            );
            
        } catch (\Exception $e) {
            return new JsonResponse(
                ['status' => 'error', 'message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/api/bookings/{id}', name: 'api_booking_get', methods: ['GET'])]
    public function getBooking(string $id): JsonResponse
    {
        try {
            $booking = $this->bookingService->getBooking($id);
            
            if (!$booking) {
                return new JsonResponse(
                    ['status' => 'error', 'message' => 'Booking not found'],
                    Response::HTTP_NOT_FOUND
                );
            }

            return new JsonResponse($booking);
            
        } catch (\Exception $e) {
            return new JsonResponse(
                ['status' => 'error', 'message' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function validateBookingData(array $data): array
    {
        $errors = [];
        
        if (empty($data['phone'])) {
            $errors['phone'] = 'Phone number is required';
        } elseif (!preg_match('/^\+?\d{10,15}$/', $data['phone'])) {
            $errors['phone'] = 'Invalid phone number format';
        }
        
        if (!isset($data['cottageId'])) {
            $errors['cottageId'] = 'Cottage ID is required';
        } elseif (!is_numeric($data['cottageId']) || $data['cottageId'] <= 0) {
            $errors['cottageId'] = 'Cottage ID must be a positive number';
        }
        
        if (isset($data['comment']) && strlen($data['comment']) > 500) {
            $errors['comment'] = 'Comment must be less than 500 characters';
        }
        
        return $errors;
    }
}