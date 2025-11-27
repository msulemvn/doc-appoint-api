<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;

class PatientAppointmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/patients/{patient}/appointments",
     *     tags={"Patient Appointments"},
     *     summary="Get all appointments for a patient",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="patient",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"pending", "confirmed", "cancelled", "completed"})
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Appointments retrieved successfully",
     *
     *         @OA\JsonContent(
     *             allOf={
     *
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(
     *
     *                     @OA\Property(
     *                         property="data",
     *                         type="array",
     *
     *                         @OA\Items(ref="#/components/schemas/Appointment")
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Patient not found")
     * )
     */
    public function index(Patient $patient, Request $request)
    {
        $query = $patient->appointments();

        if ($request->has('status')) {
            $query->where('status', AppointmentStatus::fromLabel($request->status)->value);
        }

        $appointments = $query->get();

        return $this->success(AppointmentResource::collection($appointments), 'Appointments retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/patients/{patient}/appointments/{appointment}",
     *     tags={"Patient Appointments"},
     *     summary="Get specific appointment for a patient",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="patient",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
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
     *             allOf={
     *
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(
     *
     *                     @OA\Property(property="data", ref="#/components/schemas/Appointment")
     *                 )
     *             }
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Appointment not found")
     * )
     */
    public function show(Patient $patient, Appointment $appointment)
    {
        if ($appointment->patient_id !== $patient->id) {
            return $this->error('Appointment not found', null, Response::HTTP_NOT_FOUND);
        }

        return $this->success(new AppointmentResource($appointment), 'Appointment retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/patients/{patient}/appointments",
     *     tags={"Patient Appointments"},
     *     summary="Create new appointment for a patient",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="patient",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/AppointmentRequest")
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Appointment created successfully",
     *
     *         @OA\JsonContent(
     *             allOf={
     *
     *                 @OA\Schema(
     *
     *                     @OA\Property(property="message", type="string", example="Appointment created successfully"),
     *                     @OA\Property(property="statusCode", type="integer", example=201),
     *                     @OA\Property(property="status", type="string", example="Created")
     *                 ),
     *
     *                 @OA\Schema(
     *
     *                     @OA\Property(property="data", ref="#/components/schemas/Appointment")
     *                 )
     *             }
     *         )
     *     ),
     *
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Patient not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Patient $patient, StoreAppointmentRequest $request)
    {
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

        return $this->success(new AppointmentResource($appointment), 'Appointment created successfully', Response::HTTP_CREATED);
    }

    /**
     * @OA\Delete(
     *     path="/api/patients/{patient}/appointments/{appointment}",
     *     tags={"Patient Appointments"},
     *     summary="Delete appointment for a patient",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="patient",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
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
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Appointment not found")
     * )
     */
    public function destroy(Patient $patient, Appointment $appointment)
    {
        if ($appointment->patient_id !== $patient->id) {
            return $this->error('Appointment not found', null, Response::HTTP_NOT_FOUND);
        }

        $appointment->delete();

        return $this->success([], 'Appointment deleted successfully');
    }
}
