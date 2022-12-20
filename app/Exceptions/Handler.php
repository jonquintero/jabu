<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof AuthenticationException) {
            if ($this->isFrontend($request)) {
                return redirect()->guest('login');
            }
            return $this->errorResponse(false, 'No se encuentra autenticado', 401);
        }

        if($exception instanceof AuthorizationException) {
            return $this->errorResponse(false,'No posee permisos para ejecutar esta acción', 403);
        }

        if ($exception instanceof ModelNotFoundException){
            $modelName = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse(false,"No existe ningun {$modelName} con ese identificador", 404);
        }

        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        if($exception instanceof NotFoundHttpException) {
            return $this->errorResponse(false,'No se encontró la URL especificada', 404);
        }
        if($exception instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse(false,'El método especificado en la petición no es válido', 405);
        }

        if ($exception instanceof HttpException){
            return $this->errorResponse(false,$exception->getMessage() ?? 'No tiene suficientes permisos para esta accion', $exception->getStatusCode());
        }

        /* if(!$this->isFrontend($request)) {
             return $this->errorResponse(false, 'Error interno del servidor', 500);
         }*/

        if ($exception instanceof QueryException){
            $errorCode = $exception->errorInfo[1];
            if ($errorCode == 1451) {
                return $this->errorResponse(false,'No puede eliminar este registro. Tiene relacion con otro registro', 409);
            }

        }

        /**
         * Manejo de excepcion tiempo api gmac
         */
        if($exception) {
            $message = explode(" ", $exception->getMessage());
            $file = explode("/", $exception->getFile());

            if(
                count($message)>1
                && count($file)>1
                && $message[count($message)-1] == 'exceeded'
                && $file[count($file)-1] == 'ApiController.php'
            ) {

                $error = [
                    'success'=>false,
                    'errorcode'=>1023,
                    'errordesc'=>'No se retornaron cotizaciones en tiempo - Time Out.'
                ];

                return response()->json($error, '500');
            }
        }

        return parent::render($request, $exception);
    }


    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();

        if ($this->isFrontend($request)) {

            return $request->ajax()? response()->json($errors, 422):redirect()
                ->back()
                ->withInput($request->input())
                ->withErrors($errors);
        }
        return $this->errorResponse(false, $errors, 422);
    }

    protected function errorResponse($boolean, $message, $code)
    {
        return response()->json(['success' => $boolean, 'error' => $message, 'code' => $code], $code);
    }

    private function isFrontend(Request $request)
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }

    private function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }


}


/*
//Funcional q muestra los errores

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{

    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}*/
