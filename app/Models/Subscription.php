<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $chat_id
 * @property int $group_id
 * @property string $options
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $groups_count
 *
 * @property-read \App\Models\Group|null $group
 */
class Subscription extends Model
{
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
