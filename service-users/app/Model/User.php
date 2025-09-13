<?php

declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\Relations\HasOne;
use App\Model\Wallet;

/**
 * @property int $id
 * @property string $full_name
 * @property string $document CPF ou CNPJ
 * @property string $email
 * @property string $password
 * @property string $type common ou shopkeeper
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Wallet $wallet
 */
class User extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'users';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'full_name',
        'document',
        'email',
        'password',
        'type',
    ];

    protected array $hidden = [
        'password',
    ];

    protected array $casts = [
        'id'         => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class); // Supondo que Wallet exista para fins de tipagem.
    }
}