<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Services\Frontend\FrontendPageService;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function __construct(private readonly FrontendPageService $service)
    {
    }

    public function index(): View
    {
        return view('frontend.branches.index', $this->service->branchData());
    }

    public function show(Branch $branch): View
    {
        return view('frontend.branches.show', $this->service->branchDetailData($branch));
    }
}

