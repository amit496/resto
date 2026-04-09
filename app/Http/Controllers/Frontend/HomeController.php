<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\FrontendPageService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(private readonly FrontendPageService $service)
    {
    }

    public function __invoke(): View
    {
        return view('frontend.home', $this->service->homeData());
    }
}

