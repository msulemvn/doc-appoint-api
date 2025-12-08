<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class DoctorDetail extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'doctors';

    protected $fillable = [
        'user_id',
        'specialization',
        'phone',
        'email',
        'bio',
        'years_of_experience',
        'consultation_fee',
        'license_number',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }
}
