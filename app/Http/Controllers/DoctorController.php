<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class DoctorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/doctors/available",
     *     tags={"Doctors"},
     *     summary="Get available doctors with time slots",
     *     description="Returns list of doctors. When 'date' parameter is provided, includes available time slots for that specific date.",
     *
     *     @OA\Parameter(
     *         name="specialization",
     *         in="query",
     *         description="Filter doctors by specialization",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="Cardiology")
     *     ),
     *
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Date to check availability (YYYY-MM-DD). When provided, response includes available_slots array.",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date", example="2025-11-28")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Available doctors retrieved successfully",
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
     *                         @OA\Items(
     *                             type="object",
     *
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Dr. John Smith"),
     *                             @OA\Property(property="specialization", type="string", example="Cardiology"),
     *                             @OA\Property(property="email", type="string", example="john.smith@hospital.com"),
     *                             @OA\Property(property="phone", type="string", example="+1234567890"),
     *                             @OA\Property(
     *                                 property="available_slots",
     *                                 type="array",
     *                                 description="Available time slots (only included when date parameter is provided)",
     *
     *                                 @OA\Items(type="string", example="09:00")
     *                             )
     *                         )
     *                     )
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function available(Request $request)
    {
        $query = Doctor::query();

        if ($request->has('specialization')) {
            $query->where('specialization', $request->specialization);
        }

        if ($request->has('date')) {
            $query->whereHas('appointments', function ($q) use ($request) {
                $q->whereDate('appointment_date', '!=', $request->date)
                    ->orWhereIn('status', [AppointmentStatus::CANCELLED->value, AppointmentStatus::COMPLETED->value]);
            });
        }

        $doctors = $query->with('user')->get();

        return $this->success(DoctorResource::collection($doctors), 'Available doctors retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/doctors/{doctor}",
     *     tags={"Doctors"},
     *     summary="Get specific doctor details",
     *
     *     @OA\Parameter(
     *         name="doctor",
     *         in="path",
     *         description="Doctor ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Date to check availability (YYYY-MM-DD). When provided, response includes available_slots array.",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date", example="2025-11-28")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Doctor retrieved successfully",
     *
     *         @OA\JsonContent(
     *             allOf={
     *
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(
     *
     *                     @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Dr. John Smith"),
     *                         @OA\Property(property="specialization", type="string", example="Cardiology"),
     *                         @OA\Property(property="email", type="string", example="john.smith@hospital.com"),
     *                         @OA\Property(property="phone", type="string", example="+1234567890"),
     *                         @OA\Property(
     *                             property="available_slots",
     *                             type="array",
     *                             description="Available time slots (only included when date parameter is provided)",
     *
     *                             @OA\Items(type="string", example="09:00")
     *                         )
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Doctor not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Doctor]"),
     *             @OA\Property(property="statusCode", type="integer", example=404),
     *             @OA\Property(property="status", type="string", example="Not Found")
     *         )
     *     )
     * )
     */
    public function show(Doctor $doctor)
    {
        $doctor->load('user');

        return $this->success(new DoctorResource($doctor), 'Doctor retrieved successfully');
    }

    /**
     * @OA\Get(
     *     path="/api/doctors/{doctor}/appointments",
     *     tags={"Doctors"},
     *     summary="Get all appointments for a doctor",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="doctor",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date")
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
     *         description="Doctor not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Doctor]"),
     *             @OA\Property(property="statusCode", type="integer", example=404),
     *             @OA\Property(property="status", type="string", example="Not Found")
     *         )
     *     )
     * )
     */
    public function appointments(Doctor $doctor, Request $request)
    {
        $query = $doctor->appointments();

        if ($request->has('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        if ($request->has('status')) {
            $query->where('status', AppointmentStatus::fromLabel($request->status)->value);
        }

        $appointments = $query->get();

        return $this->success(AppointmentResource::collection($appointments), 'Appointments retrieved successfully');
    }
}
