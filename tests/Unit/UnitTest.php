<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Place;
use App\Models\Price;
use App\Models\User;
use App\Repositories\BookingRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tests\TestCase;

class UnitTest extends TestCase
{
    private $carbon;
    private $price;
    private $place;
    private $booking;
    private $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->carbon = new Carbon();
        $this->price = \Mockery::mock('alias:' . Price::class);
        $this->place = \Mockery::mock('alias:' . Place::class);
        $this->booking = \Mockery::mock('alias:' . Booking::class);

        $this->repository = new BookingRepository(
            $this->carbon,
            $this->price,
            $this->place,
            $this->booking
        );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testBookingRepositoryGetPlace(): void
    {
        $request = Request::create('/', 'GET');

        $this->place->shouldReceive('with')->with(\Mockery::on(function ($argument) {
            return is_array($argument) && !empty($argument['bookings']);
        }))->once()->andReturnSelf();

        $this->place->shouldReceive('get')->once()->andReturnSelf();
        $this->place->shouldReceive('first')->once()->andReturnSelf();
        $this->place->shouldReceive('toArray')->once()->andReturn([]);

        $this->assertIsArray($this->repository->getPlace($request));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testBookingRepositoryGetBooking()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->booking->shouldReceive('where')->once()->with('user_id', $user->id)->andReturnSelf();
        $this->booking->shouldReceive('where')->with(\Mockery::on(function ($argument) {
            return is_callable($argument);
        }))->once()->andReturnSelf();
        $this->booking->shouldReceive('with')->with(['place'])->once()->andReturnSelf();
        $this->booking->shouldReceive('get')->once()->andReturnSelf();
        $this->booking->shouldReceive('first')->once()->andReturn('returnParameter');

        $this->assertEquals('returnParameter', $this->repository->getBooking());
    }
}
