<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SchemaFeature;
use App\Models\SchemaFeatureHierarchy;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class SchemaFeatureController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/schema-features",
     *     tags={"Admin - schema-features"},
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
        $schema_features = SchemaFeature::get();

        return $this->dataResponse($schema_features, null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/schema-features",
     *     tags={"Admin - schema-features"},
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
     *                 required={"min", "nax", "featureTitle", "sortable", "groupable"},
     *                 @OA\Property (property="pageGroupId", type="integer", example=1),
     *                 @OA\Property (property="primary", type="boolean", example=1),
     *                 @OA\Property (property="parentId", type="integer", example=1),
     *                 @OA\Property (property="min", type="integer", example=2),
     *                 @OA\Property (property="max", type="integer", example=4),
     *                 @OA\Property (property="featureTitle", type="string", example="Blah blah blah"),
     *                 @OA\Property (property="sortable", type="boolean", example=true),
     *                 @OA\Property (property="groupable", type="boolean", example=false),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="okay.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The new schema_feature has been stored successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="schema_feature",
     *                     type="object",
     *                     @OA\Property (property="min", type="integer", example=2),
     *                     @OA\Property (property="max", type="integer", example=4),
     *                     @OA\Property (property="featureTitle", type="string", example="Blah blah blah"),
     *                     @OA\Property (property="sortable", type="boolean", example=true),
     *                     @OA\Property (property="groupable", type="boolean", example=false),
     *                     @OA\Property (property="createdAt", type="string", example="2023-10-08T11:36:06.000000Z"),
     *                     @OA\Property (property="updatedAt", type="string", example="2023-10-08T11:36:06.000000Z"),
     *                     @OA\Property (property="id", type="integer", example=1),
     *                     @OA\Property (property="page_groups", type="array", @OA\Items(type="object")),
     *                     @OA\Property (property="children", type="array", @OA\Items(type="object")),
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
        $fields = checkApiValidationRules($request, SchemaFeature::validationRules(), $this);
        $schema_feature = null;

        try {
            // add the websiteId
            $fields['websiteId'] = $website->id;

            // creating
            $schema_feature = SchemaFeature::create($fields);

            if ($request->has('pageGroupId'))
                $schema_feature->pageGroups()->attach([
                    $request->input('pageGroupId') => ['primary' => $request->boolean('primary')]
                ]);
            else if ($request->has('parentId'))
                SchemaFeatureHierarchy::create([
                    'parentId' => $fields['parentId'],
                    'childId' => $schema_feature->id
                ]);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $schema_feature->id,
                'entity' => "schema_features",
                'action' => "create",
                'oldValue' => null,
                'newValue' => $schema_feature->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The new schema feature has been stored successfully.", ['schema_feature' => $schema_feature]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/schema-features/{schema_feature}",
     *     tags={"Admin - schema-features"},
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
     *         name="schema_feature",
     *         in="path",
     *         description="id of an existing schema_feature.",
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
    public function show($website, $schema_feature)
    {
        $schema_feature = SchemaFeature::where('websiteId', $website)->with(['pageGroups', 'children', 'types', 'groupTypes.details'])->findOrFail($schema_feature);

        return $this->dataResponse([
            'schemaFeature' => $schema_feature
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/schema-features/{schema_feature}",
     *     tags={"Admin - schema-features"},
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
     *         name="schema_feature",
     *         in="path",
     *         description="id of an existing schema_feature.",
     *         required=true,
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"min", "nax", "featureTitle", "sortable", "groupable"},
     *                 @OA\Property (property="pageGroupId", type="integer", example=1),
     *                 @OA\Property (property="primary", type="boolean", example=1),
     *                 @OA\Property (property="parentId", type="integer", example=1),
     *                 @OA\Property (property="min", type="integer", example=2),
     *                 @OA\Property (property="max", type="integer", example=4),
     *                 @OA\Property (property="featureTitle", type="string", example="Blah blah blah"),
     *                 @OA\Property (property="sortable", type="boolean", example=true),
     *                 @OA\Property (property="groupable", type="boolean", example=false),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="okay.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The new schema_feature has been stored successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="schema_feature",
     *                     type="object",
     *                     @OA\Property (property="min", type="integer", example=2),
     *                     @OA\Property (property="max", type="integer", example=4),
     *                     @OA\Property (property="featureTitle", type="string", example="Blah blah blah"),
     *                     @OA\Property (property="sortable", type="boolean", example=true),
     *                     @OA\Property (property="groupable", type="boolean", example=false),
     *                     @OA\Property (property="createdAt", type="string", example="2023-10-08T11:36:06.000000Z"),
     *                     @OA\Property (property="updatedAt", type="string", example="2023-10-08T11:36:06.000000Z"),
     *                     @OA\Property (property="id", type="integer", example=1),
     *                     @OA\Property (property="page_groups", type="array", @OA\Items(type="object")),
     *                     @OA\Property (property="children", type="array", @OA\Items(type="object")),
     *                 ),
     *             ),
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error."),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function update(Request $request, Website $website, SchemaFeature $schema_feature)
    {
        $fields = checkApiValidationRules($request, SchemaFeature::validationRules($schema_feature->id), $this);
        $old_values = $schema_feature->fieldValues();

        try {
            // creating
            $schema_feature->update($fields);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $schema_feature->id,
                'entity' => "schema_features",
                'action' => "update",
                'oldValue' => $old_values,
                'newValue' => $schema_feature->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The schema_feature has been updated successfully.", ['schema_feature' => $schema_feature]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/{website}/schema-features/{schema_feature}",
     *     tags={"Admin - schema-features"},
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
     *         name="schema_feature",
     *         in="path",
     *         description="id of an existing schema_feature.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="the schema_feature has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function destroy(Website $website, SchemaFeature $schema_feature)
    {
        if ($schema_feature->websiteId != $website->id)
            return $this->errorResponse("Bad request", [], 400);

        try {
            // deleting
            $schema_feature->delete();

            // storing activity log
            ActivityLog::create([
                'schema_featureId' => null,
                'entityId' => $schema_feature->id,
                'entity' => "schema_features",
                'action' => "delete",
                'oldValue' => $schema_feature->fieldValues(),
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the schema feature has been deleted successfully.");
    }
}
