<?php

namespace App\Http\Controllers\API;

use App\Enums\AppointmentStatus;
use App\Actions\Appointment\UpdateAppointmentStatusAction;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Payment;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    use ApiResponseTrait;

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->server('HTTP_STRIPE_SIGNATURE');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (UnexpectedValueException) {
            return $this->error('Invalid payload.', 400);
        } catch (SignatureVerificationException) {
            return $this->error('Invalid signature.', 403);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;

            $description = $paymentIntent->description;
            preg_match('/#(\d+)/', (string) $description, $matches);
            $appointmentId = $matches[1] ?? null;

            if ($appointmentId) {
                $appointment = Appointment::find($appointmentId);

                if ($appointment) {
                    Payment::create([
                        'appointment_id' => $appointment->id,
                        'amount' => $paymentIntent->amount / 100,
                        'status' => 'succeeded',
                        'transaction_id' => $paymentIntent->id,
                        'payment_method' => 'stripe',
                    ]);

                    $appointment->payment_status = PaymentStatus::Paid;
                    $appointment->payment_intent_id = $paymentIntent->id;
                    $appointment->save();

                    app(UpdateAppointmentStatusAction::class)->execute($appointment, AppointmentStatus::CONFIRMED->value);
                }
            }
        }

        return $this->success(['status' => 'success']);
    }
}
