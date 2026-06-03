<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    protected $table = 'admin';
    protected $primaryKey = 'admin_id';

    protected $fillable = [
        'admin_username',
        'admin_password',
    ];

    protected $hidden = [
        'admin_password',
    ];

    /**
     * Override password column name for Laravel Auth.
     */
    public function getAuthPassword(): string
    {
        return $this->admin_password;
    }

    // ─── JWT Interface ───────────────────────────────────────────────────────

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
