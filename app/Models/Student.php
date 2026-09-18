<?php

namespace App\Models;

use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $id
 * @property string $nis
 * @property string $name
 * @property string $dormitory
 * @property string $location_type
 * @property-read Collection<int, Region> $regions
 * @property-read Collection<int, Shipment> $shipments
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['nis', 'name', 'dormitory', 'location_type'])]
class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory;

    /** @return HasMany<Shipment, $this> */
    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    /** @return BelongsToMany<Region, $this> */
    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'iksass_students', 'student_id', 'region_iksass_id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope('region', function (Builder $builder) {
            $user = Auth::user();

            if ($user && $user->role === User::ROLE_IKSASS_ADMIN && $user->region_iksass_id) {
                $builder->whereHas('regions', function ($query) use ($user) {
                    $query->where('regions.id', $user->region_iksass_id);
                });
            }
        });
    }
}
