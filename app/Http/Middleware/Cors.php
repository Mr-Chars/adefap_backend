<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
class Cors
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        // No aplicar CORS a respuestas de archivos
        if ($response instanceof BinaryFileResponse || $response instanceof StreamedResponse) {
            return $response;
        }

        return $response
            //Url a la que se le dará acceso en las peticiones
            ->header('Access-Control-Allow-Origin', '*')
            //Métodos que a los que se da acceso
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE')
            //Headers de la petición
            ->header('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, X-Token-Auth, Authorization');
    }
}
