<?php

namespace App\Services\Frontend;

use App\Models\FrontendContactMessage;

class ContactMessageService
{
    public function store(array $data): FrontendContactMessage
    {
        return FrontendContactMessage::query()->create([
            ...$data,
            'status' => 'new',
        ]);
    }
}

