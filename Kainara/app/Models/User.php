<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne; // Pastikan ini diimpor

class User extends Authenticatable
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
        'dob', // Menambahkan 'dob' ke fillable
        'role', // Penting: Menambahkan 'role' ke fillable agar bisa diisi
        'email_verified_at',
        'last_login',
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
}