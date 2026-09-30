<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use App\Support\ContentSchema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(ContentSchema $schema): View
    {
        return view('admin.dashboard', [
            'groups' => $schema->grouped(),
            'updated' => SiteContent::pluck('updated_at', 'key'),
        ]);
    }
}
