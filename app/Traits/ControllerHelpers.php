<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

trait ControllerHelpers
{
    /**
     * Response JSON
     */
    public function responseJson($data, int $code = 200): JsonResponse
    {
        return response()->json($data, $code);
    }

    /**
     * Response JSON validation error
     */
    public function responseJsonValidate(\Illuminate\Support\MessageBag $errors, int $code = 422): JsonResponse
    {
        return response()->json(['error' => $errors], $code);
    }

    /**
     * Response JSON message
     */
    public function responseJsonMessage(string $message, int $code = 200): JsonResponse
    {
        return response()->json(['message' => $message], $code);
    }

    /**
     * Response JSON data
     */
    public function responseJsonData($data, string $message = 'success get data', int $code = 200): JsonResponse
    {
        return response()->json(['data' => $data, 'message' => $message], $code);
    }

    /**
     * Response JSON message CRUD
     */
    public function responseJsonMessageCrud(bool $success = true, string $method = 'create', string $message = null, string $exception_message = null, int $code = 200, $data = null): JsonResponse
    {
        $methods = [
            'create' => 'insert new data. ',
            'update' => 'update data. ',
            'delete' => 'delete data. ',
            'restore' => 'restore data. ',
            'forceDelete' => 'force delete data. ',
        ];

        $final_message = ($success ? 'Success ' : 'Failed ');

        if (isset($methods[$method])) {
            $final_message .= $methods[$method];

            if ($method === 'create' && $success) {
                $code = 201;
            }
        }

        if ($message !== null) {
            $final_message .= $message . ' ';
        }

        if ($exception_message !== null) {
            $final_message .= $exception_message;
        }

        $response = ['message' => $final_message];

        if ($data !== null) {
            $response['result'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Response message CRUD
     */
    public function responseMessageCrud(bool $success = true, string $method = 'create', string $message = null, string $exception_message = null): array
    {
        $methods = [
            'create' => 'insert new data. ',
            'update' => 'update data. ',
            'delete' => 'delete data. ',
            'restore' => 'restore data. ',
            'forceDelete' => 'force delete data. ',
        ];

        $final_message = ($success ? 'Success ' : 'Failed ');

        if (isset($methods[$method])) {
            $final_message .= $methods[$method];
        }

        if ($message !== null) {
            $final_message .= $message . ' ';
        }

        if ($exception_message !== null) {
            $final_message .= $exception_message;
        }

        return $final_message;
    }

    /**
     * Response file
     */
    public function responseFile(string $file_name): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $file_path = Storage::disk('public')->path($file_name);

        if (Storage::disk('public')->exists($file_name)) {
            return response()->file($file_path);
        }

        return response()->file(public_path('images\illustrations\error-404.svg'));
    }

    /**
     * Response download from storage
     */
    public function responseDownloadStorage(string $file): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $file_path = storage_path('/app/public/' . $file);
        return response()->download($file_path);
    }

    /**
     * Response download
     */
    public function responseDownload(string $file): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return response()->download($file);
    }
}
