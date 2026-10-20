<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory;

    /** Povolené stavy zařízení: kód => český popis. */
    public const STATUSES = [
        'active' => 'Aktivní',
        'service' => 'V servisu',
        'retired' => 'Vyřazeno',
    ];

    protected $table = 'equipment';

    protected $fillable = [
        'name',
        'inventory_number',
        'status',
        'purchased_at',
    ];

    protected function casts(): array
    {
        return [
            'purchased_at' => 'date',
        ];
    }

    public function serviceRecords(): HasMany
    {
        return $this->hasMany(ServiceRecord::class)
            ->orderByDesc('serviced_at')
            ->orderByDesc('id');
    }

    /** Český textový popis stavu. */
    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    /** Celkové náklady na servis tohoto zařízení. */
    public function totalServiceCost(): float
    {
        return (float) $this->serviceRecords()->sum('cost');
    }
}
