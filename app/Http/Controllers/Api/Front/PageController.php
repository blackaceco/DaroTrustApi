<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\BreadScrumbResource;
use App\Http\Resources\PageGroupResource;
use App\Models\BreadcrumbCategory;
use App\Models\ContactUsForm;
use App\Models\PageGroup;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class PageController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/pages/{website_slug}/{page}",
     *     tags={"Front - pages"},
     *     summary="guest",
     *
     *     @OA\Parameter(
     *         name="website_slug",
     *         in="path",
     *         description="slug of an existing website.",
     *         required=true,
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="path",
     *         description="page fiel of an existing page_group.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *              )
     *          )
     *      )
     * )
     */
    public function websitePage(Request $request, $website_slug, $page)
    {
        $pageObject = PageGroup::with('items.children.locale_details', 'items.locale_details', 'items.groups.locale_details');

        if($request->query('loadParent')){
            $pageObject = $pageObject->with('items.parents.locale_details');
        }

        $pageObject = $pageObject->where('websiteId', $request->websiteId)
            ->where('page', $page)
            ->get();

        $breadcrumbs = BreadcrumbCategory::where('page', $page)->with('breadcrumb_locale')->get();

        return $this->dataResponse([
            'pageGroups' => PageGroupResource::collection($pageObject),
            'breadcrumbs' => BreadScrumbResource::collection($breadcrumbs),
        ], null, false);
    }

    /**
     * @OA\Get(
     *     path="/api/pages/{website_slug}/{page}/items",
     *     tags={"Front - pages"},
     *     summary="guest",
     *
     *     @OA\Parameter(
     *         name="website_slug",
     *         in="path",
     *         description="slug of an existing website.",
     *         required=true,
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="path",
     *         description="page fiel of an existing page_group.",
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
    public function websitePageFirst(Request $request, $website_slug, $page)
    {
        $page = PageGroup::with('items.children.locale_details', 'items.locale_details', 'items.groups.locale_details')
            ->where('websiteId', $request->websiteId)
            ->where('page', $page)
            ->firstOrFail();

        return $this->dataResponse($page, PageGroupResource::class, false, true);
    }

    /**
     * @OA\Get(
     *     path="/api/pages/{website_slug}/{page}/others",
     *     tags={"Front - pages"},
     *     summary="guest",
     *
     *     @OA\Parameter(
     *         name="website_slug",
     *         in="path",
     *         description="slug of an existing website.",
     *         required=true,
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="path",
     *         description="page fiel of an existing page_group.",
     *         required=true,
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  type="object",
     *              )
     *          )
     *      )
     * )
     */
    public function websitePageOthers(Request $request, $website_slug, $page)
    {
        
        $page = PageGroup::with('items.children.locale_details', 'items.locale_details', 'items.groups.locale_details')
            ->where('websiteId', $request->websiteId)
            ->where('page', $page)
            ->skip(1)
            ->take(PHP_INT_MAX)
            ->get();

        return $this->dataResponse($page, PageGroupResource::class, false);
    }

    /**
     * @OA\Get(
     *     path="/api/languages/{website}",
     *     tags={"Front - pages"},
     *     summary="guest",
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
     *              @OA\Items(
     *                  type="object",
     *                  @OA\Property (property="id", type="integer", example=1),
     *                  @OA\Property (property="title", type="string", example="Arabic"),
     *                  @OA\Property (property="abbreviation", type="string", example="ar"),
     *                  @OA\Property (property="direction", type="string", example="rtl"),
     *                  @OA\Property (property="createdAt", type="string", example="2023-10-05T11:11:43.000000Z"),
     *                  @OA\Property (property="updatedAt", type="string", example="2023-10-05T11:11:43.000000Z"),
     *                  @OA\Property (property="items", type="array", @OA\Items(type="object")),
     *              )
     *          )
     *      )
     * )
     */
    public function websiteLanguages($website_slug)
    {
        $website = Website::whereSlug($website_slug)->firstOrFail();
        $languages = $website->languages;

        return $this->dataResponse(['languages' => $languages], null, false);
    }


    /**
     * @OA\Post(
     *     path="/api/admin/contact-us-form/{website_slug}",
     *     tags={"Front - contact-us-forms"},
     *     summary="guest",
     *
     *     @OA\Parameter(
     *         name="website_slug",
     *         in="path",
     *         description="slug of an existing website.",
     *         required=true,
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"featureTitle", "details"},
     *                 @OA\Property(property="subject", type="string", example="Example Subject"),
     *                 @OA\Property(property="name", type="string", example="Example User Name"),
     *                 @OA\Property(property="email", type="string", example="example@email.com"),
     *                 @OA\Property(property="phone", type="string", example="07701234567"),
     *                 @OA\Property(property="message", type="string", example="Lorem ipsum dolor amit with blah blah blah blah blah blah."),
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
    public function contactUsFormStore(Request $request, $website_slug)
    {
        // validation
        $fields = checkApiValidationRules($request, ContactUsForm::validationRules(), $this);

        // getting the website id
        $fields['websiteId'] = $request->websiteId;

        // storing
        try {
            ContactUsForm::create($fields);
        } catch (\Exception $e) {
            return exceptionCatchHandler($this, $e);
        }

        // responsing
        return $this->successResponse("Your form has been stored successfully.");
    }
}
