<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Language;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/languages",
     *     tags={"Admin - languages"},
     *     summary="auth",
     * 
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent(
     *              type="object",
     *          )
     *      )
     * )
     */
    public function index()
    {
        $languages = Language::get();

        return $this->dataResponse([
            'languages' => $languages
        ], null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/languages",
     *     tags={"Admin - languages"},
     *     summary="auth",
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title", "abbreviation", "direction"},
     *                 @OA\Property(property="title", type="string", example="Example Language Title"),
     *                 @OA\Property(property="abbreviation", type="string", example="ex"),
     *                 @OA\Property(property="direction", type="string", example="ltr"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="okay.", 
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The new language has been stored successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="language",
     *                     type="object",
     *                     @OA\Property(property="title", type="string", example="Example Language Title"),
     *                     @OA\Property(property="abbreviation", type="string", example="ex"),
     *                     @OA\Property(property="direction", type="string", example="ltr"),
     *                     @OA\Property(property="updatedAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *                     @OA\Property(property="createdAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *                     @OA\Property(property="id", type="integer", example=2),
     *                 ),
     *             ),
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error.")
     * )
     */
    public function store(Request $request)
    {
        $fields = checkApiValidationRules($request, Language::validationRules(), $this);
        $language = null;

        try {
            // creating
            $language = Language::create($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => null,
                'entityId' => $language->id,
                'entity' => "languages",
                'action' => "create",
                'oldValue' => null,
                'newValue' => $language->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The new language has been stored successfully.", ['language' => $language]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/languages/{id}",
     *     tags={"Admin - languages"},
     *     summary="auth",
     * 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of an existing language.",
     *         required=true,
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent (
     *              type="object",
     *              @OA\Property(property="id", type="integer", example=2),
     *              @OA\Property(property="title", type="string", example="Example Language Title"),
     *              @OA\Property(property="abbreviation", type="string", example="ex"),
     *              @OA\Property(property="direction", type="string", example="ltr"),
     *              @OA\Property(property="updatedAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *              @OA\Property(property="createdAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *          )
     *      ),
     *      @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function show($language)
    {
        $language = Language::findOrFail($language);

        return $this->dataResponse([
            'language' => $language
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/languages/{id}",
     *     tags={"Admin - languages"},
     *     summary="auth",
     * 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of an existing language.",
     *         required=true,
     *     ),
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title", "abbreviation", "direction"},
     *                 @OA\Property(property="title", type="string", example="Example Language Title"),
     *                 @OA\Property(property="abbreviation", type="string", example="ex"),
     *                 @OA\Property(property="direction", type="string", example="ltr"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="okay.", 
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The new language has been stored successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="language",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="title", type="string", example="Example Language Title"),
     *                     @OA\Property(property="abbreviation", type="string", example="ex"),
     *                     @OA\Property(property="direction", type="string", example="ltr"),
     *                     @OA\Property(property="updatedAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *                     @OA\Property(property="createdAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *                 ),
     *             ),
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error."),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function update(Request $request, Language $language)
    {
        $fields = checkApiValidationRules($request, Language::validationRules($language->id), $this);
        $old_values = $language->fieldValues();

        try {
            // creating
            $language->update($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => null,
                'entityId' => $language->id,
                'entity' => "languages",
                'action' => "update",
                'oldValue' => $old_values,
                'newValue' => $language->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The language has been updated successfully.", ['language' => $language]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/languages/{id}",
     *     tags={"Admin - languages"},
     *     summary="auth",
     * 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of an existing language.",
     *         required=true,
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="the language has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function destroy(Language $language)
    {
        try {
            // deleting
            $language->delete();

            // storing activity log
            ActivityLog::create([
                'websiteId' => null,
                'entityId' => $language->id,
                'entity' => "languages",
                'action' => "delete",
                'oldValue' => $language->fieldValues(),
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the language has been deleted successfully.");
    }
}
