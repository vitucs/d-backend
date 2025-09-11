<?php

namespace App\Model;

use Hyperf\Database\Model\Relations\HasOne;

/**
 * @property int $id
 * @property string $full_name
 * @property string $document
 * @property string $email
 * @property string $password
 * @property string $type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Wallet $wallet
 */
class User extends Model
{
    protected ?string $table = 'users';
    protected array $fillable = ['full_name', 'document', 'email', 'password', 'type'];
    protected array $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    protected array $hidden = ['password', 'created_at', 'updated_at'];
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }
}