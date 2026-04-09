<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\ContactMessageService;
use App\Services\Frontend\FrontendPageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __construct(
        private readonly FrontendPageService $pageService,
        private readonly ContactMessageService $messageService
    ) {
    }

    public function create(): View
    {
        return view('frontend.contact', $this->pageService->siteData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $this->messageService->store($data);

        return redirect()
            ->route('frontend.contact')
            ->with('success', 'Your message has been submitted. Our team will contact you shortly.');
    }
}

