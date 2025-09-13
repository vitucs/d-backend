<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property float $balance
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Wallet extends Model
{
    protected ?string $table = 'wallets';

    protected array $fillable = [
        'user_id',
        'balance',
    ];

    protected array $casts = [
        'id'         => 'integer',
        'user_id'    => 'integer',
        'balance'    => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}