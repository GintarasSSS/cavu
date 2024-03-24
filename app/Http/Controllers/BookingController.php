<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Interfaces\BookingRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class BookingController extends Controller
{
    private $repository;

    public function __construct(BookingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function index(BookingRequest $request): JsonResponse
    {
        return response()->json(
            [
                'status' => 'success',
                'data' => $this->repository->getAvailability($request)
            ],
            Response::HTTP_OK
        );
    }

    public function show()
    {
        $booking = $this->repository->getBooking();

        return response()->json(
            [
                'status' => !$booking ? 'failed' : 'success',
                'message' => !$booking ? 'There are not available bookings.' : '',
                'data' => $booking
            ],
            !$booking ? Response::HTTP_BAD_REQUEST : Response::HTTP_OK
        );
    }

    public function store(BookingRequest $request): JsonResponse
    {

        return $this->executeBooking('createBooking', Response::HTTP_CREATED, __FUNCTION__, $request);
    }

    public function update(BookingRequest $request): JsonResponse
    {
        return $this->executeBooking('updateBooking', Response::HTTP_OK, __FUNCTION__, $request);
    }

    public function destroy(): JsonResponse
    {
        return $this->executeBooking('deleteBooking', Response::HTTP_OK, __FUNCTION__);
    }

    private function executeBooking($callback, $response, $functionName, $request = null): JsonResponse
    {
        try {
            $this->repository->$callback($request);

            return response()->json(
                ['status' => 'success'],
                $response
            );
        } catch (BadRequestException $e) {
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $e->getMessage()
                ],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Exception $e) {
            Log::error(__CLASS__ . '::' . $functionName . '::' . $e->getMessage());

            return response()->json(
                ['status' => 'failed'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
