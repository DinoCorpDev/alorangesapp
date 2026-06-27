<?php

namespace App\Http\Middleware;

use Closure;
use App;
use Session;
use Config;

class ApiLocalization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = 'es'; // Valor por defecto

        // Check header request and determine localization
        if ($request->hasHeader('Accept-Language')) {
            $acceptLang = $request->header('Accept-Language');

            // Extrae solo el primer código de idioma, ej: "es_ES,es;q=0.9" -> "es"
            if (preg_match('/^[a-z]{2}/i', $acceptLang, $matches)) {
                $locale = strtolower($matches[0]);
            }
        } elseif (env('DEFAULT_LANGUAGE')) {
            $locale = strtolower(env('DEFAULT_LANGUAGE'));
        }

        // Establecer solo si es un idioma permitido
        if (in_array($locale, ['es', 'en'])) {
            app()->setLocale($locale);
        } else {
            app()->setLocale('es'); // fallback seguro
        }

        return $next($request);
    }
}
