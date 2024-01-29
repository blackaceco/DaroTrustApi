<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\NavigationGroup;
use App\Models\NavigationItem;
use App\Models\NavigationItemDetail;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class NavigationItemController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/navigation-item",
     *     tags={"Admin - navigation-item"},
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
     *              type="array",
     *              @OA\Items(type="object")
     *          )
     *      )
     * )
     */
    public function index(Request $request, Website $website)
    {
        $items = NavigationItem::with('details', 'children.details')
            ->where('websiteId', $website->id);

        $nav_group_id = $request->query('navigationGroupId', null);
        $schema_id = $request->query('navigationSchemaId', null);
        $parent_id = $request->query('parentId', null);

        if($nav_group_id) $items = $items->whereHas('groups', function ($query) use ($nav_group_id) {
            $query->where('navigationGroupId', $nav_group_id);
        });

        if($parent_id) $items = $items->whereHas('parent_pivot', function ($query) use ($parent_id) {
            $query->where('parentId', $parent_id);
        });

        if($schema_id) $items = $items->where('schemaId', $nav_group_id);


        $items = $items->get();

        return $this->dataResponse([
            'items' => $items
        ], null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/navigation-item",
     *     tags={"Admin - navigation-item"},
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
     *                 required={"featureTitle", "schemaId", "order", "visible", "details"},
     *                 @OA\Property(property="featureTitle", type="string", example="Example Feature Title"),
     *                 @OA\Property(property="schemaId", type="integer", example=1),
     *                 @OA\Property(property="order", type="integer", example=1),
     *                 @OA\Property(property="visible", type="boolean", example=1),
     *                 @OA\Property(property="details", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="languageId", type="integer", example=1),
     *                         @OA\Property(property="valueType", type="string", example="test"),
     *                         @OA\Property(property="value", type="string", example="test"),
     *                         @OA\Property(property="key", type="string", example="test"),
     *                     )
     *                 ),
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
    public function store(Request $request, Website $website)
    {
        $fields = checkApiValidationRules($request, NavigationItem::validationRules(), $this);
        $item = null;

        try {
            // add the websiteId
            $fields['websiteId'] = $website->id;

            // add visible and order
            $fields['visible'] = '1';
            $fields['order'] = 99;

            if($request->input('navigationGroupId')){

                $navigation_group = NavigationGroup::findOrFail($request->input('navigationGroupId'));

                // creating
                // $item = NavigationItem::create($fields);

                // UPDATE
                $item = $navigation_group->items()->create($fields);
            } else if($request->input('parentId')){

                $parent_item = NavigationItem::findOrFail($request->input('parentId'));

                // creating
                $item = $parent_item->children()->create($fields);
            }

            // prepare details
            $details = [];
            foreach ($request->input('details', []) as $detail) {
                $details[] = [
                    'itemId' => $item->id,
                    'languageId' => $detail['languageId'],
                    'valueType' => $detail['valueType'],
                    'key' => $detail['key'],
                    'value' => $detail['value'],
                ];
            }

            // attaching details
            $item->details()->createMany($details);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $item->id,
                'entity' => "navigation_items",
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
     *     path="/api/admin/{website}/navigation-item/{id}",
     *     tags={"Admin - navigation-item"},
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
     *         description="id of an existing navigation-item.",
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
        $item = NavigationItem::with(['details', 'schema'])->where('websiteId', $website->id)->findOrFail($item);

        return $this->dataResponse([
            'item' => $item
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/navigation-item/{item}",
     *     tags={"Admin - navigation-item"},
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
     *         description="id of an existing navigation-item.",
     *         required=true,
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"featureTitle", "schemaId", "order", "visible", "details"},
     *                 @OA\Property(property="featureTitle", type="string", example="Example Feature Title"),
     *                 @OA\Property(property="schemaId", type="integer", example=1),
     *                 @OA\Property(property="order", type="integer", example=1),
     *                 @OA\Property(property="visible", type="boolean", example=1),
     *                 @OA\Property(property="details", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="languageId", type="integer", example=1),
     *                         @OA\Property(property="valueType", type="string", example="test"),
     *                         @OA\Property(property="value", type="string", example="test"),
     *                         @OA\Property(property="key", type="string", example="test"),
     *                     )
     *                 ),
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
     *     @OA\Response(response="422", description="validation error."),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function update(Request $request, Website $website, $item)
    {
        $item = NavigationItem::with(['details', 'schema'])->findOrFail($item);
        $fields = checkApiValidationRules($request, NavigationItem::validationRules($item->id), $this);
        $old_values = $item->fieldValues();

        try {
            // creating
            // $item->update($fields);



            // prepare details
            $details = [];
            foreach ($request->input('details', []) as $detail) {
                $details[] = [
                    'id' => array_key_exists('id', $detail) ? $detail['id'] : null,
                    'itemId' => $item->id,
                    'valueType' => $detail['valueType'],
                    'key' => $detail['key'],
                    'value' => $detail['value'],
                ];
            }

            // syncing details
            // foreach ($item->details as $detail) {
            //     foreach ($details as $newDetail) {
            //         if ($detail->itemId == $newDetail['itemId'])
            //             $detail->update($newDetail);
            //         else
            //             $item->details()->create($newDetail);
            //     }
            // }

            foreach ($request->input('details', []) as $detail) {
                if (array_key_exists('id', $detail) && !is_null($item['id']))
                    $item->details()->find($detail['id'])->update($detail);
                else
                    $item->details()->create($detail);
            }

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $item->id,
                'entity' => "navigation_items",
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
     *     path="/api/admin/{website}/navigation-item/{item}",
     *     tags={"Admin - navigation-item"},
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
     *         description="id of an existing navigation-item.",
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
            $item = NavigationItem::where('websiteId', $website->id)->findOrFail($item);
            $old_values = $item->fieldValues();
            $item->delete();

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $item->id,
                'entity' => "navigation_items",
                'action' => "delete",
                'oldValue' => $old_values,
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the item has been deleted successfully.");
    }
}

