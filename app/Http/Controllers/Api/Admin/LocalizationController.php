<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Localization;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LocalizationController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/localizations",
     *     tags={"Admin - localizations"},
     *     summary="auth",
     *
     *     @OA\Parameter(
     *         name="website",
     *         in="path",
     *         description="id of an existing website.",
     *         required=true,
     *     ),
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
    public function index(Website $website)
    {
        $localizations = Localization::with(['website', 'details.language'])->where('websiteId', $website->id)->get();

        return $this->dataResponse([
            'localizations' => $localizations
        ], null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/localizations",
     *     tags={"Admin - localizations"},
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
     *                 required={"websiteId", "languageId"},
     *                 @OA\Property(property="websiteId", type="integer", example=1),
     *                 @OA\Property(property="languageId", type="integer", example=1),
     *                 @OA\Property(property="key", type="string", example="Blah blah blah"),
     *                 @OA\Property(property="value", type="string", example="Example example"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="okay.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The new localization has been stored successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="localization",
     *                     type="object",
     *                     @OA\Property(property="key", type="string", example="Blah blah blah"),
     *                     @OA\Property(property="updatedAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *                     @OA\Property(property="createdAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="website", type="object"),
     *                     @OA\Property(property="details", type="array", @OA\Items(type="object")),
     *                 ),
     *             ),
     *         )
     *     ),
     *
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error.")
     * )
     */
    public function store(Request $request, Website $website)
    {
        $fields = checkApiValidationRules($request, Localization::validationRules(), $this);
        $localization = null;

        try {
            // getting the website
            $fields['websiteId'] = $website->id;

            // creating
            $localization = Localization::create($fields);

            unset($fields['websiteId'], $fields['key']);

            // add details
            $localization->details()->create($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $localization->id,
                'entity' => "localizations",
                'action' => "create",
                'oldValue' => null,
                'newValue' => $localization->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The new localization has been stored successfully.", ['localization' => $localization]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/localizations/{id}",
     *     tags={"Admin - localizations"},
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
     *         name="id",
     *         in="path",
     *         description="id of an existing localization.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer", example="7"),
     *              @OA\Property(property="key", type="string", example="more"),
     *              @OA\Property(property="createdAt", type="string", example="2023-10-01T08:20:37.000000Z"),
     *              @OA\Property(property="updatedAt", type="string", example="2023-10-01T08:20:37.000000Z"),
     *              @OA\Property(property="details", type="array", @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id", type="integer", example=1),
     *                  @OA\Property(property="language", type="integer", example=1),
     *                  @OA\Property(property="value", type="string", example="more in Kurdish"),
     *             )),
     *          )
     *      ),
     *      @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function show($website, $localization)
    {
        $localization = Localization::with(['website', 'details.language'])->where('websiteId', $website)->findOrFail($localization);

        return $this->dataResponse([
            'localization' => $localization
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/localizations/{id}",
     *     tags={"Admin - localizations"},
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
     *         name="id",
     *         in="path",
     *         description="id of an existing localization.",
     *         required=true,
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"websiteId", "languageId"},
     *                 @OA\Property(property="websiteId", type="integer", example=1),
     *                 @OA\Property(property="languageId", type="integer", example=1),
     *                 @OA\Property(property="key", type="string", example="Blah blah blah"),
     *                 @OA\Property(property="value", type="string", example="Example example"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="okay.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The new localization has been stored successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="localization",
     *                     type="object",
     *                     @OA\Property(property="key", type="string", example="Blah blah blah"),
     *                     @OA\Property(property="updatedAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *                     @OA\Property(property="createdAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="website", type="object"),
     *                     @OA\Property(property="details", type="array", @OA\Items(type="object")),
     *                 ),
     *             ),
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error."),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function update(Request $request, Website $website, Localization $localization)
    {
        $fields = checkApiValidationRules($request, Localization::validationRules($localization->id), $this);
        $old_values = $localization->fieldValues();

        try {
            $fields['websiteId'] = $website->id;

            // updating
            // $localization->update($fields);
            // unset($fields['websiteId'], $fields['key']);
            // $localization->details()->update($fields);

            // get detail by languageId
            $detail = $localization->details()->where('languageId', $fields['languageId'])->first();

            if($detail){
                // update existing detail
                $detail->update(['value' => $fields['value']]);
            } else {
                // add new detail
                $localization->details()->create($fields);
            }

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $localization->id,
                'entity' => "localizations",
                'action' => "update",
                'oldValue' => $old_values,
                'newValue' => $localization->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The localization has been updated successfully.", ['localization' => $localization]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/{website}/localizations/{id}",
     *     tags={"Admin - localizations"},
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
     *         name="id",
     *         in="path",
     *         description="id of an existing localization.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="the localization has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function destroy($website, Localization $localization)
    {
        try {
            // deleting
            $localization->where('websiteId', $website)->delete();

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website,
                'entityId' => $localization->id,
                'entity' => "localizations",
                'action' => "delete",
                'oldValue' => $localization->fieldValues(),
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the localization has been deleted successfully.");
    }
}
