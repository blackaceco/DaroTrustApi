<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class WebsiteLanguageController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/website-language/create-update",
     *     tags={"Admin - website languages"},
     *     summary="auth",
     *
     *     @OA\Parameter(
     *         name="website",
     *         in="path",
     *         description="id of an existing website.",
     *         required=true,
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"languages"},
     *                 @OA\Property(
     *                     property="languages",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="languageId", type="integer", example=3),
     *                         @OA\Property(property="active", type="boolean", example=true),
     *                         @OA\Property(property="default", type="boolean", example=false),
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="okay.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The website's languages has been updated successfully."),
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error."),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function createUpdate(Request $request, Website $website)
    {
        // validation
        checkApiValidationRules($request, [
            'languages' => "required|array",
            'languages.*.languageId' => "required|exists:languages,id",
        ], $this);

        $old_values = $website->fieldValues();

        // this variable will use to holding the language's ids then update the existing pivot table without them.
        $requested_languageIds = [];

        // looping the requested languages
        foreach ($request->input('languages', []) as $language) {
            // holding language id
            $requested_languageIds[] = $language['languageId'];

            // if exist then update
            if ($website->languages->contains($language['languageId']))
            {
                $website->languages()->updateExistingPivot($language['languageId'], [
                    'active' => $language['active'] ?? false,
                    'default' => $language['default'] ?? false,
                ]);
            // if not exist then create
            }else
            {
                $website->languages()->attach($language['languageId'], [
                    'active' => $language['active'] ?? false,
                    'default' => $language['default'] ?? false,
                ]);
            }
        }

        // select from the pivot without the requested language ids and set thier status of active to false
        $website->languages()->wherePivotNotIn('languageId', $requested_languageIds)->update([
            'active' => false,
            'default' => false,
        ]);

        // storing activity log
        ActivityLog::create([
            'websiteId' => $website->id,
            'entityId' => null,
            'entity' => "website_languages",
            'action' => "update",
            'oldValue' => $old_values,
            'newValue' => $website->fieldValues(),
        ]);

        // responsing
        return $this->successResponse("The website's languages has been updated successfully.");
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/website-language",
     *     tags={"Admin - website languages"},
     *     summary="auth",
     *
     *      @OA\Parameter(
     *         name="website",
     *         in="path",
     *         description="id of an existing website.",
     *         required=true,
     *      ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent(
     *              type="array",
     *                  @OA\Items(
     *                      @OA\Property (property="id", type="integer", example="7"),
     *                      @OA\Property (property="title", type="string", example="English"),
     *                      @OA\Property (property="abbreviation", type="string", example="en"),
     *                      @OA\Property (property="direction", type="string", example="ltr"),
     *                      @OA\Property (property="createdAt", type="string", example="2023-10-03T13:09:41.000000Z"),
     *                      @OA\Property (property="updatedAt", type="string", example="2023-10-04T06:09:19.000000Z"),
     *                      @OA\Property (
     *                          property="pivot",
     *                          type="object",
     *                          @OA\Property (property="websiteId", type="integer", example="1"),
     *                          @OA\Property (property="languageId", type="integer", example="2"),
     *                          @OA\Property (property="active", type="boolean", example=1),
     *                          @OA\Property (property="default", type="boolean", example=0),
     *                      ),
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function index(Website $website)
    {
        return $this->dataResponse($website->languages, null);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/{website}/website-language/{website_language}",
     *     tags={"Admin - website languages"},
     *     summary="auth",
     *
     *     @OA\Parameter(
     *         name="website",
     *         in="path",
     *         description="id of an existing website.",
     *         required=true,
     *     ),
     *
     *     @OA\Parameter(
     *         name="website_language",
     *         in="path",
     *         description="id of an existing website language.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="the website's language has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function destroy(Website $website, $website_language)
    {
        if (!$website->languages->contains($website_language))
            return $this->errorResponse("Invalid website language id !");

        try {
            // deleting
            $website->languages()->detach($website_language);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $website_language,
                'entity' => "website_languages",
                'action' => "create",
                'oldValue' => null,
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the website's language has been deleted successfully.");
    }
}
