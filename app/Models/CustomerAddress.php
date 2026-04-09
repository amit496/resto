<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    protected $fillable = [
        'customer_id',
        'type',
        'label',
        'recipient_name',
        'phone',
        'address_line_1',
        'address_line_2',
        'landmark',
        'city',
        'state',
        'postal_code',
        'country_code',
        'instructions',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function displayLabel(): string
    {
        $label = trim((string) ($this->label ?: $this->type));

        return $label !== '' ? $label : 'Address';
    }

    public function formatted(): string
    {
        $parts = array_filter([
            trim((string) $this->address_line_1),
            trim((string) $this->address_line_2),
            trim((string) $this->landmark ? ('Landmark: '.$this->landmark) : ''),
            trim((string) $this->city),
            trim((string) $this->state),
            trim((string) $this->postal_code),
        ], static fn ($value) => $value !== '');

        return implode(', ', $parts);
    }
}

