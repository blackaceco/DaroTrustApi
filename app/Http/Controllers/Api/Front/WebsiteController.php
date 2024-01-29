<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Models\Website;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    use ApiResponseTrait;

    /**
     * @OA\Get(
     *     path="/api/websites",
     *     tags={"Front - websites"},
     *     summary="auth",
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
     *                      @OA\Property (property="title", type="string", example="Example Website Title"),
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
    public function index()
    {
        $websites = Website::get();

        return $this->dataResponse($websites, null, false);
    }

    /**
     * @OA\Get(
     *     path="/api/websites/{id}",
     *     tags={"Front - websites"},
     *     summary="auth",
     * 
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id of an existing website.",
     *         required=true,
     *     ),
     * 
     *     @OA\Response(
     *          response=200,
     *          description="",
     *          @OA\JsonContent (
     *              type="object",
     *              @OA\Property (property="id", type="integer", example="7"),
     *              @OA\Property (property="title", type="string", example="Example Website Title"),
     *              @OA\Property (property="slug", type="string", example="example_slug"),
     *              @OA\Property (property="createdAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *              @OA\Property (property="updatedAt", type="string", example="2023-10-03T11:07:10.000000Z"),
     *          )
     *      ),
     *      @OA\Response(response="404", description="invalid id."),
     * )
     */
    public function show($website)
    {
        $website = Website::findOrFail($website);

        return $this->dataResponse($website, null, false, true);
    }
}
