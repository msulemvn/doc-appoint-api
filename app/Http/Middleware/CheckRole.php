<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    use ApiResponseTrait;

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return $this->error('Unauthenticated', null, Response::HTTP_UNAUTHORIZED);
        }

        $userRole = auth()->user()->role;

        $allowedRoles = collect($roles)->map(fn ($role) => match (strtolower((string) $role)) {
            'doctor' => UserRole::DOCTOR,
            'patient' => UserRole::PATIENT,
            default => null,
        })->filter();

        if ($allowedRoles->isEmpty() || ! $allowedRoles->contains($userRole)) {
            return $this->error(
                'Forbidden. You do not have permission to access this resource.',
                null,
                Response::HTTP_FORBIDDEN
            );
        }

        return $next($request);
    }
}
