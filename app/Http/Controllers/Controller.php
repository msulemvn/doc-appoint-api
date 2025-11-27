<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponseTrait;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Test API",
 *     version="1.0.0",
 *     description="API documentation for test application",
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
 */
abstract class Controller
{
    use ApiResponseTrait;
}
