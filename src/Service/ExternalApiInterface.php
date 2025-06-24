<?php

namespace App\Service;

interface ExternalApiInterface
{
    /**
     * Получить список домиков из внешнего API
     */
    public function getCottages(): array;

    /**
     * Создать бронирование через внешнее API
     */
    public function createBooking(array $data): bool;
}