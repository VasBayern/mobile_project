<?php

namespace App\Traits;

use Error;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Resources\Json\Resource;

trait ApiResponseTrait
{
    /**
     * Return generic json response with the given data.
     *
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     *
     * @return JsonResponse
     */
    protected function apiResponse($data = [], $statusCode = 200, $headers = [])
    {
        $result = $this->parseGivenData($data, $statusCode, $headers);

        return response()->json($result['content'], $result['statusCode'], $result['headers']);
    }

    /**
     * Parse given data from api resource
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     * 
     * @return array
     */
    public function parseGivenData($data = [], $statusCode = 200, $headers = [])
    {
        $responseStructure = [
            'success'   => $data['success'],
            'message'   => $data['message'] ?? null,
        ];

        if (isset($data['errors'])) {
            $responseStructure['errors'] = $data['errors'];
        }

        if (isset($data['status'])) {
            $statusCode = $data['status'];
        }

        if (isset($data['exception']) && ($data['exception'] instanceof Error || $data['exception'] instanceof Exception)) {
            if (config('app.env') !== 'production') {
                $responseStructure['exception'] = [
                    'message'   => $data['exception']->getMessage(),
                    'file'      => $data['exception']->getFile(),
                    'line'      => $data['exception']->getLine(),
                    'code'      => $data['exception']->getCode(),
                    'trace'     => $data['exception']->getTrace(),
                ];
            }

            if ($statusCode == 200) {
                $statusCode = 500;
            }
        }

        if ($data['success'] === false) {
            if (isset($data['error_code'])) {
                // $responseStructure['error_code'] = $data['error_code'];
            }
        } else {
            $responseStructure['data'] = $data['data'] ?? null;
            // $responseStructure['error_code'] = 1;
        }

        return [
            'content'       => $responseStructure,
            'statusCode'    => $statusCode,
            'headers'       => $headers,
        ];
    }

    /**
     * Response with single resource
     * 
     * @param Resource $resource
     * @param null $message
     * @param int $statusCode
     * @param array $headers
     * 
     * @return JsonResponse
     */
    protected function respondWithResource($resource, $message = null, $statusCode = 200, $headers = [])
    {
        return $this->apiResponse(
            [
                'success'   => true,
                'message'   => $message,
                'data'      => $resource
            ],
            $statusCode,
            $headers
        );
    }

    /**
     * Response with resource collection pagination page
     * 
     * @param ResourceCollection $resourceCollection
     * @param null $message
     * @param int $statusCode
     * @param array $headers
     * 
     * @return JsonResponse
     */
    protected function respondWithResourceCollection($resourceCollection, $message = null, $statusCode = 200, $headers = [])
    {
        // https://laracasts.com/discuss/channels/laravel/pagination-data-missing-from-api-resource

        return $this->apiResponse(
            [
                'success'   => true,
                'data'      => $resourceCollection->response()->getData(true)
            ],
            $statusCode,
            $headers
        );
    }

    /**
     * Respone with error.
     *
     * @param string $message
     * @param int $statusCode
     * @param Exception $exception
     * @param int $error_code
     *
     * @return JsonResponse
     */
    protected function respondError($message, int $statusCode = 400, Exception $exception = null, int $error_code = 1)
    {
        return $this->apiResponse(
            [
                'success'       => false,
                'message'       => $message ?? 'Có lỗi trên máy chủ. Vui lòng thử lại sau!',
                'exception'     => $exception,
                'error_code'    => $error_code
            ],
            $statusCode
        );
    }

    /**
     * Respone with success.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    protected function respondSuccess($message = null)
    {
        return $this->apiResponse(['success' => true, 'message' => $message]);
    }

    /**
     * Respone with created.
     * 
     * @param string $message
     *
     * @return JsonResponse
     */
    protected function respondCreated($data)
    {
        return $this->apiResponse($data, 201);
    }

    /**
     * Respone with bad request.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    protected function respondBadRequest($message = 'Bad Request')
    {
        return $this->respondError($message, 400);
    }

    /**
     * Respone with unauthorized.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    protected function respondUnauthorized($message = 'Unauthorized')
    {
        return $this->respondError($message, 401);
    }

    /**
     * Respone with forbidden.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    protected function respondForbidden($message = 'Forbidden')
    {
        return $this->respondError($message, 403);
    }

    /**
     * Respone with not found.
     *
     * @param string $message
     * 
     * @return JsonResponse
     */
    protected function respondNotFound($message = 'Not Found')
    {
        return $this->respondError($message, 404);
    }

    /**
     * Respone with internal error.
     *
     * @param string $message
     *
     * @return JsonResponse
     */
    protected function respondInternalError($message = 'Internal Error')
    {
        return $this->respondError($message, 500);
    }

    /**
     * Respone with validation error.
     *
     * @param ValidationException $exception
     *
     * @return JsonResponse
     */
    protected function respondValidationErrors(ValidationException $exception)
    {
        return $this->apiResponse(
            [
                'success'   => false,
                'message'   => $exception->getMessage(),
                'errors'    => $exception->errors(),
            ]
        );
    }
}
