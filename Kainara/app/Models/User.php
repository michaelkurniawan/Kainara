<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'profile_picture',
    ];


    /**
     * Atribut yang harus disembunyikan untuk serialisasi.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Mendapatkan atribut yang harus dicasting.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login' => 'datetime', // Casting last_login sebagai datetime
            'dob' => 'date', // Casting dob sebagai date
        ];
    }

    /**
     * Mendapatkan keranjang belanja yang terkait dengan pengguna.
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'admin_id');
    }


    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class, 'user_id');
    }

    public function hasVerifiedEmail()
    {
        return ! is_null($this->email_verified_at);
    }
}
