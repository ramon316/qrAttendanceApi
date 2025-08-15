<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'employee_id',
        'status',
        'verification_attempts',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'matricula', 'employee_id');
    }

    /* Verificamos si el usuario su matricula existe */
    public function isMatriculaValid()
    {
        return $this->employee()->exists();
    }

    /* Verificar si el usuario está activo */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /* Verificar si el usuario está pendiente de verificación */
    public function isPendingVerification()
    {
        return $this->status === 'pending_verification';
    }
}
