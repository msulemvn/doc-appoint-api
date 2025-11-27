<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Http\Requests\UpdateAppointmentStatusRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class AppointmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/appointments/available-slots",
     *     tags={"Appointments"},
     *     summary="Get available appointment slots",
     *
     *     @OA\Parameter(
     *         name="doctor_id",
     *         in="query",
     *         required=false,
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
     *         name="specialization",
     *         in="query",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Available slots retrieved successfully",
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
     *                         @OA\Items(ref="#/components/schemas/AvailableSlot")
     *                     )
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function availableSlots(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());
        $doctorId = $request->input('doctor_id');
        $specialization = $request->input('specialization');

        $query = Doctor::query();

        if ($doctorId) {
            $query->where('id', $doctorId);
        }

        if ($specialization) {
            $query->where('specialization', $specialization);
        }

        $doctors = $query->get();
        $slots = [];

        foreach ($doctors as $doctor) {
            $startTime = Carbon::parse($date.' 09:00:00');
            $endTime = Carbon::parse($date.' 17:00:00');

            while ($startTime < $endTime) {
                $slotEnd = $startTime->copy()->addMinutes(30);

                $isBooked = Appointment::where('doctor_id', $doctor->id)
                    ->whereDate('appointment_date', $date)
                    ->where('appointment_date', $startTime)
                    ->whereIn('status', [AppointmentStatus::PENDING->value, AppointmentStatus::CONFIRMED->value])
                    ->exists();

                $slots[] = [
                    'doctor_id' => $doctor->id,
                    'doctor_name' => $doctor->name,
                    'specialization' => $doctor->specialization,
                    'date' => $date,
                    'start_time' => $startTime->format('H:i:s'),
                    'end_time' => $slotEnd->format('H:i:s'),
                    'available' => ! $isBooked,
                ];

                $startTime->addMinutes(30);
            }
        }

        return $this->success($slots, 'Available slots retrieved successfully');
    }

    /**
     * @OA\Put(
     *     path="/api/appointments/{appointment}/status",
     *     tags={"Appointments"},
     *     summary="Update appointment status",
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
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(ref="#/components/schemas/UpdateAppointmentStatus")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Appointment status updated successfully",
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
     *     @OA\Response(response=404, description="Appointment not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateStatus(UpdateAppointmentStatusRequest $request, Appointment $appointment)
    {
        $appointment->update(['status' => AppointmentStatus::fromLabel($request->status)]);

        return $this->success(new AppointmentResource($appointment), 'Appointment status updated successfully');
    }
}
