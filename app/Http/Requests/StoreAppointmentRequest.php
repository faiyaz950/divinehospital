<?php

namespace App\Http\Requests;

use App\Support\Clinic;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'phone' => ['required', 'string', 'regex:/^(?:\+?91)?[6-9]\d{9}$/'],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:'.now(config('clinic.timezone'))->toDateString()],
            'preferred_slot' => ['nullable', Rule::in(['morning', 'evening'])],
            'concern' => ['nullable', Rule::in(array_keys(app(Clinic::class)->concerns()))],
            'message' => ['nullable', 'string', 'max:1000'],
            'website' => ['prohibited'], // honeypot — real visitors never see this field
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please tell us the patient’s name.',
            'phone.required' => 'Please enter a mobile number so we can confirm your slot.',
            'phone.regex' => 'Please enter a valid 10-digit Indian mobile number.',
            'preferred_date.after_or_equal' => 'Please choose today or a future date.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'phone' => preg_replace('/[\s\-().]/', '', (string) $this->input('phone')),
        ]);
    }

    /** Send visitors back to the form itself (not the top of the page) when validation fails. */
    protected function getRedirectUrl(): string
    {
        return strtok(parent::getRedirectUrl(), '#').'#appointment';
    }
}
