<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\ItemDetail;
use App\Models\NavigationItemDetailSchema;
use App\Models\NavigationItemSchema;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class NavigationItemSchemaController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/navigation-item-schema",
     *     tags={"Admin - navigation item schema"},
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
    public function index(Request $request, Website $website)
    {
        $items = NavigationItemSchema::with(['website', 'details'])->where('websiteId', $website->id);

        $navigation_group_id = $request->query('navigationGroupId', null);

        if($navigation_group_id){
            $items = $items->whereHas('navigationGroups', function($q) use ($navigation_group_id){
                $q->where('navigationGroupId', $navigation_group_id);
            });
        }

        $items = $items->get();

        return $this->dataResponse([
            'items' => $items
        ], null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/navigation-item-schema",
     *     tags={"Admin - navigation item schema"},
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
     *                 required={"featureTitle", "languageId", "schemaId", "visible", "values"},
     *                 @OA\Property(property="featureTitle", type="string", example="Example Feature Title"),
     *                 @OA\Property(property="min", type="integer", example=2),
     *                 @OA\Property(property="max", type="integer", example=4),
     *                 @OA\Property(property="sortable", type="boolean", example=1),
     *                 @OA\Property(property="details", type="array",
     *                     @OA\Items(
     *                     @OA\Property(property="valueKey", type="string", example="test"),
     *                     @OA\Property(property="valueType", type="string", example="test"),
     *                     )
     *                 ),
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
    public function store(Request $request, Website $website)
    {
        $fields = checkApiValidationRules($request, NavigationItemSchema::validationRules(), $this);
        $item = null;

        try {
            // add the websiteId
            $fields['websiteId'] = $website->id;

            // creating
            $item = NavigationItemSchema::create($fields);

            // attaching details
            $item->details()->createMany($request->input('details'));

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $item->id,
                'entity' => "navigation_item_schemas",
                'action' => "create",
                'oldValue' => null,
                'newValue' => $item->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The new item has been stored successfully.", ['item' => $item]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/navigation-item-schema/{id}",
     *     tags={"Admin - navigation item schema"},
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
     *         description="id of an existing navigation-item-schema.",
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
    public function show(Website $website, $item)
    {
        $item = NavigationItemSchema::with(['website', 'details', 'children'])->where('websiteId', $website->id)->findOrFail($item);

        return $this->dataResponse([
            'item' => $item
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/navigation-item-schema/{id}",
     *     tags={"Admin - navigation item schema"},
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
     *         description="id of an existing item.",
     *         required=true,
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"featureTitle", "languageId", "schemaId", "visible", "values"},
     *                 @OA\Property(property="featureTitle", type="string", example="Example Feature Title"),
     *                 @OA\Property(property="min", type="integer", example=2),
     *                 @OA\Property(property="max", type="integer", example=4),
     *                 @OA\Property(property="sortable", type="boolean", example=1),
     *                 @OA\Property(property="details", type="array",
     *                     @OA\Items(
     *                     @OA\Property(property="valueKey", type="string", example="test"),
     *                     @OA\Property(property="valueType", type="string", example="test"),
     *                     )
     *                 ),
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
     *     @OA\Response(response="422", description="validation error."),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function update(Request $request, Website $website, $item)
    {
        $fields = checkApiValidationRules($request, NavigationItemSchema::validationRules($item), $this);

        $item = NavigationItemSchema::with('details')->findOrFail($item);

        $old_values = $item->fieldValues();

        try {
            // creating
            $item->update($fields);

            // syncing details
            foreach ($item->details as $detail) {
                foreach ($request->input('details') as $newDetail) {
                    if ($detail->itemId == $item->id)
                        $detail->update($newDetail);
                    else
                        $item->details()->create($newDetail);
                }
            }

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $item->id,
                'entity' => "navigation_item_schemas",
                'action' => "update",
                'oldValue' => $old_values,
                'newValue' => $item->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The item has been updated successfully.", ['item' => $item]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/{website}/navigation-item-schema/{id}",
     *     tags={"Admin - navigation item schema"},
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
     *         description="id of an existing navigation-item-schema.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="the item has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function destroy(Website $website, $item)
    {
        try {
            // deleting
            $item = NavigationItemSchema::where('websiteId', $website->id)->findOrFail($item);
            $item->delete();

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $item->id,
                'entity' => "navigation_item_schemas",
                'action' => "delete",
                'oldValue' => $item->fieldValues(),
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the item has been deleted successfully.");
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/{website}/navigation-item-schema/remove-detail/{item}/{id}",
     *     tags={"Admin - navigation item schema"},
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
     *         name="item",
     *         in="path",
     *         description="id of an existing navigation-item-schema.",
     *         required=true,
     *     ),
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of an existing navigation-item-schema detail.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="the item detail has been deleted successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function removeDetail(Website $website, NavigationItemSchema $item, $id)
    {
        try {
            // deleting
            NavigationItemDetailSchema::findOrFail($id)->delete();

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $item->id,
                'entity' => "navigation_item_detail_schema",
                'action' => "delete",
                'oldValue' => $item->fieldValues(),
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the item detail has been deleted successfully.");
    }
}

