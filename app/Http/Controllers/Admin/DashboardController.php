<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\SiteContent;
use App\Support\ContentSchema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(ContentSchema $schema): View
    {
        $today = now(config('clinic.timezone'))->startOfDay();

        return view('admin.dashboard', [
            'stats' => [
                'new' => Appointment::where('status', 'new')->count(),
                'today' => Appointment::where('created_at', '>=', $today->copy()->utc())->count(),
                'week' => Appointment::where('created_at', '>=', $today->copy()->subDays(6)->utc())->count(),
                'total' => Appointment::count(),
            ],
            'latest' => Appointment::latest()->limit(6)->get(),
            'groups' => $schema->grouped(),
            'updated' => SiteContent::pluck('updated_at', 'key'),
        ]);
    }
}
