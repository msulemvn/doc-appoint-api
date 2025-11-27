<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Doctor Appointment API",
 *     version="1.0.0",
 *     description="API documentation for Doctor Appointment application",
 *
 *     @OA\Contact(email="support@blogapi.com")
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter JWT Bearer token"
 * )
 *
 * @OA\Schema(
 *     schema="Doctor",
 *     type="object",
 *     required={"id", "name", "specialization", "email"},
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Dr. John Smith"),
 *     @OA\Property(property="specialization", type="string", example="Cardiology"),
 *     @OA\Property(property="email", type="string", format="email", example="doctor@example.com"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Patient",
 *     type="object",
 *     required={"id", "name", "email"},
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Jane Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="patient@example.com"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-15"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="Appointment",
 *     type="object",
 *     required={"id", "patient_id", "doctor_id", "appointment_date", "status"},
 *
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="patient_id", type="integer", example=1),
 *     @OA\Property(property="doctor_id", type="integer", example=1),
 *     @OA\Property(property="appointment_date", type="string", format="date-time", example="2025-12-01 10:00:00"),
 *     @OA\Property(property="status", type="string", enum={"pending", "confirmed", "cancelled", "completed"}, example="pending"),
 *     @OA\Property(property="notes", type="string", example="Follow-up consultation"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="patient", ref="#/components/schemas/Patient"),
 *     @OA\Property(property="doctor", ref="#/components/schemas/Doctor")
 * )
 *
 * @OA\Schema(
 *     schema="AvailableSlot",
 *     type="object",
 *     required={"doctor_id", "date", "start_time", "end_time", "available"},
 *
 *     @OA\Property(property="doctor_id", type="integer", example=1),
 *     @OA\Property(property="doctor_name", type="string", example="Dr. John Smith"),
 *     @OA\Property(property="specialization", type="string", example="Cardiology"),
 *     @OA\Property(property="date", type="string", format="date", example="2025-12-01"),
 *     @OA\Property(property="start_time", type="string", format="time", example="10:00:00"),
 *     @OA\Property(property="end_time", type="string", format="time", example="10:30:00"),
 *     @OA\Property(property="available", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="AppointmentRequest",
 *     type="object",
 *     required={"doctor_id", "appointment_date"},
 *
 *     @OA\Property(property="doctor_id", type="integer", example=1),
 *     @OA\Property(property="appointment_date", type="string", format="date-time", example="2025-12-01 10:00:00"),
 *     @OA\Property(property="notes", type="string", example="Follow-up consultation")
 * )
 *
 * @OA\Schema(
 *     schema="UpdateAppointmentStatus",
 *     type="object",
 *     required={"status"},
 *
 *     @OA\Property(property="status", type="string", enum={"confirmed", "cancelled", "completed"}, example="confirmed")
 * )
 *
 * @OA\Schema(
 *     schema="SuccessResponse",
 *     type="object",
 *
 *     @OA\Property(property="message", type="string", example="Operation successful"),
 *     @OA\Property(property="statusCode", type="integer", example=200),
 *     @OA\Property(property="status", type="string", example="OK")
 * )
 *
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *
 *     @OA\Property(property="message", type="string", example="Error message"),
 *     @OA\Property(property="statusCode", type="integer", example=400),
 *     @OA\Property(property="status", type="string", example="Bad Request")
 * )
 */
abstract class Controller
{
    use ApiResponseTrait;
}
