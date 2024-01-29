<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Breadcrumb;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class BreadcrumbController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/breadcrumbs",
     *     tags={"Admin - breadcrumbs"},
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
    public function index()
    {
        $breadcrumbs = Breadcrumb::with(['category', 'language'])->get();

        return $this->dataResponse([
            'breadcrumbs' => $breadcrumbs
        ], null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/breadcrumbs",
     *     tags={"Admin - breadcrumbs"},
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
     *                 required={"featureTitle", "breadcrumbCategoryId", "languageId"},
     *                 @OA\Property(property="title", type="string", example="Example Feature Title"),
     *                 @OA\Property(property="breadcrumbCategoryId", type="integer", example=1),
     *                 @OA\Property(property="languageId", type="integer", example=1),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="okay.", 
     *         @OA\JsonContent(
     *             type="object",
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error.")
     * )
     */
    public function store(Request $request,Website $website)
    {
        $fields = checkApiValidationRules($request, Breadcrumb::validationRules(), $this);
        $breadcrumb = null;

        try {
            // creating
            $breadcrumb = Breadcrumb::create($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $breadcrumb->id,
                'entity' => "breadcrumbs",
                'action' => "create",
                'oldValue' => null,
                'newValue' => $breadcrumb->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The new breadcrumb has been stored successfully.", ['breadcrumb' => $breadcrumb]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/breadcrumbs/{breadcrumb}",
     *     tags={"Admin - breadcrumbs"},
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
     *         name="breadcrumb",
     *         in="path",
     *         description="id of an existing breadcrumb.",
     *         required=true,
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent (
     *              type="object",
     *          )
     *      ),
     *      @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function show(Website $website, $breadcrumb)
    {
        $breadcrumb = Breadcrumb::with(['category', 'language'])->findOrFail($breadcrumb);

        return $this->dataResponse([
            'breadcrumb' => $breadcrumb
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/breadcrumbs/{breadcrumb}",
     *     tags={"Admin - breadcrumbs"},
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
     *         name="breadcrumb",
     *         in="path",
     *         description="id of an existing breadcrumb.",
     *         required=true,
     *     ),
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"featureTitle", "breadcrumbCategoryId", "languageId"},
     *                 @OA\Property(property="title", type="string", example="Example Feature Title"),
     *                 @OA\Property(property="breadcrumbCategoryId", type="integer", example=1),
     *                 @OA\Property(property="languageId", type="integer", example=1),
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
     *             @OA\Property(property="message", type="string", example="The breadcrumb has been updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *             ),
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error."),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function update(Request $request, Website $website, Breadcrumb $breadcrumb)
    {
        $fields = checkApiValidationRules($request, Breadcrumb::validationRules($breadcrumb->id), $this);
        $old_values = $breadcrumb->fieldValues();

        try {
            // creating
            $breadcrumb->update($fields);

            // storing activity log
            ActivityLog::create([
                'breadcrumbId' => $breadcrumb->id,
                'entityId' => $breadcrumb->id,
                'entity' => "breadcrumbs",
                'action' => "update",
                'oldValue' => $old_values,
                'newValue' => $breadcrumb->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The breadcrumb has been updated successfully.", ['breadcrumb' => $breadcrumb]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/{website}/breadcrumbs/{breadcrumb}",
     *     tags={"Admin - breadcrumbs"},
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
     *         name="breadcrumb",
     *         in="path",
     *         description="id of an existing breadcrumb.",
     *         required=true,
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="the breadcrumb has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function destroy(Website $website, Breadcrumb $breadcrumb)
    {
        try {
            // deleting
            $breadcrumb->delete();

            // storing activity log
            ActivityLog::create([
                'breadcrumbId' => null,
                'entityId' => $breadcrumb->id,
                'entity' => "breadcrumbs",
                'action' => "delete",
                'oldValue' => $breadcrumb->fieldValues(),
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the breadcrumb has been deleted successfully.");
    }
}
