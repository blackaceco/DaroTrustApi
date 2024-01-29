<?php

use App\Http\Controllers\Api\Admin\ActivityLogController;
use App\Http\Controllers\Api\Admin\AttachmentController;
use App\Http\Controllers\Api\Admin\Auth\AdminController;
use App\Http\Controllers\Api\Admin\Auth\AuthController;
use App\Http\Controllers\Api\Admin\Auth\PasswordResetController;
use App\Http\Controllers\Api\Admin\BreadcrumbCategoryController;
use App\Http\Controllers\Api\Admin\BreadcrumbController;
use App\Http\Controllers\Api\Admin\BreadcrumbSchemaController;
use App\Http\Controllers\Api\Admin\ContactUsController;
use App\Http\Controllers\Api\Admin\GoogleAnalyticsController;
use App\Http\Controllers\Api\Admin\GroupController;
use App\Http\Controllers\Api\Admin\GroupTypeController;
use App\Http\Controllers\Api\Admin\HomeController;
use App\Http\Controllers\Api\Admin\ItemController;
use App\Http\Controllers\Api\Admin\LanguageController;
use App\Http\Controllers\Api\Admin\LocalizationController;
use App\Http\Controllers\Api\Admin\MetaController;
use App\Http\Controllers\Api\Admin\NavigationController;
use App\Http\Controllers\Api\Admin\NavigationItemController;
use App\Http\Controllers\Api\Admin\NavigationItemSchemaController;
use App\Http\Controllers\Api\Admin\PageGroupController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\SchemaFeatureController;
use App\Http\Controllers\Api\Admin\SchemaFeatureTypeController;
use App\Http\Controllers\Api\Admin\TagController;
use App\Http\Controllers\Api\Admin\WebsiteController;
use App\Http\Controllers\Api\Admin\WebsiteLanguageController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function() {

    Route::prefix('auth')->group(function() {

        Route::controller(AuthController::class)->group(function () {
            Route::post('login', 'login')->middleware(['throttle:5,60']);
            Route::get('me', 'me');
            Route::put('update-password', 'changePassword');
            Route::post('logout', 'logout');
        });

    });

    Route::post('password/reset', [PasswordResetController::class, 'sendResetLinkEmail']);
    Route::post('password/reset/verify', [PasswordResetController::class, 'reset']);

    Route::controller(HomeController::class)->group(function () {
        Route::put('admins/self', 'updateSelfAccount');
    });

    Route::apiResource('admins', AdminController::class);
    Route::put('admins/password/{admin}', [AdminController::class, 'updatePassword']);

    Route::apiResource('websites', WebsiteController::class);

    Route::prefix('{website}')->controller(WebsiteLanguageController::class)->group(function () {
        Route::post('website-language/create-update', 'createUpdate');
        Route::get('website-language', 'index');
        Route::delete('website-language/{website_language}', 'destroy');
    });

    Route::apiResource('activity-logs', ActivityLogController::class)->only('index', 'show');

    Route::apiResource('{website}/groups', GroupController::class);
    Route::put('{website}/groups/{group}/translate', [GroupController::class, 'translate']);
    Route::post('{website}/groups/item', [GroupController::class, 'addItem']);

    Route::apiResource('{website}/group-types', GroupTypeController::class);

    Route::apiResource('{website}/tags', TagController::class);
    Route::put('{website}/tags/{tag}/translate', [TagController::class, 'translate']);
    Route::post('{website}/tags/item', [TagController::class, 'addItem']);

    Route::put('{website}/items/update-order', [ItemController::class, 'updateOrder']);
    Route::apiResource('{website}/items', ItemController::class);
    Route::get('{website}/items/primary/only', [ItemController::class, 'primaries']);
    Route::put('{website}/items/{item}/translate', [ItemController::class, 'translate']);
    Route::put('{website}/items/pageGroup/update', [ItemController::class, 'pageGroupUpdate']);
    Route::delete('{website}/items/remove-detail/{item}/{id}', [ItemController::class, 'removeDetail']);

    Route::apiResource('languages', LanguageController::class);

    Route::apiResource('roles', RoleController::class);

    Route::apiResource('{website}/localization', LocalizationController::class);

    Route::apiResource('{website}/contact-us-form', ContactUsController::class)->only('index', 'show');

    Route::apiResource('{website}/schema-features', SchemaFeatureController::class);

    Route::apiResource('{website}/schema-feature-types', SchemaFeatureTypeController::class);

    Route::apiResource('{website}/navigation-group', NavigationController::class);

    Route::apiResource('{website}/navigation-item', NavigationItemController::class);

    Route::prefix('attachments')->controller(AttachmentController::class)->group(function () {
        Route::post('/signed-url', 'getSignedUrl');
        Route::get('/{count?}', 'attachments')->where('count', "count");  // ->where(parameterName, allowed values)
        Route::post('upload-attachments', 'uploadAttachments');
        Route::delete('/', 'destroy');
    });

    Route::apiResource('{website}/breadcrumbs', BreadcrumbController::class);

    Route::apiResource('{website}/breadcrumb-category', BreadcrumbCategoryController::class);

    Route::apiResource('{website}/breadcrumb-schema', BreadcrumbSchemaController::class);

    Route::apiResource('{website}/meta', MetaController::class);

    Route::apiResource('{website}/navigation-item-schema', NavigationItemSchemaController::class);
    Route::delete('{website}/navigation-item-schema/remove-detail/{item}/{id}', [NavigationItemSchemaController::class, 'removeDetail']);

    Route::prefix('{website}')->controller(GoogleAnalyticsController::class)->group(function () {
        Route::get('analytics', 'analytics');
    });

    Route::apiResource('{website}/page-groups', PageGroupController::class);
});
