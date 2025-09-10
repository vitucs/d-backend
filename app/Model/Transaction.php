<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $payer_id
 * @property int $payee_id
 * @property float $amount
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $payer
 * @property-read User $payee
 */
class Transaction extends Model
{
    protected ?string $table = 'transactions';

    protected array $fillable = ['payer_id', 'payee_id', 'amount', 'status'];

    protected array $casts = [
        'id' => 'integer',
        'payer_id' => 'integer',
        'payee_id' => 'integer',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function payee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payee_id');
    }
}