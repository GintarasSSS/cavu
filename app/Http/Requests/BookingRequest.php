<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_at' => 'required|date|date_format:Y-m-d|after:tomorrow',
            'end_at' => 'required|date|date_format:Y-m-d|after:start_at'
        ];
    }
}
