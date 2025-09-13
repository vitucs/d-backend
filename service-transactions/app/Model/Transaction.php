<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Model;

/**
 * @property int $id
 * @property int $payer_id ID do pagador
 * @property int $payee_id ID do recebedor
 * @property float $amount
 * @property string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Transaction extends Model
{
    protected ?string $table = 'transactions';

    protected array $fillable = [
        'payer_id',
        'payee_id',
        'amount',
        'status',
    ];

    protected array $casts = [
        'id'         => 'integer',
        'payer_id'   => 'integer',
        'payee_id'   => 'integer',
        'amount'     => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}