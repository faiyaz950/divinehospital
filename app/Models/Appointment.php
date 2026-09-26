<?php

namespace App\Models;

use App\Support\Clinic;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'preferred_date',
        'preferred_slot',
        'concern',
        'message',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
        ];
    }

    public function concernLabel(): ?string
    {
        return $this->concern ? (app(Clinic::class)->concerns()[$this->concern] ?? $this->concern) : null;
    }

    /** Chat link to the patient; 10-digit numbers are assumed to be Indian mobiles. */
    public function whatsappUrl(): string
    {
        $digits = preg_replace('/\D/', '', $this->phone);

        return 'https://wa.me/'.(strlen($digits) === 10 ? '91'.$digits : $digits);
    }

    /** @return array<string, string> */
    public static function statuses(): array
    {
        return [
            'new' => 'New',
            'confirmed' => 'Confirmed',
            'completed' => 'Visited',
            'cancelled' => 'Cancelled',
        ];
    }
}
