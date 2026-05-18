<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInternCalendarDayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->session()->has('intern_id');
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'am_in' => ['nullable', 'date_format:H:i'],
            'am_out' => ['nullable', 'date_format:H:i'],
            'pm_in' => ['nullable', 'date_format:H:i'],
            'pm_out' => ['nullable', 'date_format:H:i'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'am_in' => $this->filled('am_in') ? $this->string('am_in')->toString() : null,
            'am_out' => $this->filled('am_out') ? $this->string('am_out')->toString() : null,
            'pm_in' => $this->filled('pm_in') ? $this->string('pm_in')->toString() : null,
            'pm_out' => $this->filled('pm_out') ? $this->string('pm_out')->toString() : null,
        ]);
    }
}
