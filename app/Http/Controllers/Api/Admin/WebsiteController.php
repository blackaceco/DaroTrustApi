<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/websites",
     *     tags={"Admin - websites"},
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
        $websites = Website::with(['languages', 'websiteLanguages'])->get();

        return $this->dataResponse([
            'websites' => $websites
        ], null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/websites",
     *     tags={"Admin - websites"},
     *     summary="auth",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title", "slug"},
     *                 @OA\Property(property="title", type="string", example="Example Website Title"),
     *                 @OA\Property(property="slug", type="string", example="example_slug"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="okay.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The new website has been stored successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="website",
     *                     type="object",
     *                     @OA\Property(property="title", type="string", example="Example Website Title"),
     *                     @OA\Property(property="slug", type="string", example="example_slug"),
     *                     @OA\Property(property="updatedAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *                     @OA\Property(property="createdAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *                     @OA\Property(property="id", type="integer", example=1),
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
        $fields = checkApiValidationRules($request, Website::validationRules(), $this);
        $website = null;

        try {
            // creating
            $website = Website::create($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => null,
                'entityId' => $website->id,
                'entity' => "websites",
                'action' => "create",
                'oldValue' => null,
                'newValue' => $website->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The new website has been stored successfully.", ['website' => $website]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/websites/{id}",
     *     tags={"Admin - websites"},
     *     summary="auth",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of an existing website.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent (
     *              type="object",
     *              @OA\Property (property="id", type="integer", example="7"),
     *              @OA\Property (property="title", type="string", example="Example Website Title"),
     *              @OA\Property (property="slug", type="string", example="example_slug"),
     *              @OA\Property (property="createdAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *              @OA\Property (property="updatedAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *          )
     *      ),
     *      @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function show($website)
    {
        $website = Website::findOrFail($website);

        return $this->dataResponse([
            'website' => $website
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/websites/{id}",
     *     tags={"Admin - websites"},
     *     summary="auth",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of an existing admin.",
     *         required=true,
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title", "slug"},
     *                 @OA\Property(property="title", type="string", example="Example Website Title"),
     *                 @OA\Property(property="slug", type="string", example="example_slug"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="okay.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The new website has been stored successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="website",
     *                     type="object",
     *                     @OA\Property(property="title", type="string", example="Example Website Title"),
     *                     @OA\Property(property="slug", type="string", example="example_slug"),
     *                     @OA\Property(property="updatedAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *                     @OA\Property(property="createdAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *                     @OA\Property(property="id", type="integer", example=1),
     *                 ),
     *             ),
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error."),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function update(Request $request, Website $website)
    {
        $fields = checkApiValidationRules($request, Website::validationRules($website->id), $this);
        $old_values = $website->fieldValues();

        try {
            // creating
            $website->update($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => null,
                'entityId' => $website->id,
                'entity' => "websites",
                'action' => "update",
                'oldValue' => $old_values,
                'newValue' => $website->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The website has been updated successfully.", ['website' => $website]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/websites/{id}",
     *     tags={"Admin - websites"},
     *     summary="auth",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of an existing website.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="the website has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function destroy(Website $website)
    {
        try {
            // deleting
            $website->delete();

            // storing activity log
            ActivityLog::create([
                'websiteId' => null,
                'entityId' => $website->id,
                'entity' => "websites",
                'action' => "delete",
                'oldValue' => $website->fieldValues(),
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the website has been deleted successfully.");
    }
}
