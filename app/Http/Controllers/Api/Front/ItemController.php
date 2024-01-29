<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\BreadScrumbResource;
use App\Http\Resources\ItemResource;
use App\Models\BreadcrumbCategory;
use App\Models\Item;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/items/index/{website_slug}",
     *     tags={"Front - items"},
     *     summary="guest",
     *
     *     @OA\Parameter(
     *         name="website_slug",
     *         in="path",
     *         description="id of existing website.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="limiting getting rows.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="id of an existing item.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="related",
     *         in="query",
     *         description="it is a boolean field , true values: 1 , 'true'. Worked with id parameter only.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="relatedLimit",
     *         in="query",
     *         description="limit of related items. Worked with id and related parameter only.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="languageId",
     *         in="query",
     *         description="id of an existing language.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="query for searching in valueType, key, value of details.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="feature",
     *         in="query",
     *         description="query for searching in the featureTitle.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="pageGroupPage",
     *         in="query",
     *         description="query for searching in the page in page groups.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="groups",
     *         in="query",
     *         description="array of group ids separated by comma like: 1,2,3 .",
     *     ),
     *
     *     @OA\Parameter(
     *         name="tags",
     *         in="query",
     *         description="array of tag ids separated by comma like: 1,2,3 .",
     *     ),
     *
     *     @OA\Parameter(
     *         name="latest",
     *         in="query",
     *         description="it is a boolean field , true values: 1 , 'true'.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="mostView",
     *         in="query",
     *         description="it is a boolean field , true values: 1 , 'true'.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         description="it is a date field.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         description="it is a date field.",
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items (
     *                      type="object",
     *                      @OA\Property (property="id", type="integer", example="7"),
     *                      @OA\Property (property="title", type="string", example="Example Item Title"),
     *                      @OA\Property (property="slug", type="string", example="example_slug"),
     *                      @OA\Property (property="createdAt", type="string", example="2023-10-01T08:20:37.000000Z"),
     *                      @OA\Property (property="updatedAt", type="string", example="2023-10-01T08:20:37.000000Z"),
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="meta",
     *                  type="object",
     *                  @OA\Property (property="current_page", type="integer", example=1),
     *                  @OA\Property (property="last_page", type="integer", example=1),
     *                  @OA\Property (property="has_next_page", type="boolean", example=false),
     *                  @OA\Property (property="has_previous_page", type="boolean", example=false),
     *                  @OA\Property (property="next_page_url", type="string", example=null),
     *                  @OA\Property (property="prev_page_url", type="string", example=null),
     *                  @OA\Property (property="on_first_page", type="boolean", example=true),
     *                  @OA\Property (property="per_page", type="integer", example=10),
     *                  @OA\Property (property="total", type="integer", example=20),
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request, $website_slug)
    {
        $items = Item::with(['children.locale_details', 'locale_details', 'groups.locale_details']);

        return $items = $this->filterItems($request, $items);
    }

    /**
     * @OA\Get(
     *     path="/api/items/{website_slug}/{id}",
     *     tags={"Front - items"},
     *     summary="guest",
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
     *              @OA\Property (property="title", type="string", example="Example Item Title"),
     *              @OA\Property (property="slug", type="string", example="example_slug"),
     *              @OA\Property (property="createdAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *              @OA\Property (property="updatedAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *          )
     *      ),
     *      @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function show(Request $request, $website_slug, $item)
    {
        $item = Item::with(['children.locale_details', 'locale_details', 'groups.locale_details'])
            ->where('websiteId', $request->websiteId)
            ->findOrFail($item);

        // Increase viewing of the requested item.
        $item->increment('view');

        return $this->dataResponse($item, ItemResource::class, false, true);
    }

    /**
     * @OA\Get(
     *     path="/api/items/feature/{website_slug}",
     *     tags={"Front - items"},
     *     summary="guest",
     *
     *     @OA\Parameter(
     *         name="website_slug",
     *         in="path",
     *         description="id of existing website.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="limiting getting rows.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="id of an existing item.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="related",
     *         in="query",
     *         description="it is a boolean field , true values: 1 , 'true'. Worked with id parameter only.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="relatedLimit",
     *         in="query",
     *         description="limit of related items. Worked with id and related parameter only.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="languageId",
     *         in="query",
     *         description="id of an existing language.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="query for searching in valueType, key, value of details.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="feature",
     *         in="query",
     *         description="query for searching in the featureTitle.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="pageGroupPage",
     *         in="query",
     *         description="query for searching in the page in page groups.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="groups",
     *         in="query",
     *         description="array of group ids separated by comma like: 1,2,3 .",
     *     ),
     *
     *     @OA\Parameter(
     *         name="tags",
     *         in="query",
     *         description="array of tag ids separated by comma like: 1,2,3 .",
     *     ),
     *
     *     @OA\Parameter(
     *         name="latest",
     *         in="query",
     *         description="it is a boolean field , true values: 1 , 'true'.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="mostView",
     *         in="query",
     *         description="it is a boolean field , true values: 1 , 'true'.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="fromDate",
     *         in="query",
     *         description="it is a date field.",
     *     ),
     *
     *     @OA\Parameter(
     *         name="toDate",
     *         in="query",
     *         description="it is a date field.",
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items (
     *                      type="object",
     *                      @OA\Property (property="id", type="integer", example="7"),
     *                      @OA\Property (property="title", type="string", example="Example Item Title"),
     *                      @OA\Property (property="slug", type="string", example="example_slug"),
     *                      @OA\Property (property="createdAt", type="string", example="2023-10-01T08:20:37.000000Z"),
     *                      @OA\Property (property="updatedAt", type="string", example="2023-10-01T08:20:37.000000Z"),
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="meta",
     *                  type="object",
     *                  @OA\Property (property="current_page", type="integer", example=1),
     *                  @OA\Property (property="last_page", type="integer", example=1),
     *                  @OA\Property (property="has_next_page", type="boolean", example=false),
     *                  @OA\Property (property="has_previous_page", type="boolean", example=false),
     *                  @OA\Property (property="next_page_url", type="string", example=null),
     *                  @OA\Property (property="prev_page_url", type="string", example=null),
     *                  @OA\Property (property="on_first_page", type="boolean", example=true),
     *                  @OA\Property (property="per_page", type="integer", example=10),
     *                  @OA\Property (property="total", type="integer", example=20),
     *              )
     *          )
     *      )
     * )
     */
    public function feature(Request $request, $website_slug)
    {
        $items = Item::where('websiteId', $request->websiteId)
            ->with(['children.locale_details', 'locale_details', 'groups.locale_details']);

        return $items = $this->filterItems($request, $items);
    }



    /**
     * Separate function which will used by the filterItems function , this is called when id's query parameter
     * is available in the request.
     * This will return a JSON response !
     */
    private function requestedItemId(Request $request, $items)
    {
        // Response variable
        $response = [];

        // find the item if available else throw 404
        $item = $items->findOrFail($request->input('id'));

        // append the item to the response
        $response['data'] = [new ItemResource($item)];


        // check related is requested then collect the group ids and fetch the other items except the requested id.
        if ($request->boolean('related')) {
            $relatedLimit = $request->input('relatedLimit', 2);
            $groupIds = $item->groups->pluck('id')->toArray();

            $related = Item::with(['children.locale_details', 'locale_details', 'groups.locale_details'])
                ->whereHas('groups', function ($query) use ($groupIds, $item) {
                    $query->whereIn('groupId', $groupIds);
                })
                ->where('schemaId', $item->schemaId)
                ->where('id', '!=', $item->id)->take($relatedLimit)->get();

            // append the related item to the response
            $response['related'] = ItemResource::collection($related);
        }


        // ---------  pageGroupPage  ---------
        if ($request->filled('pageGroupPage')) {
            $breadcrumbs = BreadcrumbCategory::where('page', $request->input('pageGroupPage'))->with('breadcrumb_locale')->get();

            if (count($breadcrumbs))
                $response['breadcrumbs'] = BreadScrumbResource::collection($breadcrumbs);
        }


        // Return the JSON response
        return $this->dataResponse($response, null, false, true);
    }


    private function filterItems(Request $request, $items)
    {
        /**
         * Checking the query parameters and prepare the items object
         */

        // ---------  id & related & relatedLimit  ---------
        if ($request->filled('id'))
            return $this->requestedItemId($request, $items);

        // ---------  limit  ---------
        if ($request->filled('limit'))
            $items = $items->take($request->query('limit'));

        // ---------  latest ----------
        if ($request->boolean('latest'))
            $items = $items->latest();

        // ---------  most views  ---------
        if ($request->boolean('mostView'))
            $items = $items->orderBy('view', 'DESC');

        // ---------  languageId  ---------
        if ($request->filled('languageId'))
            $items = $items->whereHas('locale_details', function ($model) use ($request) {
                $model->where('languageId', $request->input('languageId'));
            });

        // ---------  search  ---------
        if ($request->filled('search'))
            $items = $items->whereHas('details', function ($model) use ($request) {
                $query = $request->input('search');
                $model->whereIn('key', ['title', 'summary', 'description']);
                $model->where('value', 'LIKE', "%$query%");
            });

        // ---------  feature  ---------
        if ($request->filled('features')) {
            $features = explode(',', $request->input('features'));

            $items = $items->whereIn('featureTitle', $features);
        }

        // ---------  pageGroupPage  ---------
        if ($request->filled('pageGroupPage'))
            $items = $items->whereHas('pageGroups', function ($model) use ($request) {
                $query = $request->input('pageGroupPage');
                $model->where('page', 'LIKE', "$query");
            });

        // ---------  groups  ---------
        if ($request->filled('groups')) {
            $groupIds = explode(',', $request->input('groups'));

            $items = $items->whereHas('groups', function ($query) use ($groupIds) {
                $query->whereIn('groupId', $groupIds);
            });
        }

        // ---------  tags  ---------
        if ($request->filled('tags')) {
            $tagIds = explode(',', $request->input('tags'));

            $items = $items->whereHas('tags', function ($query) use ($tagIds) {
                $query->whereIn('tagId', $tagIds);
            });
        }

        // ---------  fromDate  ---------
        if ($request->filled('fromDate')) {
            $fromDate = now()->parse($request->input('fromDate'))->toDateString();
            $items = $items->whereDate('createdAt', '>=', $fromDate);
        }

        // ---------  toDate  ---------
        if ($request->filled('toDate')) {
            $toDate = now()->parse($request->input('toDate'))->toDateString();
            $items = $items->whereDate('createdAt', '<=', $toDate);
        }


        /**
         * After preparing the items object getting the items and returning to the response.
         */
        $isPaginated = true;
        $resource = ItemResource::class;

        $isLimited = $request->filled('limit');
        $isLatested = ($request->boolean('latest'));
        $isMostViewed = ($request->boolean('mostView'));

        if ($isLatested || $isMostViewed || $isLimited) {
            $items = ['data' => ItemResource::collection($items->get())];

            if ($request->filled('pageGroupPage')) {
                $breadcrumbs = BreadcrumbCategory::where('page', $request->input('pageGroupPage'))->with('breadcrumb_locale')->get();

                if (count($breadcrumbs))
                    $items['breadcrumbs'] = BreadScrumbResource::collection($breadcrumbs);
            }

            $resource = null;
            $isPaginated = false;
        } else
            $items = $items->paginate($request->input('perPage', 9));


        // return
        return $this->dataResponse($items, $resource, $isPaginated, false);
    }
}
