<?php

namespace App\Models;

use Database\Factories\ShipmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $tracking_code
 * @property int $student_id
 * @property int $region_iksass_id
 * @property string $sender_name
 * @property string|null $sender_phone
 * @property string|null $location_type
 * @property string $amount_money
 * @property int $package_count
 * @property string $status
 * @property int $created_by
 * @property-read Student|null $student
 * @property-read Region|null $region
 * @property-read User|null $creator
 * @property-read Collection<int, ShipmentLog> $logs
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'tracking_code',
    'student_id',
    'region_iksass_id',
    'sender_name',
    'sender_phone',
    'location_type',
    'amount_money',
    'package_count',
    'status',
    'created_by',
])]
class Shipment extends Model
{
    /** @use HasFactory<ShipmentFactory> */
    use HasFactory;

    public const STATUS_CREATED = 'created';

    public const STATUS_IN_TRANSIT = 'in_transit';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUSES = [
        self::STATUS_CREATED => 'Dibuat (Draft)',
        self::STATUS_IN_TRANSIT => 'Dalam Perjalanan',
        self::STATUS_DELIVERED => 'Tiba & Diserahkan',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('region', function (Builder $builder) {
            /** @var User|null $user */
            $user = Auth::user();

            if ($user && $user->role === User::ROLE_IKSASS_ADMIN && $user->region_iksass_id) {
                $builder->where('region_iksass_id', $user->region_iksass_id);
            }
        });
    }

    /** @return BelongsTo<Student, $this> */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /** @return BelongsTo<Region, $this> */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'region_iksass_id');
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return HasMany<ShipmentLog, $this> */
    public function logs(): HasMany
    {
        return $this->hasMany(ShipmentLog::class)->orderBy('created_at')->orderBy('id');
    }

    /**
     * Generate a unique tracking code.
     */
    public static function generateTrackingCode(?Region $region = null): string
    {
        do {
            $code = Str::upper((string) Str::random(6));

            if ($region) {
                $code = strtoupper((string) $region->code).'-'.$code;
            } else {
                $code = 'SPR-'.$code;
            }
        } while (static::query()->where('tracking_code', $code)->exists());

        return $code;
    }
}
