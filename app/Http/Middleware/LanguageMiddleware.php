<?php

namespace App\Http\Middleware;

use App\Models\Language;
use App\Models\Website;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    private $defaultLanguage = null;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /*************************************************************
         * 
         * version : 3
         * 
         * The logic has been updated
         * On 29 November 2023
         * By Kamyar
         * 
         ************************************************************/

        // check if the website_slug's query parameter doesn't exit on the route don't apply the logic.
        if (!$request->route('website_slug')) {
            return $next($request);
        }

        // get the website via website slug if exist with his languages.
        $website = Website::whereSlug($request->route('website_slug'))->with('languages')->firstOrFail();

        // loop through languages and select the default language
        foreach ($website->languages as $language) {
            if ($language->pivot->default) {
                $this->defaultLanguage = $language->abbreviation;

                // append languageId to request
                $request->merge(['languageId' => $language->id]);
                break;
            }
        }

        // merge the website id with the request
        $request->merge(['websiteId' => $website->id]);

        // check if the lang's query parameter available in the request
        $language = $website->languages->first(function ($language) use ($request) {
            $isSelected = $language['abbreviation'] == $request->input('lang');

            if ($isSelected) {
                $request->merge(['languageId' => $language->id]);

                return true;
            }

            return false;
        });

        // set the system language
        $this->setLanguageOrUseDefault($language->abbreviation ?? null);

        // The end
        return $next($request);





        // // If we have languageId from the request then find it in the available languages then set it to the system
        // if ($request->filled('languageId')) {
        //     $language = Language::whereId($request->input('languageId'))->firstOrFail();

        //     // loop the website's languages and check if the requested languageId exist
        //     foreach ($website->languages as $lang) {
        //         if ($language->id == $lang->id) {
        //             app()->setLocale($language->abbreviation);
        //             break;
        //         }
        //     }

        //     // If we haven't languageId from the request then check for the website's slug , it should be available always
        // } elseif ($request->route('website_slug')) {

        //     /**
        //      * NEW 2023-11-20 Hema
        //      * append languageId to request
        //      */
        //     if ($request->query('lang', null)) {
        //         $language = Language::whereAbbreviation($request->query('lang'))->first();
        //         if ($language)
        //             $request->merge(['languageId' => $language->id]);
        //     }

        //     // check if the website has more than a language
        //     else if (count($website->languages) > 1) {
        //         // loop between languages
        //         foreach ($website->languages as $language) {
        //             if ($language->pivot->default) {
        //                 app()->setLocale($language->abbreviation);

        //                 /**
        //                  * NEW 2023-11-20 Hema
        //                  * append languageId to request
        //                  */
        //                 $request->merge(['languageId' => $language->id]);
        //                 break;
        //             }
        //         }
        //         // if ($request->query('lang', null)) {
        //         //     $language = Language::whereAbbreviation($request->query('lang'))->first();
        //         //     if ($language)
        //         //         $request->merge(['languageId' => $language->id]);
        //         // }

        //         // else set the app locale to the first language's abbrevation else set to en by default
        //     } else {
        //         app()->setLocale($website->languages[0]->abbreviation ?? 'en');
        //         $request->merge(['languageId' => $website->languages[0]->id]);
        //     }
        // } else if ($request->query('lang', null)) {
        //     /**
        //      * NEW 2023-11-20 Hema
        //      * append languageId to request
        //      */
        //     $language = Language::whereAbbreviation($request->query('lang'))->first();
        //     if ($language)
        //         $request->merge(['languageId' => $language->id]);
        // }


        // return $next($request);
    }


    /**
     * New  :  29 November 2023
     */
    private function setLanguageOrUseDefault($lang)
    {
        if (!is_null($lang))
            app()->setlocale($lang);
        else
            app()->setlocale($this->defaultLanguage);
    }
}
