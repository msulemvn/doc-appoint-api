<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;

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
        'stripe_customer_id',
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
     * Get the array representation of the model.
     *
     * Converts role enum to string label for API responses.
     */
    public function toArray(): array
    {
        $array = parent::toArray();

        if (isset($array['role']) && $this->role instanceof UserRole) {
            $array['role'] = $this->role->label();
        }

        return $array;
    }

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
            'role' => UserRole::class,
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function patient(): HasOne
    {
        return $this->hasOne(PatientDetail::class);
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(DoctorDetail::class);
    }

    public function isDoctor(): bool
    {
        return $this->role === UserRole::DOCTOR;
    }

    public function isPatient(): bool
    {
        return $this->role === UserRole::PATIENT;
    }

    public function getDoctorNameAttribute(): string
    {
        if ($this->isDoctor()) {
            return 'Dr. '.$this->name;
        }

        return $this->name;
    }
}
