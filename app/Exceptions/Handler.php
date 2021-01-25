<?php

namespace App\Exceptions;

use Error;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     * 
     * We format almost everything here to comply with https://jsonapi.org/.
     *
     * @return void
     */
    public function register()
    {
        if (!env('APP_DEBUG')) {
            /** Handles 404 resources */
            $this->renderable(function (NotFoundHttpException $e, $request) {
                return response()->json([
                    "errors" => [
                        [
                            "status" => 404,
                            "detail" => "The specified resource does not exist."
                        ]
                    ]
                ], 404);
            });

            /** Handles 422 resources */
            $this->renderable(function (ValidationException $e, $request) {
                $errors = $e->validator->errors()->toArray();
                $formatted = [];

                foreach ($errors as $key => $value) {
                    array_push($formatted, [
                        "status" => 422,
                        "source" => $key,
                        "detail" => $value
                    ]);
                }

                return response()->json([
                    "status" => 422,
                    "errors" => $formatted
                ], 422);
            });

            /** Handles server errors */
            $this->renderable(function (Error $e, $request) {
                return response()->json([
                    "errors" => [
                        [
                            "status" => 500,
                            "detail" => "Internal Server Error"
                        ]
                    ]
                ], 500);
            });

            /** Handles generic HTTP error resources */
            $this->renderable(function (HttpException $e, $request) {
                return response()->json([
                    "errors" => [
                        [
                            "status" => $e->getStatusCode(),
                            "detail" => "Service unavailable, please try again."
                        ]
                    ]
                ], $e->getStatusCode());
            });
        }
    }
}
