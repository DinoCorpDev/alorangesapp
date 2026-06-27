<?php

namespace App\Http\Middleware;

use Closure;
use App;
use App\Models\Language as LanguageModel;
use Session;

class Language
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
        if (Session::has('locale')) {
            $locale = Session::get('locale');
        }
        elseif (env('DEFAULT_LANGUAGE') != null) {
            $locale = env('DEFAULT_LANGUAGE');
        }
        else {
            $locale = 'es';
        }

        $languageExists = LanguageModel::where('code', $locale)->where('status', 1)->exists();

        if (! $languageExists) {
            $locale = LanguageModel::where('code', 'es')->where('status', 1)->exists() ? 'es' : 'en';
        }

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        return $next($request);
    }
}
