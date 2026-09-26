<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Mail\AppointmentRequested;
use App\Models\Appointment;
use App\Support\Clinic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class AppointmentController extends Controller
{
    public function store(StoreAppointmentRequest $request, Clinic $clinic): RedirectResponse
    {
        $appointment = Appointment::create([
            ...$request->safe()->except('website'),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
        ]);

        if ($recipient = $clinic->notifyEmail()) {
            try {
                Mail::to($recipient)->send(new AppointmentRequested($appointment));
            } catch (Throwable $e) {
                // The request is already saved — never fail the patient's booking because email is down.
                report($e);
            }
        }

        return back()
            ->withFragment('appointment')
            ->with('appointment', [
                'name' => Str::before($appointment->name, ' '),
                'phone' => $appointment->phone,
                'whatsapp_message' => $this->whatsappMessage($appointment),
            ]);
    }

    private function whatsappMessage(Appointment $appointment): string
    {
        return collect([
            'Hello '.site('settings.identity.brand').', I have requested an appointment.',
            "Name: {$appointment->name}",
            "Phone: {$appointment->phone}",
            $appointment->preferred_date ? 'Preferred date: '.$appointment->preferred_date->format('d M Y') : null,
            $appointment->preferred_slot ? 'Preferred time: '.ucfirst($appointment->preferred_slot) : null,
            $appointment->concernLabel() ? "Concern: {$appointment->concernLabel()}" : null,
        ])->filter()->implode("\n");
    }
}
