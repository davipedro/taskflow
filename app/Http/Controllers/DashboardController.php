<?php

namespace App\Http\Controllers;

use App\Actions\Dashboard\GetDashboardDataAction;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private GetDashboardDataAction $getDashboardDataAction
    ) {}

    public function index(): Response
    {
        $data = $this->getDashboardDataAction->handle(auth()->id());

        return Inertia::render('Dashboard', $data);
    }
}
