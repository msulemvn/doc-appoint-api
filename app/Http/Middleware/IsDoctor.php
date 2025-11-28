<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsDoctor
{
    use ApiResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return $this->error('Unauthenticated', null, Response::HTTP_UNAUTHORIZED);
        }

        if (! auth()->user()->isDoctor()) {
            return $this->error(
                'Forbidden. Only doctors can access this resource.',
                null,
                Response::HTTP_FORBIDDEN
            );
        }

        return $next($request);
    }
}
