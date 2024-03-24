<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Booking;
use App\Models\Place;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Http\Response;

class IntegrationTest extends TestCase
{
    use RefreshDatabase;

    const TEST_USER = [
        'name' => 'TestName',
        'email' => 'test.test@test.co.uk',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ];

    /**
     * @dataProvider endPointRoutes
     */
    public function testEndPointsReturnCodesByCallingWithoutToken($url, $method, $params, $code)
    {
        $response = $this->json($method, $url, $params);

        $response->assertStatus($code);
    }

    public function testUserWasCreated()
    {

        $response = $this->json(
            'post',
            '/api/register',
            self::TEST_USER
        );

        $this->assertEquals('success', $response->json()['status']);

        $response = $this->json(
            'post',
            '/api/login',
            self::TEST_USER
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testCreatedBooking()
    {
        $this->seed();

        $user = $this->createUser();

        $response = $this->json(
            'post',
            '/api/booking',
            [
                'start_at' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'end_at' => Carbon::now()->addDays(10)->format('Y-m-d')
            ],
            [
                'Authorization' => 'Bearer ' . $user['token'],
                'Accept'        => 'application/json'
            ]
        );

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('bookings', ['user_id' => $user['id'], 'deleted_at' => null]);
    }

    public function testAbleToSeeBooking()
    {
        $this->seed();

        $user = $this->createUser();

        $place = Place::factory()->count(1)->create();

        Booking::create([
            'user_id' => $user['id'],
            'place_id' => $place->first()->id,
            'start_at' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'end_at' => Carbon::now()->addDays(10)->format('Y-m-d')
        ]);

        $response = $this->json(
            'get',
            '/api/booking/details',
            [],
            [
                'Authorization' => 'Bearer ' . $user['token'],
                'Accept'        => 'application/json'
            ]
        );

        $response->assertStatus(Response::HTTP_OK);
        $this->assertNotEmpty($response->json()['data']);
    }

    public function testAbleToDeleteBooking()
    {
        $user = $this->createUser();

        $place = Place::factory()->count(1)->create();

        Booking::create([
            'user_id' => $user['id'],
            'place_id' => $place->first()->id,
            'start_at' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'end_at' => Carbon::now()->addDays(10)->format('Y-m-d')
        ]);

        $response = $this->json(
            'delete',
            '/api/booking',
            [],
            [
                'Authorization' => 'Bearer ' . $user['token'],
                'Accept'        => 'application/json'
            ]
        );

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('bookings', ['user_id' => $user['id'], 'deleted_at' => null]);
    }

    public function testAbleToUpdateBooking()
    {
        $firstBooking = [
            'start_at' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'end_at' => Carbon::now()->addDays(10)->format('Y-m-d')
        ];

        $secondBooking = [
            'start_at' => Carbon::now()->addDays(6)->format('Y-m-d'),
            'end_at' => Carbon::now()->addDays(8)->format('Y-m-d')
        ];

        $user = $this->createUser();

        $place = Place::factory()->count(1)->create();

        Booking::create(
            array_merge(
                [
                    'user_id' => $user['id'],
                    'place_id' => $place->first()->id
                ],
                $firstBooking
            )
        );

        $response = $this->json(
            'put',
            '/api/booking',
            $secondBooking,
            [
                'Authorization' => 'Bearer ' . $user['token'],
                'Accept'        => 'application/json'
            ]
        );

        $response->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseMissing(
            'bookings',
            array_merge(
                [
                    'user_id' => $user['id'],
                    'deleted_at' => null
                ],
                $firstBooking
            )
        );

        $this->assertDatabaseHas(
            'bookings',
            array_merge(
                [
                    'user_id' => $user['id'],
                    'deleted_at' => null
                ],
                $secondBooking
            )
        );
    }

    static function endPointRoutes(): array
    {
        return [
            'get booking details' => [
                'url' => '/api/booking/details',
                'method' => 'get',
                'params' => [],
                'code' => Response::HTTP_UNAUTHORIZED
            ],
            'create new booking' => [
                'url' => '/api/booking',
                'method' => 'post',
                'params' => [],
                'code' => Response::HTTP_UNAUTHORIZED
            ],
            'update booking' => [
                'url' => '/api/booking',
                'method' => 'put',
                'params' => [],
                'code' => Response::HTTP_UNAUTHORIZED
            ],
            'cancel booking' => [
                'url' => '/api/booking',
                'method' => 'delete',
                'params' => [],
                'code' => Response::HTTP_UNAUTHORIZED
            ],
            'get available bookings' => [
                'url' => '/api/booking/available',
                'method' => 'get',
                'params' => [
                    'start_at' => Carbon::now()->addDays(3)->format('Y-m-d'),
                    'end_at' => Carbon::now()->addDays(10)->format('Y-m-d')
                ],
                'code' => Response::HTTP_OK
            ],
        ];
    }

    private function createUser(): array
    {
        $response = $this->json(
            'post',
            '/api/register',
            self::TEST_USER
        );

        return [
            'token' => $response->json()['data']['token'],
            'id' => $response->json()['data']['user']['id']
        ];
    }
}
