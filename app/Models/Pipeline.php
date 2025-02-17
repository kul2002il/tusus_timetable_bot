<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $chat_id
 * @property string $command
 * @property int $stage
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Pipeline extends Model
{
    protected $fillable = [
        'chat_id',
        'command',
        'stage',
    ];
}
