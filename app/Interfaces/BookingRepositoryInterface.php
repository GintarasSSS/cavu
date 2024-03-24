<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface BookingRepositoryInterface
{
    public function getAvailability(Request $request): array;
    public function getBooking();
    public function createBooking(Request $request);
    public function updateBooking(Request $request);
    public function deleteBooking();
}
