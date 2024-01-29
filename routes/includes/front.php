<?php

use App\Http\Controllers\Api\Front\GeneralController;
use App\Http\Controllers\Api\Front\PageController;
use App\Http\Controllers\Api\Front\ItemController;
use App\Http\Controllers\Api\Front\LocalizationController;
use App\Http\Controllers\Api\Front\MetaController;
use App\Http\Controllers\Api\Front\NavigationController;
use App\Http\Controllers\Api\Front\WebsiteController;
use Illuminate\Support\Facades\Route;

Route::apiResource('websites', WebsiteController::class)->only(['index', 'show']);

Route::get('items/index/{website_slug}', [ItemController::class, 'index']);
Route::get('items/show/{website_slug}/{item}', [ItemController::class, 'show']);
Route::get('items/feature/{website_slug}', [ItemController::class, 'feature']);

Route::controller(PageController::class)->group(function () {
    Route::get('pages/{website_slug}/{page}', 'websitePage');
    Route::get('pages/{website_slug}/{page}/first', 'websitePageFirst');
    Route::get('pages/{website_slug}/{page}/others', 'websitePageOthers');
    Route::get('languages/{website_slug}', 'websiteLanguages');
    Route::post('contact-us-form/{website_slug}', 'contactUsFormStore');
});

Route::controller(LocalizationController::class)->group(function () {
    Route::get('{website_slug}/localization/key/{key}', 'localizationKey');
    Route::get('localization/website/{website_slug}', 'localizationWebsite');
});

Route::controller(NavigationController::class)->group(function () {
    Route::get('navigation/{website_slug}', 'index');
});

Route::get('meta/{website_slug}/{page?}', [MetaController::class, 'index']);

Route::controller(GeneralController::class)->group(function () {
    Route::get('{website_slug}/tags', 'tags');
    Route::get('{website_slug}/groups', 'groups');
});

?>