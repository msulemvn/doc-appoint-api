<?php

namespace App\Http\Controllers\API;

use App\Enums\AppointmentStatus;
use App\Enums\PaymentStatus;
use App\Events\PaymentConfirmed;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePaymentIntentRequest;
use App\Models\Appointment;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Stripe\Customer;
use Stripe\Exception\InvalidRequestException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    public function createPaymentIntent(CreatePaymentIntentRequest $request)
    {
        $appointment = Appointment::findOrFail($request->input('appointment_id'));
        $amount = $appointment->price * 100;

        $user = $request->user();

        Stripe::setApiKey(config('services.stripe.secret'));

        if (! $user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->name,
            ]);

            $user->stripe_customer_id = $customer->id;
            $user->save();
        } else {
            try {
                $customer = Customer::retrieve($user->stripe_customer_id);
            } catch (InvalidRequestException) {
                $customer = Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);

                $user->stripe_customer_id = $customer->id;
                $user->save();
            }
        }

        $paymentIntent = PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'usd',
            'customer' => $customer->id,
            'description' => 'Doctor Appointment Payment for Appointment #'.$appointment->id,
        ]);

        return $this->success(['clientSecret' => $paymentIntent->client_secret]);
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_intent' => 'required|string',
            'appointment_id' => 'required|exists:appointments,id',
        ]);

        $appointment = Appointment::findOrFail($request->input('appointment_id'));
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::retrieve($request->input('payment_intent'));

            if ($paymentIntent->status !== 'succeeded') {
                return $this->error('Payment not completed', 400);
            }

            if ($appointment->payment_status === PaymentStatus::Paid && $appointment->payment_intent_id === $paymentIntent->id) {
                return $this->success(['success' => true, 'message' => 'Payment already confirmed']);
            }

            $appointment->update([
                'payment_status' => PaymentStatus::Paid,
                'status' => AppointmentStatus::CONFIRMED->value,
                'payment_intent_id' => $paymentIntent->id,
            ]);

            event(new PaymentConfirmed($appointment));

            return $this->success([
                'success' => true,
                'message' => 'Payment confirmed successfully',
            ]);
        } catch (Exception $exception) {
            return $this->error('Failed to confirm payment: '.$exception->getMessage(), 500);
        }
    }
}
