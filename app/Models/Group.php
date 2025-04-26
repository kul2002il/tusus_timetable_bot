<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $faculty
 * @property string $number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $days_count
 * @property int|null $subscriptions_count
 *
 * @property-read Collection|Day[] $days
 * @property-read Collection|Subscription[] $subscriptions
 */
class Group extends Model
{
    protected $fillable = [
        'faculty',
        'number',
    ];

    public function days(): HasMany
    {
        return $this->hasMany(Day::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
