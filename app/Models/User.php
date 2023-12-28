<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int           $id
 * @property string        $name
 * @property string        $email
 * @property null|string   $email_verified_at
 * @property string        $password
 * @property null|string   $remember_token
 * @property null|DateTime $created_at
 * @property null|DateTime $updated_at
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    /** @var string[] */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
