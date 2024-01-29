<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Breadcrumb;
use App\Models\BreadcrumbCategory;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class BreadcrumbCategoryController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/breadcrumb-category",
     *     tags={"Admin - breadcrumb category"},
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
        $breadcrumb_categories = BreadcrumbCategory::
        with('breadcrumbs')
        ->get();

        return $this->dataResponse([
            'categories' => $breadcrumb_categories
        ], null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/breadcrumb-category",
     *     tags={"Admin - breadcrumb category"},
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
     *                 required={"featureTitle", "page", "level"},
     *                 @OA\Property(property="path", type="string", example="Blah blah blah"),
     *                 @OA\Property(property="page", type="string", example="Blah blah blah"),
     *                 @OA\Property(property="level", type="integer", example=15),
     *             )
     *         )
     *     ),
     *
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
        $fields = checkApiValidationRules($request, BreadcrumbCategory::validationRules(), $this);
        $breadcrumb_category = null;

        try {
            $fields['websiteId'] = $website->id;

            // creating
            $breadcrumb_category = BreadcrumbCategory::create($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $breadcrumb_category->id,
                'entity' => "breadcrumb_categories",
                'action' => "create",
                'oldValue' => null,
                'newValue' => $breadcrumb_category->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The new breadcrumb_category has been stored successfully.", ['breadcrumb_category' => $breadcrumb_category]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/breadcrumb-category/{breadcrumb_category}",
     *     tags={"Admin - breadcrumb category"},
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
     *         name="breadcrumb_category",
     *         in="path",
     *         description="id of an existing breadcrumb_category.",
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
    public function show($website, $breadcrumb_category)
    {
        $breadcrumb_category = BreadcrumbCategory::where('websiteId', $website)->with('breadcrumbs')->findOrFail($breadcrumb_category);

        return $this->dataResponse([
            'category' => $breadcrumb_category
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/breadcrumb-category/{breadcrumb_category}",
     *     tags={"Admin - breadcrumb category"},
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
     *         name="breadcrumb_category",
     *         in="path",
     *         description="id of an existing breadcrumb_category.",
     *         required=true,
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"featureTitle", "page", "level"},
     *                 @OA\Property(property="path", type="string", example="Blah blah blah"),
     *                 @OA\Property(property="page", type="string", example="Blah blah blah"),
     *                 @OA\Property(property="level", type="integer", example=15),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="okay.",
     *         @OA\JsonContent(
     *             type="object",
     *         )
     *     ),
     *
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error."),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function update(Request $request, Website $website, BreadcrumbCategory $breadcrumb_category)
    {
        $fields = checkApiValidationRules($request, BreadcrumbCategory::validationRules($breadcrumb_category->id), $this);
        $old_values = $breadcrumb_category->fieldValues();

        try {
            // creating
            $breadcrumb_category->update($request->only('path'));

            if ($fields['breadcrumb'] ?? null)
            {
                $breadcrumb_id = $fields['breadcrumb']['id'] ??  null;
                
                if ($breadcrumb_id)
                    Breadcrumb::findOrFail($breadcrumb_id)->update($fields['breadcrumb']);
                else
                    $breadcrumb_category->breadcrumbs()->create($fields['breadcrumb']);
            }

            // storing activity log
            ActivityLog::create([
                'breadcrumb_categoryId' => $breadcrumb_category->id,
                'entityId' => $breadcrumb_category->id,
                'entity' => "breadcrumb_categories",
                'action' => "update",
                'oldValue' => $old_values,
                'newValue' => $breadcrumb_category->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The breadcrumb category has been updated successfully.", ['breadcrumb_category' => $breadcrumb_category]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/{website}/breadcrumb-category/{breadcrumb_category}",
     *     tags={"Admin - breadcrumb category"},
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
     *         name="breadcrumb_category",
     *         in="path",
     *         description="id of an existing breadcrumb_category.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="the breadcrumb_category has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function destroy(Website $website, BreadcrumbCategory $breadcrumb_category)
    {
        if ($breadcrumb_category->websiteId != $website->id)
            return $this->errorResponse("Bad request", [], 400);

        try {
            // deleting
            $breadcrumb_category->delete();

            // storing activity log
            ActivityLog::create([
                'breadcrumb_categoryId' => null,
                'entityId' => $breadcrumb_category->id,
                'entity' => "breadcrumb_categories",
                'action' => "delete",
                'oldValue' => $breadcrumb_category->fieldValues(),
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the breadcrumb_category has been deleted successfully.");
    }
}
