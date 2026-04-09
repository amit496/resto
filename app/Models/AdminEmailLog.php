<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminEmailLog extends Model
{
    protected $fillable = [
        'subject',
        'body',
        'to_recipients',
        'cc_recipients',
        'bcc_recipients',
        'attachments',
        'status',
        'error_message',
        'sent_at',
        'sent_by',
    ];

    protected function casts(): array
    {
        return [
            'to_recipients' => 'array',
            'cc_recipients' => 'array',
            'bcc_recipients' => 'array',
            'attachments' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
