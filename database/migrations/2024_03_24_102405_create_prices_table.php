<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Price;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->boolean('is_weekend')->nullable();
            $table->boolean('is_default')->nullable();
            $table->integer('price')->nullable(false);
            $table->string('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        if (Schema::hasTable('prices')) {
            Price::insert([
                [
                    'start_at' => Carbon::parse('2024-06-01')->format('Y-m-d'),
                    'end_at' => Carbon::parse('2024-09-30')->format('Y-m-d'),
                    'is_weekend' => null,
                    'is_default' => null,
                    'price' => 200,
                    'description' => 'Summer time markup',
                    'created_at' => Carbon::now(),
                    'updated_at'  => Carbon::now()
                ],
                [
                    'start_at' => Carbon::parse('2024-11-01')->format('Y-m-d'),
                    'end_at' => Carbon::parse('2025-01-31')->format('Y-m-d'),
                    'is_weekend' => null,
                    'is_default' => null,
                    'price' => 50,
                    'description' => 'Winter time markup',
                    'created_at' => Carbon::now(),
                    'updated_at'  => Carbon::now()
                ],
                [
                    'start_at' => null,
                    'end_at' => null,
                    'is_weekend' => 1,
                    'is_default' => null,
                    'price' => 300,
                    'description' => 'Weekend time markup',
                    'created_at' => Carbon::now(),
                    'updated_at'  => Carbon::now()
                ],
                [
                    'start_at' => null,
                    'end_at' => null,
                    'is_weekend' => null,
                    'is_default' => 1,
                    'price' => 100,
                    'description' => 'Default price',
                    'created_at' => Carbon::now(),
                    'updated_at'  => Carbon::now()
                ]
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
