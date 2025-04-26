<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $group_id
 * @property int $chat_id
 * @property string $options
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $groups_count
 *
 * @property-read Group|null $group
 */
class Subscription extends Model
{
    protected $fillable = [
        'group_id',
        'chat_id',
        'options',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
