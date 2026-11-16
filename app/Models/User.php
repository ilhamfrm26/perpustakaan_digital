<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'username', 'password', 'role', 'kelas'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['password' => 'hashed'];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'petugas'], true);
    }

    public function roleLabel(): string
    {
        return ['admin' => 'Admin', 'petugas' => 'Petugas', 'pelajar' => 'Pelajar'][$this->role] ?? $this->role;
    }
}
