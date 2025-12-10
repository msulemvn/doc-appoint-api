<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'price',
        'status',
        'notes',
        'payment_status',
        'payment_intent_id',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'status' => AppointmentStatus::class,
        'payment_status' => PaymentStatus::class,
    ];

    protected $with = ['patient.user', 'doctor.user'];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(PatientDetail::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(DoctorDetail::class, 'doctor_id');
    }
}
