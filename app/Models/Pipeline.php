<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $chat_id
 * @property string $command
 * @property int $stage
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Pipeline extends Model
{
    protected $fillable = [
        'chat_id',
        'command',
        'stage',
    ];
}
