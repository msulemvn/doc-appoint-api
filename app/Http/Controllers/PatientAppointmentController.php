<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Http\Requests\DeleteAppointmentRequest;
use App\Http\Requests\GetAppointmentsRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentStatusRequest;
use App\Http\Requests\ViewAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class PatientAppointmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/appointments",
     *     tags={"Appointments"},
     *     summary="Get all appointments for authenticated user (role-based view)",
     *     description="Returns appointments based on user role. Patients see their booked appointments with doctor details. Doctors see their scheduled appointments with patient details.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter appointments by status",
     *
     *         @OA\Schema(type="string", enum={"pending", "confirmed", "cancelled", "completed"})
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Appointments retrieved successfully. Response structure varies by role: patients see doctor info, doctors see patient info.",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Appointments retrieved successfully"),
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="string", example="OK"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Array of appointments. Each appointment shows either doctor (for patients) or patient (for doctors), never both.",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="appointment_date", type="string", format="date-time", example="2025-12-01 10:00:00"),
     *                     @OA\Property(property="status", type="string", example="pending"),
     *                     @OA\Property(property="notes", type="string", example="Follow-up consultation"),
     *                     @OA\Property(
     *                         property="doctor",
     *                         type="object",
     *                         description="Only present for patient users",
     *                         @OA\Property(property="name", type="string", example="Dr. John Smith"),
     *                         @OA\Property(property="specialization", type="string", example="Cardiology"),
     *                         @OA\Property(property="phone", type="string", example="+1234567890")
     *                     ),
     *                     @OA\Property(
     *                         property="patient",
     *                         type="object",
     *                         description="Only present for doctor users",
     *                         @OA\Property(property="name", type="string", example="Jane Doe"),
     *                         @OA\Property(property="email", type="string", example="jane@example.com"),
     *                         @OA\Property(property="phone", type="string", example="+1234567890"),
     *                         @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-15")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="statusCode", type="integer", example=401),
     *             @OA\Property(property="status", type="string", example="Unauthorized")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Profile not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Patient profile not found"),
     *             @OA\Property(property="statusCode", type="integer", example=404),
     *             @OA\Property(property="status", type="string", example="Not Found")
     *         )
     *     )
     * )
     */
    public function index(GetAppointmentsRequest $request)
    {
        $user = auth('api')->user();

        if ($user->isPatient()) {
            $patient = $user->patient;

            if (! $patient) {
                return $this->error('Patient profile not found', null, Response::HTTP_NOT_FOUND);
            }

            $query = $patient->appointments()->with(['doctor.user']);

            if ($request->has('status')) {
                $query->where('status', AppointmentStatus::fromLabel($request->status)->value);
            }

            $appointments = $query->get();

            return $this->success(AppointmentResource::collection($appointments), 'Appointments retrieved successfully');
        }

        if ($user->isDoctor()) {
            $doctor = $user->doctor;

            if (! $doctor) {
                return $this->error('Doctor profile not found', null, Response::HTTP_NOT_FOUND);
            }

            $query = $doctor->appointments()->with(['patient.user']);

            if ($request->has('status')) {
                $query->where('status', AppointmentStatus::fromLabel($request->status)->value);
            }

            $appointments = $query->get();

            return $this->success(AppointmentResource::collection($appointments), 'Appointments retrieved successfully');
        }

        return $this->error('Invalid user role', null, Response::HTTP_FORBIDDEN);
    }

    /**
     * @OA\Get(
     *     path="/api/appointments/{appointment}",
     *     tags={"Appointments"},
     *     summary="Get specific appointment for authenticated patient",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="appointment",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Appointment retrieved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Appointment retrieved successfully"),
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="string", example="OK"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="appointment_date", type="string", format="date-time", example="2025-12-01 10:00:00"),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="notes", type="string", example="Follow-up consultation"),
     *                 @OA\Property(
     *                     property="doctor",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Dr. John Smith"),
     *                     @OA\Property(property="specialization", type="string", example="Cardiology"),
     *                     @OA\Property(property="email", type="string", example="doctor@example.com"),
     *                     @OA\Property(property="phone", type="string", example="+1234567890")
     *                 ),
     *                 @OA\Property(
     *                     property="patient",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Jane Doe"),
     *                     @OA\Property(property="email", type="string", example="jane@example.com"),
     *                     @OA\Property(property="phone", type="string", example="+1234567890"),
     *                     @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-15")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="statusCode", type="integer", example=401),
     *             @OA\Property(property="status", type="string", example="Unauthorized")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Appointment not found"),
     *             @OA\Property(property="statusCode", type="integer", example=404),
     *             @OA\Property(property="status", type="string", example="Not Found")
     *         )
     *     )
     * )
     */
    public function show(ViewAppointmentRequest $request, Appointment $appointment)
    {
        $appointment->load(['doctor', 'patient']);

        return $this->success(new AppointmentResource($appointment), 'Appointment retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/appointments",
     *     tags={"Appointments"},
     *     summary="Create new appointment for authenticated patient",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"doctor_id","appointment_date"},
     *
     *             @OA\Property(property="doctor_id", type="integer", example=1),
     *             @OA\Property(property="appointment_date", type="string", format="date-time", example="2025-12-01 10:00:00"),
     *             @OA\Property(property="notes", type="string", example="Follow-up consultation for chest pain")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Appointment created successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Appointment created successfully"),
     *             @OA\Property(property="statusCode", type="integer", example=201),
     *             @OA\Property(property="status", type="string", example="Created"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="appointment_date", type="string", format="date-time", example="2025-12-01 10:00:00"),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="notes", type="string", example="Follow-up consultation for chest pain"),
     *                 @OA\Property(
     *                     property="doctor",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Dr. John Smith"),
     *                     @OA\Property(property="specialization", type="string", example="Cardiology"),
     *                     @OA\Property(property="email", type="string", example="doctor@example.com"),
     *                     @OA\Property(property="phone", type="string", example="+1234567890")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="statusCode", type="integer", example=401),
     *             @OA\Property(property="status", type="string", example="Unauthorized")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or time slot unavailable",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="This time slot is already booked"),
     *             @OA\Property(property="statusCode", type="integer", example=422),
     *             @OA\Property(property="status", type="string", example="Unprocessable Content")
     *         )
     *     )
     * )
     */
    public function store(StoreAppointmentRequest $request)
    {
        $patient = auth('api')->user()->patient;

        if (! $patient) {
            return $this->error('Patient profile not found', null, Response::HTTP_NOT_FOUND);
        }

        $conflictingAppointment = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->whereIn('status', [AppointmentStatus::PENDING->value, AppointmentStatus::CONFIRMED->value])
            ->exists();

        if ($conflictingAppointment) {
            return $this->error('This time slot is already booked', null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $appointment = $patient->appointments()->create([
            'doctor_id' => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'notes' => $request->notes,
            'status' => AppointmentStatus::PENDING,
        ]);

        $appointment->load('doctor');

        return $this->success(new AppointmentResource($appointment), 'Appointment created successfully', Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/appointments/{appointment}/status",
     *     tags={"Appointments"},
     *     summary="Update appointment status (role-based permissions)",
     *     description="Updates appointment status based on user role. Patients can only cancel appointments. Doctors can confirm, cancel, or complete appointments. State transitions are validated: pending→confirmed/cancelled, confirmed→completed/cancelled. Completed and cancelled are final states.",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="appointment",
     *         in="path",
     *         required=true,
     *         description="Appointment ID",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Status to update. Patients: only 'cancelled' allowed. Doctors: 'confirmed', 'cancelled', or 'completed' allowed based on current state.",
     *
     *         @OA\JsonContent(
     *             required={"status"},
     *
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 description="New status. For patients: 'cancelled' only. For doctors: 'confirmed' (from pending), 'completed' (from confirmed), or 'cancelled' (from pending/confirmed)",
     *                 enum={"confirmed", "cancelled", "completed"},
     *                 example="confirmed"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Appointment status updated successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Appointment status updated successfully"),
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="string", example="OK"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="appointment_date", type="string", format="date-time", example="2025-12-01 10:00:00"),
     *                 @OA\Property(property="status", type="string", example="confirmed"),
     *                 @OA\Property(property="notes", type="string", example="Follow-up consultation"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-27 18:45:30"),
     *                 @OA\Property(
     *                     property="doctor",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Dr. John Smith"),
     *                     @OA\Property(property="specialization", type="string", example="Cardiology"),
     *                     @OA\Property(property="email", type="string", example="doctor@example.com"),
     *                     @OA\Property(property="phone", type="string", example="+1234567890")
     *                 ),
     *                 @OA\Property(
     *                     property="patient",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Jane Doe"),
     *                     @OA\Property(property="email", type="string", example="jane@example.com"),
     *                     @OA\Property(property="phone", type="string", example="+1234567890"),
     *                     @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-15")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="statusCode", type="integer", example=401),
     *             @OA\Property(property="status", type="string", example="Unauthorized")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User is not authorized to update this appointment",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="This action is unauthorized"),
     *             @OA\Property(property="statusCode", type="integer", example=403),
     *             @OA\Property(property="status", type="string", example="Forbidden")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Appointment not found"),
     *             @OA\Property(property="statusCode", type="integer", example=404),
     *             @OA\Property(property="status", type="string", example="Not Found")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or invalid state transition",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Cannot change appointment status from 'completed' to 'confirmed'"),
     *             @OA\Property(property="statusCode", type="integer", example=422),
     *             @OA\Property(property="status", type="string", example="Unprocessable Content"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 description="Validation errors (only present for validation failures, not state transition errors)",
     *                 @OA\Property(
     *                     property="status",
     *                     type="array",
     *
     *                     @OA\Items(type="string", example="The selected status is invalid.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function updateStatus(UpdateAppointmentStatusRequest $request, Appointment $appointment)
    {
        $newStatus = AppointmentStatus::fromLabel($request->status);
        $currentStatus = $appointment->status;

        $validTransitions = [
            AppointmentStatus::PENDING->value => [AppointmentStatus::CONFIRMED->value, AppointmentStatus::CANCELLED->value],
            AppointmentStatus::CONFIRMED->value => [AppointmentStatus::COMPLETED->value, AppointmentStatus::CANCELLED->value],
            AppointmentStatus::COMPLETED->value => [],
            AppointmentStatus::CANCELLED->value => [],
        ];

        if (! in_array($newStatus->value, $validTransitions[$currentStatus->value] ?? [])) {
            $currentLabel = $currentStatus->label();
            $newLabel = $newStatus->label();

            return $this->error(
                sprintf("Cannot change appointment status from '%s' to '%s'", $currentLabel, $newLabel),
                null,
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $appointment->update(['status' => $newStatus]);

        $appointment->load(['doctor', 'patient']);

        return $this->success(new AppointmentResource($appointment), 'Appointment status updated successfully');
    }

    /**
     * @OA\Delete(
     *     path="/api/appointments/{appointment}",
     *     tags={"Appointments"},
     *     summary="Delete appointment for authenticated patient",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="appointment",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Appointment deleted successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Appointment deleted successfully"),
     *             @OA\Property(property="statusCode", type="integer", example=200),
     *             @OA\Property(property="status", type="string", example="OK")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *             @OA\Property(property="statusCode", type="integer", example=401),
     *             @OA\Property(property="status", type="string", example="Unauthorized")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Appointment not found"),
     *             @OA\Property(property="statusCode", type="integer", example=404),
     *             @OA\Property(property="status", type="string", example="Not Found")
     *         )
     *     )
     * )
     */
    public function destroy(DeleteAppointmentRequest $request, Appointment $appointment)
    {
        $appointment->delete();

        return $this->success([], 'Appointment deleted successfully');
    }
}
