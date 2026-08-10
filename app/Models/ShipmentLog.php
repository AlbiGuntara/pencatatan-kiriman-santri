<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $shipment_id
 * @property string|null $status_from
 * @property string $status_to
 * @property string|null $notes
 * @property string|null $attachment_path
 * @property int $logged_by
 * @property-read Shipment $shipment
 * @property-read User|null $logger
 * @property Carbon $created_at
 */
#[Fillable([
    'shipment_id',
    'status_from',
    'status_to',
    'notes',
    'attachment_path',
    'logged_by',
])]
class ShipmentLog extends Model
{
    public const UPDATED_AT = null;

    /** @return BelongsTo<Shipment, $this> */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    /** @return BelongsTo<User, $this> */
    public function logger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }
}
