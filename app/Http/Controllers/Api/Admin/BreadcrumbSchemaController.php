<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BreadcrumbSchema;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class BreadcrumbSchemaController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/breadcrumb-schema",
     *     tags={"Admin - breadcrumb schema"},
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
        $breadcrumb_schemas = BreadcrumbSchema::get();

        return $this->dataResponse([
            'breadcrumb_schemas' => $breadcrumb_schemas
        ], null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/breadcrumb-schema",
     *     tags={"Admin - breadcrumb schema"},
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
        $fields = checkApiValidationRules($request, BreadcrumbSchema::validationRules(), $this);
        $breadcrumb_schema = null;

        try {
            $fields['websiteId'] = $website->id;

            // creating
            $breadcrumb_schema = BreadcrumbSchema::create($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $breadcrumb_schema->id,
                'entity' => "breadcrumb_schemas",
                'action' => "create",
                'oldValue' => null,
                'newValue' => $breadcrumb_schema->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The new breadcrumb_schema has been stored successfully.", ['breadcrumb_schema' => $breadcrumb_schema]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/breadcrumb-schema/{breadcrumb_schema}",
     *     tags={"Admin - breadcrumb schema"},
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
     *         name="breadcrumb_schema",
     *         in="path",
     *         description="id of an existing breadcrumb_schema.",
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
    public function show($website, $breadcrumb_schema)
    {
        $breadcrumb_schema = BreadcrumbSchema::where('websiteId', $website)->findOrFail($breadcrumb_schema);

        return $this->dataResponse([
            'schema' => $breadcrumb_schema
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/breadcrumb-schema/{breadcrumb_schema}",
     *     tags={"Admin - breadcrumb schema"},
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
     *         name="breadcrumb_schema",
     *         in="path",
     *         description="id of an existing breadcrumb_schema.",
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
    public function update(Request $request, Website $website, BreadcrumbSchema $breadcrumb_schema)
    {
        $fields = checkApiValidationRules($request, BreadcrumbSchema::validationRules($breadcrumb_schema->id), $this);
        $old_values = $breadcrumb_schema->fieldValues();

        try {
            // creating
            $breadcrumb_schema->update($fields);

            // storing activity log
            ActivityLog::create([
                'breadcrumb_schemaId' => $breadcrumb_schema->id,
                'entityId' => $breadcrumb_schema->id,
                'entity' => "breadcrumb_schemas",
                'action' => "update",
                'oldValue' => $old_values,
                'newValue' => $breadcrumb_schema->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The breadcrumb_schema has been updated successfully.", ['breadcrumb_schema' => $breadcrumb_schema]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/{website}/breadcrumb-schema/{breadcrumb_schema}",
     *     tags={"Admin - breadcrumb schema"},
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
     *         name="breadcrumb_schema",
     *         in="path",
     *         description="id of an existing breadcrumb_schema.",
     *         required=true,
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="the breadcrumb_schema has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function destroy(Website $website, BreadcrumbSchema $breadcrumb_schema)
    {
        if ($breadcrumb_schema->websiteId != $website->id)
            return $this->errorResponse("Bad request", [], 400);

        try {
            // deleting
            $breadcrumb_schema->delete();

            // storing activity log
            ActivityLog::create([
                'breadcrumb_schemaId' => null,
                'entityId' => $breadcrumb_schema->id,
                'entity' => "breadcrumb_schemas",
                'action' => "delete",
                'oldValue' => $breadcrumb_schema->fieldValues(),
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the breadcrumb_schema has been deleted successfully.");
    }
}
