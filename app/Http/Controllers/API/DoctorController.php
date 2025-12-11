<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\GetAvailableDoctorsRequest;
use App\Http\Resources\DoctorResource;
use App\Models\DoctorDetail;
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
     *             type="array",
     *
     *             @OA\Items(
     *                 type="object",
     *
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Dr. John Smith"),
     *                 @OA\Property(property="email", type="string", example="john.smith@hospital.com"),
     *                 @OA\Property(property="phone", type="string", example="+1234567890"),
     *                 @OA\Property(property="specialization", type="string", example="Cardiology"),
     *                 @OA\Property(property="bio", type="string", example="Experienced cardiologist with 15 years of practice"),
     *                 @OA\Property(property="years_of_experience", type="integer", example=15),
     *                 @OA\Property(property="consultation_fee", type="number", format="float", example=150.00),
     *                 @OA\Property(property="license_number", type="string", example="MD12345"),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Dr. John Smith"),
     *                     @OA\Property(property="email", type="string", example="john.smith@hospital.com"),
     *                     @OA\Property(property="avatar", type="string", nullable=true, example=null)
     *                 ),
     *                 @OA\Property(
     *                     property="available_slots",
     *                     type="array",
     *                     description="Available time slots (only included when date parameter is provided)",
     *
     *                     @OA\Items(type="string", example="09:00")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function available(GetAvailableDoctorsRequest $request)
    {
        $query = DoctorDetail::query();

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

        return response()->json(DoctorResource::collection($doctors));
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
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Dr. John Smith"),
     *             @OA\Property(property="email", type="string", example="john.smith@hospital.com"),
     *             @OA\Property(property="phone", type="string", example="+1234567890"),
     *             @OA\Property(property="specialization", type="string", example="Cardiology"),
     *             @OA\Property(property="bio", type="string", example="Experienced cardiologist with 15 years of practice"),
     *             @OA\Property(property="years_of_experience", type="integer", example=15),
     *             @OA\Property(property="consultation_fee", type="number", format="float", example=150.00),
     *             @OA\Property(property="license_number", type="string", example="MD12345"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Dr. John Smith"),
     *                 @OA\Property(property="email", type="string", example="john.smith@hospital.com"),
     *                 @OA\Property(property="avatar", type="string", nullable=true, example=null)
     *             ),
     *             @OA\Property(
     *                 property="available_slots",
     *                 type="array",
     *                 description="Available time slots (only included when date parameter is provided)",
     *
     *                 @OA\Items(type="string", example="09:00")
     *             )
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
    public function show(DoctorDetail $doctor)
    {
        return response()->json(new DoctorResource($doctor->load('user')));
    }

    /**
     * @OA\Get(
     *     path="/api/doctors/{doctor}/available-slots",
     *     tags={"Doctors"},
     *     summary="Get available time slots for a specific doctor on a given date",
     *     description="Returns a list of all possible time slots for a doctor, indicating whether each slot is available or booked for the specified date.",
     *
     *     @OA\Parameter(
     *         name="doctor",
     *         in="path",
     *         description="ID of the doctor",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Date to check availability (YYYY-MM-DD)",
     *         required=true,
     *
     *         @OA\Schema(type="string", format="date", example="2025-11-28")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Available time slots retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(
     *                 type="object",
     *
     *                 @OA\Property(property="time", type="string", example="09:00 AM"),
     *                 @OA\Property(property="is_available", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Doctor not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Doctor not found"),
     *             @OA\Property(property="statusCode", type="integer", example=404),
     *             @OA\Property(property="status", type="string", example="Not Found")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"date": {"The date field is required."}})
     *         )
     *     )
     * )
     */
    public function getAvailableSlots(Request $request, DoctorDetail $doctor)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);

        $selectedDate = $request->input('date');

        // Hardcoded available slots
        $allSlots = [
            '09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM',
            '01:00 PM', '02:00 PM', '03:00 PM', '04:00 PM',
        ];

        // Get booked appointments for the doctor on the selected date
        $bookedAppointments = $doctor->appointments()
            ->whereDate('appointment_date', $selectedDate)
            ->whereIn('status', [AppointmentStatus::PENDING->value, AppointmentStatus::CONFIRMED->value, AppointmentStatus::AWAITING_PAYMENT->value])
            ->get();

        $bookedTimes = $bookedAppointments->map(fn($appointment) => Carbon::parse($appointment->appointment_date)->format('h:i A'))->toArray();

        // Return only available slots
        $availableTimeSlots = [];
        foreach ($allSlots as $slot) {
            if (! in_array($slot, $bookedTimes)) {
                $availableTimeSlots[] = [
                    'time' => $slot,
                    'is_available' => true,
                ];
            }
        }

        return response()->json($availableTimeSlots);
    }
}
