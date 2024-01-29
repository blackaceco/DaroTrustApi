<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Item;
use App\Models\ItemDetail;
use App\Models\ItemHierarchy;
use App\Models\SchemaFeature;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/items",
     *     tags={"Admin - items"},
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
        $items = Item::with(['website', 'details', 'schema', 'pageGroups', 'groups.details', 'tags.details'])->where('websiteId', $website->id);

        // where clause based on page group's id
        if ($request->has('pageGroupId'))
            $items = $items->whereHas('pageGroups', function ($query) use ($request) {
                $query->where('pageGroupId', $request->input('pageGroupId'));
            });

        // where clause based on schemaFeatureId
        if ($request->has('schemaFeatureId'))
            $items = $items->where('schemaId', $request->input('schemaFeatureId'));

        // where clause based on parentId
        if ($request->has('parentId')){
            /**
             * UPDATE Hema, 2023-11-27
             */
            $items = $items->whereRelation('parent_pivot', 'parentId', $request->input('parentId') );
            // $items = $items->find( $request->input('parentId') )->children();
        }

        return $this->dataResponse([
            'items' => $items->get()
        ], null, false);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/items/primary/only",
     *     tags={"Admin - items"},
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
    public function primaries(Request $request, Website $website)
    {
        $primaryItems = Item::with(['website', 'details', 'schema', 'pageGroups', 'groups.details'])
            ->where('websiteId', $website->id)
            ->where('schemaId', $request->input('schemaFeatureId'));
        // ->whereHas('schema', function ($schema) {
        //     $schema->whereHas('pageGroups', function ($group) {
        //         $group->where('primary', true);
        //     });
        // });

        $items = Item::with(['website', 'details', 'schema', 'pageGroups', 'groups.details'])->where('websiteId', $website->id)
        ->where('schemaId', $request->input('schemaFeatureId'));

        // select only primary page groups pivot
        // $primaryItems->whereHas('schema', function ($schema) {
        //     $schema->whereHas('pageGroups', function ($group) {
        //         $group->where('primary', true);
        //     });
        // });

        // where clause based on page group's id
        // $primaryItems = $items->whereHas('pageGroups', function ($query) use ($request) {
        //     $query->where('pageGroupId', $request->input('pageGroupId'));
        // });

        // where clause based on page group's id
        $items = $items->whereHas('pageGroups', function ($query) use ($request) {
            $query->where('pageGroupId', $request->input('pageGroupId'));
        });

        // selecting
        $primaryItems = $primaryItems->get();
        $items = $items->get();

        return $this->dataResponse([
            'primaryItems' => $primaryItems,
            'items' => $items,
        ], null, false);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/{website}/items",
     *     tags={"Admin - items"},
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
     *                 required={"schemaFeatureId", "languageId", "values"},
     *
     *                 @OA\Property(property="languageId", type="integer", example=1),
     *                 @OA\Property(property="schemaFeatureId", type="integer", example=1),
     *                 @OA\Property(property="visible", type="boolean", example=1),
     *                 @OA\Property(property="groupId", type="integer", example=1),
     *                 @OA\Property(property="pageGroupId", type="integer", example=1),
     *                 @OA\Property(property="parentId", type="integer", example=1),
     *                 @OA\Property(property="tagIds", type="array",
     *                     @OA\Items(type="integer")
     *                 ),
     *                 @OA\Property(property="values", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="valueType", type="string", example="blah"),
     *                         @OA\Property(property="valueKey", type="string", example="blah"),
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="value", type="string", example="blah"),
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
        $fields = checkApiValidationRules($request, Item::validationRules(), $this);
        $item = null;

        try {
            // add the websiteId
            $fields['websiteId'] = $website->id;

            // update schema feature field
            $fields['schemaId'] = $request->input('schemaFeatureId');

            $schemeFeature = SchemaFeature::findOrFail($fields['schemaId']);

            // set featureTitle
            $fields['featureTitle'] = $schemeFeature->featureTitle;

            // set visible
            $fields['visible'] = true;

            // creating
            $item = Item::create($fields);

            // add groups
            if ($request->has('groupId'))
                $item->groups()->attach($fields['groupId']);

            // add tags
            if ($request->has('tagIds'))
                $item->tags()->attach($request->input('tagIds'));

            // prepare details
            $details = [];
            foreach ($request->input('values', []) as $detail) {
                $details[] = [
                    'itemId' => $item->id,
                    'languageId' => $fields['languageId'],
                    'valueType' => $detail['valueType'],
                    'key' => $detail['valueKey'],
                    'value' => $detail['value'],
                    'order' => 99, // $detail['order'],
                ];
            }

            // attaching details
            $item->details()->createMany($details);

            // attaching parent id if exist else attaching page group id
            if ($request->filled('parentId'))
                ItemHierarchy::create([
                    'parentId' => $request->input('parentId'),
                    'childId' => $item->id
                ]);
            elseif ($request->filled('pageGroupId'))
                $item->pageGroups()->attach($request->input('pageGroupId'));

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $item->id,
                'entity' => "items",
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
     * @OA\Put(
     *     path="/api/admin/{website}/items/{item}/translate",
     *     tags={"Admin - items"},
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
     *                 required={"details"},
     *                 @OA\Property(property="details", type="array", @OA\Items(
     *                     @OA\Property(property="languageId", type="integer", example="2"),
     *                     @OA\Property(property="title", type="string", example="Example Title"),
     *                 )),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="okay.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="The item has been translated successfully."),
     *         )
     *     ),
     *     @OA\Response(response="401", description="unauthenticated."),
     *     @OA\Response(response="422", description="validation error.")
     * )
     */
    public function translate(Request $request, Website $website, Item $item)
    {
        checkApiValidationRules($request, [
            'languageId' => "required|exists:languages,id", // ADDED 2023-10-28
            'values' => "required|array",
            'values.*' => "required|array",
            // 'values.*.languageId' => "required|exists:languages,id",
            'values.*.valueType' => "required|string|max:255",
            'values.*.valueKey' => "required|string|max:255",
            'values.*.value' => "required|string|max:255",
            // 'values.*.order' => "required|string|max:255",
        ], $this);
        $old_values = $item->fieldValues();

        $detail = $request->input('values')[0];

        // check for dupilcation
        if ($item->details()->where('languageId', $request->input('languageId'))->exists())
            return $this->errorResponse("Duplicate !", [], 423);

        try {
            // // attaching details
            // $item->details()->create($detail);

            $details = [];

            foreach ($request->input('values', []) as $detail) {
                $details[] = [
                    'itemId' => $item->id,
                    'languageId' => $request->input('languageId'),
                    'valueType' => $detail['valueType'],
                    'key' => $detail['valueKey'],
                    'value' => $detail['value'],
                    'order' => 99, // $detail['order'],
                ];
            }

            // attaching details
            $item->details()->createMany($details);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $item->id,
                'entity' => "items",
                'action' => "item translate",
                'oldValue' => $old_values,
                'newValue' => $item->fieldValues(),
            ]);

            // responsing
            return $this->successResponse("The item has been translated successfully.", []);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/items/pageGroup/update",
     *     tags={"Admin - items"},
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
     *                 required={"itemId", "pageGroupId"},
     *                 @OA\Property(property="itemId", type="integer", example=3),
     *                 @OA\Property(property="pageGroupId", type="integer", example=6),
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
    public function pageGroupUpdate(Request $request, Website $website)
    {
        $fields = checkApiValidationRules($request, [
            'itemId' => "required|exists:items,id",
            'pageGroupId' => "required|exists:page_groups,id",
        ], $this);

        // find the item if exist or throw 404
        $item = Item::findOrFail($fields['itemId']);

        try {
            // check if the pageGroupId is exist or not.
            $is_exist = $item->pageGroups()->wherePivot('pageGroupId', $fields['pageGroupId'])->exists();

            // if exist remove it
            if ($is_exist)
                $item->pageGroups()->detach($fields['pageGroupId']);
            // if not exist add it
            else
                $item->pageGroups()->attach($fields['pageGroupId']);

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $fields['itemId'],
                'entity' => "items",
                'action' => "item page group updation",
                'oldValue' => null,
                'newValue' => null,
            ]);

            // responsing
            return $this->successResponse("The item's page group has been updated successfully.", []);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/{website}/items/{id}",
     *     tags={"Admin - items"},
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
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent (
     *              type="object",
     *              @OA\Property (property="id", type="integer", example="7"),
     *              @OA\Property (property="featureTitle", type="string", example="Example Feature Title"),
     *              @OA\Property (property="order", type="integer", example=99),
     *              @OA\Property (property="visible", type="boolean", example=true),
     *              @OA\Property (property="createdAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *              @OA\Property (property="updatedAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *              @OA\Property(property="details", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="website", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="schema", type="array", @OA\Items(type="object")),
     *              @OA\Property(property="page_groups", type="array", @OA\Items(type="object")),
     *          )
     *      ),
     *      @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function show(Website $website, $item)
    {
        $item = Item::with(['website', 'details.language', 'schema', 'pageGroups', 'groups.details', 'tags.details'])->where('websiteId', $website->id)->findOrFail($item);

        return $this->dataResponse([
            'item' => $item
        ], null, false, true);
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/items/{id}",
     *     tags={"Admin - items"},
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
     *                 required={"schemaFeatureId", "languageId", "values"},
     *
     *                 @OA\Property(property="languageId", type="integer", example=1),
     *                 @OA\Property(property="tagIds", type="array",
     *                     @OA\Items(type="integer")
     *                 ),
     *                 @OA\Property(property="values", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="valueType", type="string", example="blah"),
     *                         @OA\Property(property="valueKey", type="string", example="blah"),
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="value", type="string", example="blah"),
     *                     )
     *                 ),
     *                 @OA\Property(property="newValues", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="valueType", type="string", example="blah"),
     *                         @OA\Property(property="valueKey", type="string", example="blah"),
     *                         @OA\Property(property="value", type="string", example="blah"),
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
    public function update(Request $request, Website $website, Item $item)
    {
        $fields = checkApiValidationRules($request, Item::validationRules($item->id), $this);
        $old_values = $item->fieldValues();

        // try {
        // updating
        // $item->update($fields); // not needed

        // add groups
        if ($request->has('groupId'))
            $item->groups()->sync($fields['groupId']);

        // add tags
        if ($request->has('tagIds'))
            $item->tags()->sync($request->input('tagIds'));

        // prepare details
        $details = [];
        foreach ($request->input('values', []) as $detail) {
            $item_detail = ItemDetail::findOrFail($detail['id']);

            if (!$item_detail)
                continue;

            $details[] = [
                'id' => $detail['id'],
                'itemId' => $item->id,
                'value' => $detail['value'],
                'order' => $detail['order'] ?? $item_detail->order,
            ];
        }

        foreach ($request->input('newValues', []) as $detail) {
            $details[] = [
                'itemId' => $item->id,
                'languageId' => $request->input('languageId'),
                'valueType' => $detail['valueType'],
                'key' => $detail['valueKey'],
                'value' => $detail['value'],
                'order' => 99, // $detail['order'],
            ];
        }

        // syncing details
        // foreach ($item->details as $detail) {
        //     foreach ($details as $newDetail) {
        //         if ($detail->languageId == $newDetail['languageId'])
        //             $detail->update($newDetail);
        //         else
        //             $item->details()->create($newDetail);
        //     }
        // }

        // UPDATED
        foreach ($details as $detail) {
            if (array_key_exists('id', $detail))
                $item->details()->find($detail['id'])->update($detail);
            else
                $item->details()->create($detail);
        }

        // if ($request->has('pageGroupId'))
        //     if (!$item->pageGroups->contains($request->has('pageGroupId')))
        //         $item->pageGroups()->attach($request->input('pageGroupId'));

        // storing activity log
        ActivityLog::create([
            'websiteId' => $website->id,
            'entityId' => $item->id,
            'entity' => "items",
            'action' => "update",
            'oldValue' => $old_values,
            'newValue' => $item->fieldValues(),
        ]);

        // responsing
        return $this->successResponse("The item has been updated successfully.", ['item' => $item]);
        // } catch (\Exception $e) {
        //     return exceptionCatchHandler($this, $e);
        // }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/{website}/items/{id}",
     *     tags={"Admin - items"},
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
            $item = Item::where('websiteId', $website->id)->findOrFail($item);
            $item->delete();

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $item->id,
                'entity' => "items",
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
     *     path="/api/admin/{website}/items/remove-detail/{item}/{id}",
     *     tags={"Admin - items"},
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
     *         description="id of an existing item.",
     *         required=true,
     *     ),
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of an existing item detail.",
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
    public function removeDetail(Website $website, Item $item, $id)
    {
        try {
            // deleting
            ItemDetail::findOrFail($id)->delete();

            // storing activity log
            ActivityLog::create([
                'websiteId' => $website->id,
                'entityId' => $item->id,
                'entity' => "items",
                'action' => "delete",
                'oldValue' => $item->fieldValues(),
                'newValue' => null,
            ]);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        return $this->successResponse("the item detail has been deleted successfully.");
    }

    /**
     * @OA\Put(
     *     path="/api/admin/{website}/items/update-order",
     *     tags={"Admin - items"},
     *     summary="auth",
     *
     *     @OA\Parameter(
     *         name="website",
     *         in="path",
     *         description="id of an existing website.",
     *         required=true,
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"id"},
     *
     *                 @OA\Property(property="id", type="array",
     *                     @OA\Items(type="integer")
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Items order updated successfully.",
     *     ),
     *     @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function updateOrder(Request $request, Website $website)
    {
        // validate request
        $fields = checkApiValidationRules($request, Item::updateOrderValidationRules($website->id), $this);

        // loop through all ids
        foreach ($fields['id'] as $key => $id) {
            // get item
            $item = Item::find($id);

            // update item order
            if ($item) {
                $item->order = $key + 1;
                $item->save();
            }
        }

        // responsing
        return $this->successResponse("Items order updated successfully.");
    }
}
