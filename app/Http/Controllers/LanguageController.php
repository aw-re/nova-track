<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchLang(Request $request, $locale)
    {
        // Validate if the locale is supported
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }

        // Store the locale in session
        Session::put('locale', $locale);
        
        // Store the locale in cookie for persistence (1 year)
        return redirect()->back()->withCookie(cookie('locale', $locale, 525600));
    }
}
