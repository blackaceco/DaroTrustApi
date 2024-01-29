<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SchemaFeatureType;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class SchemaFeatureTypeController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/schema_feature_types",
     *     tags={"Admin - schema_feature_types"},
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
        $schema_feature_types = SchemaFeatureType::with('schemaFeature')->get();

        return $this->dataResponse([
            'schema_feature_types' => $schema_feature_types
        ], null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/schema_feature_types",
     *     tags={"Admin - schema_feature_types"},
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
     *                 required={"schemaFeatureId", "valueKey", "valueType"},
     *                 @OA\Property(property="schemaFeatureId", type="integer", example=1),
     *                 @OA\Property(property="valueKey", type="string", example="Blah blah blah"),
     *                 @OA\Property(property="valueType", type="string", example="Blah blah blah"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200", 
     *         description="okay.", 
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The new schema_feature_type has been stored successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="schema_feature_type",
     *                     type="object"
     *                 ),
     *             ),
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error.")
     * )
     */
    public function store(Request $request, Website $website)
    {
        $fields = checkApiValidationRules($request, SchemaFeatureType::validationRules(), $this);
        $schema_feature_type = null;

        try {
            // creating
            $schema_feature_type = SchemaFeatureType::create($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $schema_feature_type->id,
                'entity' => "schema_feature_types",
                'action' => "create",
                'oldValue' => null,
                'newValue' => $schema_feature_type->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The new schema feature type has been stored successfully.", ['schema_feature_type' => $schema_feature_type]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/schema_feature_types/{schema_feature_type}",
     *     tags={"Admin - schema_feature_types"},
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
     *         name="schema_feature_type",
     *         in="path",
     *         description="id of an existing schema_feature_type.",
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
    public function show($website, $schema_feature_type)
    {
        $schema_feature_type = SchemaFeatureType::findOrFail($schema_feature_type);

        return $this->dataResponse([
            'type' => $schema_feature_type
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/schema_feature_types/{schema_feature_type}",
     *     tags={"Admin - schema_feature_types"},
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
     *         name="schema_feature_type",
     *         in="path",
     *         description="id of an existing schema_feature_type.",
     *         required=true,
     *     ),
     * 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"schemaFeatureId", "valueKey", "valueType"},
     *                 @OA\Property(property="schemaFeatureId", type="integer", example=1),
     *                 @OA\Property(property="valueKey", type="string", example="Blah blah blah"),
     *                 @OA\Property(property="valueType", type="string", example="Blah blah blah"),
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
     *             @OA\Property(property="message", type="string", example="The schema_feature_type has been updated successfully."),
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
    public function update(Request $request, Website $website, SchemaFeatureType $schema_feature_type)
    {
        $fields = checkApiValidationRules($request, SchemaFeatureType::validationRules($schema_feature_type->id), $this);
        $old_values = $schema_feature_type->fieldValues();

        try {
            // creating
            $schema_feature_type->update($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $schema_feature_type->id,
                'entity' => "schema_feature_types",
                'action' => "update",
                'oldValue' => $old_values,
                'newValue' => $schema_feature_type->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The schema_feature_type has been updated successfully.", ['schema_feature_type' => $schema_feature_type]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/{website}/schema_feature_types/{schema_feature_type}",
     *     tags={"Admin - schema_feature_types"},
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
     *         name="schema_feature_type",
     *         in="path",
     *         description="id of an existing schema_feature_type.",
     *         required=true,
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="the schema_feature_type has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function destroy(Website $website, SchemaFeatureType $schema_feature_type)
    {
        try {
            // deleting
            $schema_feature_type->delete();

            // storing activity log
            ActivityLog::create([
                'websiteId' => null,
                'entityId' => $schema_feature_type->id,
                'entity' => "schema_feature_types",
                'action' => "delete",
                'oldValue' => $schema_feature_type->fieldValues(),
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the schema_feature_type has been deleted successfully.");
    }
}
