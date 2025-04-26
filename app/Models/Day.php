<?php

namespace App\Models;

use App\Models\DTO\DayScheduleDTO;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $group_id
 * @property mixed $date
 * @property DayScheduleDTO $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $groups_count
 *
 * @property-read \App\Models\Group|null $group
 */
class Day extends Model
{
    protected $fillable = [
        'group_id',
        'date',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    protected function body(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => DayScheduleDTO::fromArray(json_decode($value, true)),
            set: fn (DayScheduleDTO $value) => json_encode($value, JSON_UNESCAPED_UNICODE),
        );
    }
}
