<?php

namespace App\Repositories;

use App\Interfaces\BookingRepositoryInterface;
use App\Models\Booking;
use App\Models\Place;
use App\Models\Price;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class BookingRepository implements BookingRepositoryInterface
{
    public function getAvailability(Request $request): array
    {
        $startDate = Carbon::parse($request->start_at);
        $endDate = Carbon::parse($request->end_at);

        $model = null;

        $prices = Price::all();

        while ($startDate->lte($endDate)) {
            $date = $startDate->toDateString();
            $datePrice = 0;

            foreach ($prices as $price) {
                if ($price->is_default) {
                    $datePrice += $price->price;
                }

                if ($price->is_weekend && $startDate->isWeekend()) {
                    $datePrice += $price->price;
                }

                if ($price->start_at && $price->end_at) {
                    $priceStart = Carbon::parse($price->start_at);
                    $priceEnd = Carbon::parse($price->end_at);

                    if ($startDate->gte($priceStart) && $startDate->lte($priceEnd)) {
                        $datePrice += $price->price;
                    }
                }
            }

            $datePrice /= 100;

            $query = Place::query()
                ->selectRaw("? as date", [$date])
                ->selectRaw('COUNT(places.id) as available')
                ->selectRaw('? as price', [$datePrice])
                ->whereNotIn('id',
                    Booking::query()
                        ->select('place_id')
                        ->whereDate('start_at', '<=', $date)
                        ->whereDate('end_at', '>=', $date)
                );

            if (!$model) {
                $model = $query;
            } else {
                $model->union($query);
            }

            $startDate->addDay();
        }

        return $model->get() ? $model->get()->toArray() : [];
    }

    public function getBooking()
    {
        $currentDate = Carbon::now()->toDateString();

        return Booking::where('user_id', auth()->user()->id)->where(function ($q) use ($currentDate) {
            $q->where('start_at', '>', $currentDate)->orWhere('end_at', '>', $currentDate);
        })->with(['place'])->get()->first();
    }

    private function getPlace(Request $request): array
    {
        $place = Place::with(['bookings' => function ($q) use ($request) {
            $q->whereNotBetween('start_at', [$request->start_at, $request->end_at])
                ->whereNotBetween('end_at', [$request->start_at, $request->end_at]);
        }])->get()->first();

        return $place ? $place->toArray() : [];
    }

    public function createBooking(Request $request)
    {
        if ($this->getBooking()) {
            throw new BadRequestException('Customer has already valid booking.');
        }

        if (!($place = $this->getPlace($request))) {
            throw new BadRequestException('There are not available places for selected days.');
        }

        Booking::create([
            'user_id' => auth()->user()->id,
            'place_id' => $place['id'],
            'start_at' => $request->start_at,
            'end_at' => $request->end_at
        ]);
    }

    public function updateBooking(Request $request)
    {
        $this->deleteBooking();
        $this->createBooking($request);
    }

    public function deleteBooking()
    {
        if (!($booking = $this->getBooking())) {
            throw new BadRequestException('There are no available bookings.');
        }

        $booking->delete();
    }
}
