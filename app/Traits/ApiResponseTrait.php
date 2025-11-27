<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

trait ApiResponseTrait
{
    public function success($data = [], string $message = 'Success', int $statusCode = ResponseAlias::HTTP_OK, array $meta = []): JsonResponse
    {
        if ($data instanceof Paginator) {
            return $this->paginatedResponse($data, $message, $statusCode);
        }

        if ($data instanceof JsonResource) {
            return $this->resourceResponse($data, $message, $statusCode);
        }

        $response = [
            'message' => $message,
            'statusCode' => $statusCode,
            'status' => ResponseAlias::$statusTexts[$statusCode],
        ];

        if (! empty($data)) {
            $response = array_merge($response, is_array($data) ? $data : ['data' => $data]);
        }

        if (! empty($meta)) {
            $response['meta'] = $meta;
        }

        return $this->jsonResponse($response, $statusCode);
    }

    public function error(string $message = 'Error', $data = null, int $statusCode = ResponseAlias::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->jsonResponse([
            'message' => $message,
            'statusCode' => $statusCode,
            'status' => ResponseAlias::$statusTexts[$statusCode],
            'errors' => $data,
        ], $statusCode);
    }

    private function paginatedResponse(Paginator $paginator, string $message, int $statusCode): JsonResponse
    {
        $meta = [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'has_more_pages' => $paginator->hasMorePages(),
        ];

        return $this->success(['data' => $paginator->getCollection()], $message, $statusCode, $meta);
    }

    private function resourceResponse(JsonResource $resource, string $message, int $statusCode): JsonResponse
    {
        return $this->success($resource->response()->getData(true), $message, $statusCode);
    }

    private function jsonResponse(array $data, int $statusCode, array $headers = []): JsonResponse
    {
        return response()->json($data, $statusCode, $headers, JSON_UNESCAPED_UNICODE);
    }
}
